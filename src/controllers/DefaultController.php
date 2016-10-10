<?php

namespace app\controllers;

use app\models\forms\SignInForm;
use app\models\PolletUser;
use Yii;
use yii\filters\AccessControl;
use yii\web\HttpException;

/**
 * Class DefaultController
 * @package app\controllers
 */
class DefaultController extends CommonController
{
    const CIRCLER_GRAPH_DENOMINATOR = 999999; // 5000;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'login'],
                        'allow'   => true,
                        'roles'   => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 発行IDでのログインとページ振り分けを行う
     * @throws HttpException
     */
    public function actionIndex()
    {
        // ヘッダーからユーザーを取得
        $user = PolletUser::findByCodeSecret(Yii::$app->request->headers->get(self::HEADER_POLLET_ID));
        if (!$user) {
            throw new HttpException(406);
        } elseif (!Yii::$app->user->login($user)) {
            throw new HttpException(401);
        }
        $this->authorizedUser = $user;

        // TODO: ステータスに応じたページへリダイレクトする
        switch ($this->authorizedUser->registration_status) {
            case PolletUser::STATUS_NEW_USER:
                $nextPage = '/tutorial';
                break;
            case PolletUser::STATUS_SIGN_OUT:
                $nextPage = '/auth/sign-in';
                break;
            case PolletUser::STATUS_ACTIVATED:
                $signInForm = new SignInForm();
                $signInForm->scenario = SignInForm::SCENARIO_AUTO;
                if ($signInForm->authenticate($user)) {
                    $nextPage = '/top';
                } else {
                    $nextPage = '/auth/sign-in';
                }
                break;
            default:
                $nextPage = '/top';
        }

        return $this->redirect($nextPage);
    }

    /**
     * 1. チュートリアル
     * @return string
     * @throws HttpException
     */
    public function actionTutorial()
    {
        return $this->render('tutorial');
    }

    /**
     * 15. トップ画面
     * @return string
     */
    public function actionTop()
    {
        $this->redirectIfNoneChargedValue();

        return $this->render('top', [
            'percentage' => $this->getPercentageOfCirclerGraph($this->authorizedUser->myChargedValue),
        ]);
    }

    /**
     * 23. 利用ガイド
     * @return string
     */
    public function actionGuide()
    {
        return $this->render('guide');
    }

    /**
     * 24-1. 初めてガイド
     * @return string
     */
    public function actionFirstGuide()
    {
        return $this->render('first-guide');
    }

    /**
     * 24-2. 詳細ガイド(ヘルプ)
     * @return string
     */
    public function actionHelp()
    {
        return $this->render('help');
    }

    /**
     * 24-3. 利用規約
     * @return string
     */
    public function actionTerms()
    {
        return $this->render('terms');
    }

    /**
     * 25. 設定画面
     * @return string
     */
    public function actionSetting()
    {
        return $this->render('setting');
    }

    /**
     * 円グラフのパーセンテージになる値を取得する
     * @param integer $númeràtor 分子になる値
     * @return float
     */
    private function getPercentageOfCirclerGraph($númeràtor)
    {
        $ret = $númeràtor / self::CIRCLER_GRAPH_DENOMINATOR;
        if ($ret > 1.0) {
            return 1.0;
        }
        return floatval($ret);
    }
}
