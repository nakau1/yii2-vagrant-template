<?php

namespace app\models\forms;

use app\models\cedyna_my_pages\CedynaMyPageWithCache;
use app\models\PolletUser;
use Yii;
use yii\base\Model;
use yii\web\HttpException;

/**
 * ログイン(カード認証)フォーム用モデル
 * @package app\models\forms
 */
class SignInForm extends Model
{
    const ACTIVATE_DURATION = 60*60*24 * 30; // 30日間

    const SCENARIO_ID_WITH_PW = 'required-cedynaID-and-password';
    const SCENARIO_ID_ONLY    = 'only-cedynaID';
    const SCENARIO_AUTO       = 'auto-login';

    public $cedyna_id;
    public $password;

    /**
     * 認証を実行する
     * @param $user PolletUser ユーザ
     * @return bool
     */
    public function authenticate($user)
    {
        if (!$this->validate()) {
            return false;
        }

        switch ($this->scenario) {
            case self::SCENARIO_ID_WITH_PW:
                $boval = $this->authenticateOnCedynaIdWithPasswordScenario($user);
                break;
            case self::SCENARIO_ID_ONLY:
                $boval = $this->authenticateOnCedynaIdOnlyScenario($user);
                break;
            case self::SCENARIO_AUTO:
                $boval = $this->authenticateAuto($user);
                break;
            default: return false; break; // dead-code
        }

        return $boval;
    }

    /**
     * セディナIDとパスワード入力をするシナリオでの認証処理を行う
     * @param $user PolletUser ユーザ
     * @return bool 成功/失敗 失敗時はエラーを追加した状態になる
     * @throws HttpException
     */
    private function authenticateOnCedynaIdWithPasswordScenario($user)
    {
        // セディナの認証実行
        if (!CedynaMyPageWithCache::getInstance()->login($this->cedyna_id, $this->password)) {
            $this->addAuthorizeError();
            return false;
        }

        // 入力されたセディナIDに紐付くユーザを検索する
        $searchedUser = PolletUser::find()->andWhere([
            PolletUser::tableName() .'.cedyna_id' => $this->cedyna_id,
        ])->one();
        if (!$searchedUser) {
            $this->addAuthorizeError();
            return false;
        }

        // 入力されたパスワードと、抽出されたレコードのパスワードが異なるときは
        // 入力された方が認証可能なパスワードなので書き換える
        if ($this->password !== $searchedUser->password) {
            if (!$searchedUser->updatePassword($this->password)) {
                throw new HttpException(500);
            }
        }

        // 抽出されたユーザのトークンと、セッション上のユーザのトークンが違う場合
        if ($user->user_code_secret !== $searchedUser->user_code_secret) {
            $trans = Yii::$app->db->beginTransaction();
            try {

                // 重複が発生しないように先に元レコードを削除
                $userCodeSecret = $user->user_code_secret;
                if ($user->delete() === false) {
                    throw new \Exception('failed delete original user');
                }

                $searchedUser->user_code_secret = $userCodeSecret;
                if (!$searchedUser->save()) {
                    throw new \Exception('failed update searched user');
                }

                $trans->commit();
            } catch (\Exception $e) {
                $trans->rollBack();
                throw new HttpException($e->getMessage());
            }
            Yii::$app->user->login($searchedUser);
        }

        return true;
    }

    /**
     * セディナIDのみを入力をするシナリオでの認証処理を行う
     * @param $user PolletUser ユーザ
     * @return bool 成功/失敗 失敗時はエラーを追加した状態になる
     */
    private function authenticateOnCedynaIdOnlyScenario($user)
    {
        // 入力されたセディナIDに紐付くユーザを検索してパスワードを取得する
        $searchedUser = PolletUser::find()->andWhere([
            PolletUser::tableName() .'.user_code_secret' => $user->user_code_secret,
            PolletUser::tableName() .'.cedyna_id'        => $this->cedyna_id,
        ])->one();
        if (!$searchedUser || !$searchedUser->password) {
            $this->addAuthorizeError();
            return false;
        }
        
        // セディナの認証実行
        if (!CedynaMyPageWithCache::getInstance()->login($this->cedyna_id, $searchedUser->password)) {
            $this->addAuthorizeError();
            return false;
        }

        return true;
    }

    /**
     * 自動ログインシナリオでの認証処理を行う
     * @param $user PolletUser ユーザ
     * @return bool 成功/失敗 失敗時はエラーを追加した状態になる
     */
    private function authenticateAuto($user)
    {
        // 入力されたセディナIDに紐付くユーザを検索してパスワードを取得する
        $searchedUser = PolletUser::find()->andWhere([
            PolletUser::tableName() .'.user_code_secret' => $user->user_code_secret,
        ])->one();
        if (!$searchedUser || !$searchedUser->cedyna_id || !$searchedUser->password) {
            $this->addAuthorizeError();
            return false;
        }

        // セディナの認証実行
        if (!CedynaMyPageWithCache::getInstance()->login($searchedUser->cedyna_id, $searchedUser->password)) {
            $this->addAuthorizeError();
            return false;
        }

        return true;
    }

    /**
     * 認証エラーを追加する
     */
    private function addAuthorizeError()
    {
        $this->addError('cedyna_id', '入力された情報では認証できませんでした');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $ret = [
            ['cedyna_id', 'required', 'on' => [self::SCENARIO_ID_ONLY, self::SCENARIO_ID_WITH_PW]],
            ['cedyna_id', 'match', 'pattern' => '/^[0-9]{16}$/', 'message' => 'セディナIDは16桁の数字で入力してください', 'on' => [self::SCENARIO_ID_ONLY, self::SCENARIO_ID_WITH_PW]],
            ['password', 'required', 'on' => [self::SCENARIO_ID_WITH_PW]],
            ['password', 'string', 'max' => 256, 'on' => [self::SCENARIO_ID_WITH_PW]],
        ];

        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cedyna_id' => 'セディナID',
            'password'  => 'パスワード',
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            self::SCENARIO_ID_ONLY    => ['cedyna_id'],
            self::SCENARIO_ID_WITH_PW => ['cedyna_id', 'password'],
            self::SCENARIO_AUTO       => [],
        ]);
    }

    /**
     * パスワード入力が必要かどうかを返す
     * @return bool
     */
    public function isNecessityInputPassword()
    {
        return $this->scenario == self::SCENARIO_ID_WITH_PW;
    }
}
