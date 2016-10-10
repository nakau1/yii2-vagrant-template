<?php

namespace app\controllers;

use app\models\forms\DemoCedynaIdForm;
use app\models\PointSite;
use app\models\PolletUser;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class DemoController
 *
 * 開発の便宜上作成したデモ画面です
 *
 * @package app\controllers
 */
class DemoController extends Controller
{
    /**
     * 外部認証
     *
     * 本来は外部サイトへ遷移します
     *
     * @param $auth_url string 認証URL
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionAuthenticate($auth_url)
    {
        if (Yii::$app->request->isPost) {
            $site = PointSite::find()->where([
                'auth_url' => $auth_url,
            ])->one();
            if (!$site) {
                throw new BadRequestHttpException('サイトがありません');
            }

            $this->redirect(['charge/index',
                'token'     => Yii::$app->security->generateRandomString(40), // 仮トークン
                'site_code' => $site->point_site_code,
            ]);
        }
        return $this->render('authenticate');
    }

    /**
     * 開発デモ用発番
     *
     * ログイン中のユーザに任意のセディナIDを発番して「発番済」ステータスにすることができます
     *
     * @return string|Response
     */
    public function actionIssue()
    {
        $formModel = new DemoCedynaIdForm();
        $formModel->scenario = DemoCedynaIdForm::SCENARIO_DEMO;

        /* @var $user PolletUser */
        $user = Yii::$app->user->identity;
        if ($formModel->load(Yii::$app->request->post()) && $formModel->issue($user)) {
            return $this->goHome();
        }

        return $this->render('issue', [
            'formModel' => $formModel,
        ]);
    }

    /**
     * 開発デモ用セディナマイページメールアドレス入力画面
     *
     * @return string
     */
    public function actionCedynaSendEmail()
    {
        return $this->render('cedyna_send_email');
    }

    /**
     * 開発デモ用セディナマイページメールアドレス入力完了画面
     * メールアドレスが空の場合、完了画面の代わりに入力画面をレンダリングする
     *
     * @return string
     */
    public function actionCedynaSendEmailComplete()
    {
        $params = Yii::$app->request->get();
        if (!empty($params['email1']) && !empty($params['email2']) && $params['email1'] === $params['email2']) {
            return $this->render('cedyna_send_email_complete');
        } else {
            return $this->render('cedyna_send_email', [
                'error' => 'メールアドレスを正しく入力してください',
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function render($view, $params = [])
    {
        $this->layout = 'simple';
        return parent::render($view, $params);
    }
}
