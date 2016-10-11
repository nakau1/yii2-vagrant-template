<?php

namespace app\modules\api\controllers;

use app\modules\api\models\Initialize;
use Yii;

/**
 * Class DefaultController
 * @package app\modules\api\controllers
 */
class DefaultController extends CommonController
{
    /**
     * アプリ起動時初期化
     * @return array
     */
    public function actionInitialize()
    {
        $params = [
            'polletId'     => Yii::$app->request->post('pollet_id'),
            'uuid'         => Yii::$app->request->post('uuid'),
            'platform'     => Yii::$app->request->post('platform'),
            'device_token' => Yii::$app->request->post('device_token'),
        ];

        $model = new Initialize();

        if (!$model->load($params) || !$model->validate()) {
            // リクエスト不正
            return $this->generateErrorResponse('不正なリクエストです。', $model->getErrors(), 400);
        }

        $polletUser = $model->getUser();

        try {
            if ($polletUser) {
                // 登録済み既存ユーザー
                if (!$model->updateDeviceToken()) {
                    throw new \Exception('トークンの保存に失敗しました。');
                }
                $polletUser->refresh();
            } else {
                // 新規ユーザー作成
                $polletUser = $model->createPolletUser();
                // ユーザー作成失敗
                if (!$polletUser) {
                    throw new \Exception('新規ユーザーの作成に失敗しました。');
                }
            }
        } catch (\Exception $e) {
            return $this->generateErrorResponse($e->getMessage(), $model->getErrors(), 500);
        }

        $this->message = self::STATUS_OK;

        return [
            'pollet_id' => $polletUser->user_code_secret,
        ];
    }

    /**
     * 共通エラーメソッド
     */
    public function actionError()
    {
        // 実際のレスポンスは config/api.php に設定
    }
}
