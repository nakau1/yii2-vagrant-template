<?php

namespace app\models;

use app\models\cedyna_my_pages\CedynaMyPageWithCache;
use app\models\queries\PolletUserQuery;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "pollet_user".
 *
 * @property integer                      $id
 * @property string                       $user_code_secret
 * @property string                       $cedyna_id
 * @property string                       $password
 * @property string                       $total_point          polletポイント所持数
 * @property string                       $mail_address
 * @property string                       $registration_status
 * @property integer                      $unread_notifications プッシュ通知未読数
 * @property integer                      $updated_at
 * @property integer                      $created_at
 *
 * @property CardValueCache               $cardValueCache
 * @property ChargeRequestHistory[]       $chargeRequestHistories
 * @property Inquiry[]                    $inquiries
 * @property MonthlyTradingHistoryCache[] $monthlyTradingHistoryCaches
 * @property PointSiteToken[]             $pointSiteTokens
 * @property PointSite[]                  $pointSites
 * @property PolletPointHistory[]         $polletPointHistories
 * @property PushInformationOpening[]     $pushInformationOpenings
 * @property PushNotificationToken[]      $pushNotificationTokens
 * @property TradingHistoryCache[]        $tradingHistoryCaches
 *
 * @property int|boolean                  $myChargedValue ユーザーの状態に応じたチャージ残高
 * @property boolean                      $hasUnreadInformation ユーザに未読お知らせがあるかどうか
 */
class PolletUser extends ActiveRecord implements IdentityInterface
{
    const STATUS_NEW_USER           = 'new_user';           // 新規ユーザ
    const STATUS_SITE_AUTHENTICATED = 'site_authenticated'; // 初回サイト認証完了済
    const STATUS_CHARGE_REQUESTED   = 'charge_requested';   // 初回チャージ申請完了済
    const STATUS_WAITING_ISSUE      = 'waiting_issue';      // 発番待ち
    const STATUS_ISSUED             = 'issued';             // 発番済
    const STATUS_ACTIVATED          = 'activated';          // アクティベート完了
    const STATUS_SIGN_OUT           = 'sign-out';           // ログアウト済み(アクティベートはされている)
    const STATUS_REMOVED            = 'removed';            // 削除済み

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pollet_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_code_secret', 'registration_status'], 'required'],
            [['cedyna_id', 'unread_notifications', 'updated_at', 'created_at'], 'integer'],
            [['cedyna_id', 'unread_notifications'], 'match', 'pattern' => '/\A[0-9]{1,16}\z/u'],
            [['total_point'], 'number'],
            [['user_code_secret'], 'string', 'max' => 64],
            [['password', 'mail_address'], 'string', 'max' => 256],
            [['registration_status'], 'string', 'max' => 35],
            [['user_code_secret'], 'unique'],
            [['cedyna_id'], 'unique'],
            [['cedyna_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                   => 'ID',
            'user_code_secret'     => 'ユーザコード',
            'cedyna_id'            => 'セディナID',
            'password'             => 'パスワード',
            'total_point'          => '合計ポイント',
            'mail_address'         => 'メールアドレス',
            'registration_status'  => 'ステータス(登録状態)',
            'unread_notifications' => '通知未読数',
            'updated_at'           => '更新日時',
            'created_at'           => '作成日時',
        ];
    }

    // ===============================================================
    // relations
    // ===============================================================

    /**
     * @return ActiveQuery
     */
    public function getCardValueCache()
    {
        return $this->hasOne(CardValueCache::className(), ['pollet_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getChargeRequestHistories()
    {
        return $this->hasMany(ChargeRequestHistory::className(), ['pollet_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getInquiries()
    {
        return $this->hasMany(Inquiry::className(), ['pollet_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMonthlyTradingHistoryCaches()
    {
        return $this->hasMany(MonthlyTradingHistoryCache::className(), ['pollet_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPointSiteTokens()
    {
        return $this->hasMany(PointSiteToken::className(), ['pollet_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPointSites()
    {
        return $this->hasMany(PointSite::className(), ['point_site_code' => 'point_site_code'])->viaTable('point_site_token',
            ['pollet_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPolletPointHistories()
    {
        return $this->hasMany(PolletPointHistory::className(), ['pollet_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPushInformationOpenings()
    {
        return $this->hasMany(PushInformationOpening::className(), ['pollet_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPushNotificationTokens()
    {
        return $this->hasMany(PushNotificationToken::className(), ['pollet_user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTradingHistoryCaches()
    {
        return $this->hasMany(TradingHistoryCache::className(), ['pollet_user_id' => 'id']);
    }

    // ===============================================================
    // find
    // ===============================================================

    /**
     * @inheritdoc
     * @return PolletUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return (new PolletUserQuery(get_called_class()))->andWhere([
            '<>',
            self::tableName() . '.registration_status',
            self::STATUS_REMOVED,
        ]);
    }

    /**
     * セディナIDからユーザを検索して返す
     * @param $cedynaId integer セディナID
     * @return null|PolletUser ユーザ
     */
    public static function findByCedynaId($cedynaId)
    {
        return self::findOne([
            'cedyna_id' => $cedynaId,
            //'registration_status' => '1', //TODO:ステータス条件は必要と思われるが、今は外している
        ]);
    }

    /**
     * 提携サイトCodeから自身の持つ提携サイトを返す
     * @param $pointSiteCode string 提携サイトCode
     * @return PointSite|null 提携サイト。存在しない、もしくは自身が持っていない場合はnullを返す
     */
    public function findMyPointSite($pointSiteCode)
    {
        foreach ($this->pointSiteTokens as $pointSiteToken) {
            if ($pointSiteToken->pointSite->point_site_code == $pointSiteCode) {
                return $pointSiteToken->pointSite;
            }
        }
        return null;
    }

    /**
     * ポイントサイトにチャージを申請するAPI情報を取得する
     * @param $pointSiteID
     * @return PointSiteApi|null
     */
    public function findChargeRequestAPI($pointSiteID)
    {
        $pointSite = $this->findMyPointSite($pointSiteID);
        if (!$pointSite) {
            return null;
        }
        foreach ($pointSite->apis as $api) {
            if ($api->api_name == "request") { //TODO: どのAPI名で取得するかは仕様未定
                return $api;
            }
        }
        return null;
    }

    /**
     * トークンからユーザーを検索する
     * @param $secret
     * @return PolletUser|null
     */
    public static function findByCodeSecret($secret)
    {
        if (!$secret) {
            return null;
        }

        $user = self::find()->where([
            'user_code_secret' => $secret,
        ])->one();

        return $user;
    }

    // ===============================================================
    // save
    // ===============================================================

    /**
     * ユーザのステータスを更新する
     * @param $status string 変更後のステータス
     * @return bool 成功/失敗
     */
    public function updateStatus($status)
    {
        $trans = Yii::$app->db->beginTransaction();
        try {
            $this->registration_status = $status;

            if (!$this->save()) {
                throw new \Exception('failed change status.');
            }
            $trans->commit();
            return true;
        } catch (\Exception $e) {
            $trans->rollBack();
            return false;
        }
    }

    /**
     * ユーザのトークンを更新する
     * @param $newUserCodeSecret string 新しいトークン
     * @return bool 成功/失敗
     */
    public function updateUserCodeSecret($newUserCodeSecret)
    {
        $trans = Yii::$app->db->beginTransaction();
        try {
            $this->user_code_secret = $newUserCodeSecret;

            if (!$this->save()) {
                throw new \Exception('failed update user-token-secret.');
            }
            $trans->commit();
            return true;
        } catch (\Exception $e) {
            $trans->rollBack();
            return false;
        }
    }

    /**
     * ユーザのパスワードを更新する
     * @param $newPassword string 新しいパスワード
     * @return bool 成功/失敗
     */
    public function updatePassword($newPassword)
    {
        $trans = Yii::$app->db->beginTransaction();
        try {
            $this->password = $newPassword;

            if (!$this->save()) {
                throw new \Exception('failed update password.');
            }
            $trans->commit();
            return true;
        } catch (\Exception $e) {
            $trans->rollBack();
            return false;
        }
    }

    /**
     * ユーザを削除する
     * @return bool 成功/失敗
     */
    public function requestRemove()
    {
        $trans = Yii::$app->db->beginTransaction();
        try {
            $this->registration_status = self::STATUS_REMOVED;

            if (!$this->save()) {
                throw new \Exception('failed remove user.');
            }
            $trans->commit();
            return true;
        } catch (\Exception $e) {
            $trans->rollBack();
            return false;
        }
    }

    // ===============================================================
    // alias method for status
    // ===============================================================

    /**
     * 新規ユーザかどうかを返す
     * @return bool
     */
    public function isNewUser()
    {
        return $this->registration_status === self::STATUS_NEW_USER;
    }

    /**
     * 初回サイト認証完了済のユーザかどうかを返す
     * @return bool
     */
    public function isSiteAuthenticated()
    {
        return $this->registration_status === self::STATUS_SITE_AUTHENTICATED;
    }

    /**
     *  初回チャージ申請完了済のユーザかどうかを返す
     * @return bool
     */
    public function isChargeRequested()
    {
        return $this->registration_status === self::STATUS_CHARGE_REQUESTED;
    }

    /**
     * 発番待ちのユーザかどうかを返す
     * @return bool
     */
    public function isWaitingIssue()
    {
        return $this->registration_status === self::STATUS_WAITING_ISSUE;
    }

    /**
     * 発番済のユーザかどうかを返す
     * @return bool
     */
    public function isIssued()
    {
        return $this->registration_status === self::STATUS_ISSUED;
    }

    /**
     * アクティベート完了状態のユーザかどうかを返す
     * @return bool
     */
    public function isActivatedUser()
    {
        return $this->registration_status === self::STATUS_ACTIVATED;
    }

    /**
     * ログアウト状態のユーザかどうかを返す
     * @return bool
     */
    public function isSignOut()
    {
        return $this->registration_status === self::STATUS_SIGN_OUT;
    }

    // ===============================================================
    // dynamic getter properties
    // ===============================================================

    /**
     * ユーザに未読お知らせがあるかどうかを返す
     * @return bool
     */
    public function getHasUnreadInformation()
    {
        return (Information::find()->joinOpening(true)->published()->count() > 0);
    }

    /**
     * ユーザーの状態に応じたチャージ残高を返す
     * @return bool|int
     */
    public function getMyChargedValue()
    {
        // 新規ユーザー
        if ($this->isNewUser()) {
            // あるはず無いので0
            return 0;
        }

        // 初回申請後でアクティベート前
        if (in_array($this->registration_status, [
            self::STATUS_CHARGE_REQUESTED,
            self::STATUS_WAITING_ISSUE,
            self::STATUS_ISSUED,
        ])) {
            // 初回申請から取得(このステータス時には1レコードしか無い)
            /** @var ChargeRequestHistory $charge */
            $charge = ChargeRequestHistory::find()->where([
                'pollet_user_id'    => $this->id,
                'processing_status' => [
                    ChargeRequestHistory::STATUS_UNPROCESSED_FIRST_CHARGE,
                    ChargeRequestHistory::STATUS_READY,
                    ChargeRequestHistory::STATUS_IS_MAKING_PAYMENT_FILE,
                ],
            ])->one();

            if (!$charge) {
                return 0;
            }

            return $charge->charge_value;
        }

        if (in_array($this->registration_status, [
            self::STATUS_ACTIVATED,
        ])) {
            $cedynaWithCache = CedynaMyPageWithCache::getInstance();
            if (!$cedynaWithCache->login($this->cedyna_id, $this->password)) {
                // ログイン失敗
                return false;
            }

            return $cedynaWithCache->cardValue();
        }

        return 0;
    }

    // ===============================================================
    // implementation for IdentityInterface
    // ===============================================================

    /**
     * @param int|string $id
     * @return PolletUser|array|null
     */
    public static function findIdentity($id)
    {
        return self::find()->andWhere([
            self::tableName() . '.id' => $id,
        ])->one();
    }

    /**
     * @inheritdoc
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->id == $authKey;
    }
}