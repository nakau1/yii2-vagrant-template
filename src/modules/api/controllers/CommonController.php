<?php
namespace app\modules\api\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class CommonController extends Controller
{
    const STATUS_KEY  = 'status';
    const RESULT_KEY  = 'result';
    const ERRORS_KEY  = 'errors';
    const MESSAGE_KEY = 'message';

    /**
     * ステータスコード
     * @var int
     */
    protected $code = 200;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    /**
     * @inheritdoc
     */
    public function afterAction($action, $result)
    {
        return self::generateResult($this->code, $result);
    }

    /**
     * エラーレスポンスを生成して返す
     * @param string|array $errorMessage エラーメッセージ
     * @param int $code ステータスコード
     * @return array 空配列
     */
    protected function error($errorMessage, $code = 500)
    {
        if (is_string($errorMessage)) {
            $errorMessage = [$errorMessage];
        }

        $result = [];
        foreach ($errorMessage as $item) {
            $result[self::ERRORS_KEY][] = $item;
        }

        Yii::$app->response->setStatusCode($code);
        return $result;
    }

    /**
     * API結果を配列で返す
     * @param int $statusCode ステータスコード
     * @param array $result 結果データ
     * @return array API結果
     */
    public static function generateResult($statusCode, $result)
    {
        return [
            self::STATUS_KEY => $statusCode,
            self::RESULT_KEY => $result,
        ];
    }
}
