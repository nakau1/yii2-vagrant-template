<?php

use app\modules\api\controllers\CommonController;
use yii\base\Event;
use yii\web\Response;

return [
    'id'                  => 'neroblu-api',
    'controllerNamespace' => 'app\modules\api\controllers',
    'components'          => [
        'response'     => [
            'on beforeSend' => function (Event $event) {
                /** @var Response $response */
                $response = $event->sender;

                if ($response->data !== null && !$response->isSuccessful) {

                    $result = [];
                    if (isset($response->data[CommonController::RESULT_KEY])) {
                        $result = $response->data[CommonController::RESULT_KEY];
                    } else if (isset($response->data[CommonController::MESSAGE_KEY])) {
                        $result = [CommonController::ERRORS_KEY => [$response->data[CommonController::MESSAGE_KEY]]];
                    }

                    $response->data = CommonController::generateResult(
                        $response->getStatusCode(),
                        $result
                    );
                }
            },
        ],
    ],
];