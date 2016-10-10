<?php

namespace app\controllers;

use app\models\PolletUser;
use app\views\View;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Class CommonController
 * @package app\controllers
 */
class CommonController extends Controller
{
    const HEADER_POLLET_ID   = 'X-Pollet-Id';
    const SESSION_FAILED_KEY = 'failed-sign-in';

    /** @var PolletUser 認証されているユーザ */
    protected $authorizedUser;


    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /** @inheritdoc */
    public function init()
    {
        parent::init();
        $this->authorizedUser = Yii::$app->user->identity;
    }

    /** @inheritdoc */
    public function render($view, $params = [])
    {
        /** @var $viewObject View */
        $viewObject = $this->view;
        $viewObject->user = $this->authorizedUser;

        return parent::render($view, array_merge($params, $params));
    }

    /**
     * 認証中のユーザのChargeValueが、
     * セディナ認証失敗で取得できない場合にログイン画面へリダイレクトさせる
     */
    protected function redirectIfNoneChargedValue()
    {
        if ($this->authorizedUser->myChargedValue === false) {
            // セディナ認証失敗はログインへリダイレクト
            Yii::$app->session->set(self::SESSION_FAILED_KEY, '1');
            $this->redirect('/auth/sign-in');
        }
    }
}