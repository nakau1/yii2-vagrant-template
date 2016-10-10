<?php

namespace app\controllers;

use app\models\forms\InquiryForm;
use Yii;

/**
 * 問い合わせ画面コントローラ
 * @package app\controllers
 */
class InquiryController extends CommonController
{
    /**
     * 24-4. お問い合わせフォーム
     * @return string
     */
    public function actionIndex()
    {
        $formModel = new InquiryForm();

        if ($formModel->load(Yii::$app->request->post()) && $formModel->contact($this->authorizedUser->id)) {
            Yii::$app->session->setFlash('contactFormSubmitted');
            return $this->render('complete');
        }
        return $this->render('index', [
            'formModel' => $formModel,
        ]);
    }
}
