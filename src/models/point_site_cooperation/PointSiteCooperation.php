<?php
namespace app\models\point_site_cooperation;

use app\models\exceptions\PointSiteCooperation\NotCooperationException;
use app\models\exceptions\PointSiteCooperation\PointSiteApiNotFoundException;
use app\models\PointSiteApi;
use app\models\PointSiteToken;
use app\models\CashWithdrawal;
use Exception;
use Faker;
use linslin\yii2\curl\Curl;
use Yii;
use yii\base\Model;

class PointSiteCooperation extends Model
{
    /**
     * ポイントサイト連携の認可処理
     *
     * 認証用のコードを使用し認可リクエストを要求し、
     * アクセストークンを取得する
     *
     * @param string $code
     * @param int $polletUserId
     * @param string $pointSiteCode
     * @return bool
     */
    public static function saveAuthorization(string $code, int $polletUserId, string $pointSiteCode) : bool
    {
        // TODO: トークンリクエスト with $code
        $token = Faker\Factory::create()->regexify('[A-Z0-9._%+-]{10}');

        $pointSiteToken = new PointSiteToken();
        $pointSiteToken->pollet_user_id = $polletUserId;
        $pointSiteToken->point_site_code = $pointSiteCode;
        $pointSiteToken->token = $token;

        return $pointSiteToken->save();
    }

    /**
     * 有効なポイント残高を円換算で取得する
     *
     * @param string $chargeSourceCode
     * @param int $polletUserId
     * @return int
     */
    public static function fetchValidPointAsCash(string $chargeSourceCode, int $polletUserId)
    {
        // TODO: 実装

        return 5000;
    }

    /**
     * 交換リクエストの実行
     *
     * @param string $chargeSourceCode
     * @param int $value
     * @param int $polletUserId
     * @return CashWithdrawal|bool
     * @throws Exception
     */
    public static function exchange(string $chargeSourceCode, int $value, int $polletUserId)
    {
        $cashWithdrawal = new CashWithdrawal();
        $cashWithdrawal->add($chargeSourceCode, $value);

        $exchangeApi = self::getExchangeApi($chargeSourceCode);

        $apiWithParam = $exchangeApi.'?='.http_build_query([
                'charge_id'    => $cashWithdrawal->id,
                'charge_value' => $value,
            ]);
        $header = [
            //HTTPヘッダ・インジェクション回避のためエンコード
            'Authorization: Bearer '.rawurlencode(self::getToken($chargeSourceCode, $polletUserId)),
            'Content-Type: application/json',
        ];
        $curl = self::getCurlInstance();
        $curl->setOption(CURLOPT_HTTPHEADER, $header)->get($apiWithParam);

        //今は通信先がないので無条件で200返す
        $curl->responseCode = 200;
        if ($curl->responseCode === 200) {
            return $cashWithdrawal;
        }
        //fixme ログの出力内容 本番つなぎ込みの際にきめる
        Yii::error($curl->response);

        return false;
    }

    /**
     * トークン取得
     *
     * @param string $chargeSourceCode
     * @param int $polletUserId
     * @return string
     */
    public static function getToken(string $chargeSourceCode, int $polletUserId)
    {
        try {
            return PointSiteToken::find()->where([
                'pollet_user_id'  => $polletUserId,
                'point_site_code' => $chargeSourceCode,
            ])->one()->token;
        } catch (Exception $e) {
            throw new NotCooperationException;
        }
    }

    /**
     * 交換APIのURLを取得
     *
     * @param string $chargeSourceCode
     * @return string
     */
    public static function getExchangeApi(string $chargeSourceCode)
    {
        try {
            return PointSiteApi::find()->where([
                'api_name'          => PointSiteApi::API_NAME_EXCHANGE,
                'point_site_code'   => $chargeSourceCode,
                'publishing_status' => PointSiteApi::PUBLISHING_STATUS_PUBLIC,
            ])->one()->url;
        } catch (Exception $e) {
            throw new PointSiteApiNotFoundException;
        }
    }

    /**
     * CurlをモックにしてAPI呼び出しをテストするためのメソッド
     * FIXME: static メソッドのモックは作れないので`Yii::$app->set()`で無理やり置き換える必要がある
     *
     * @return Curl
     */
    private static function getCurlInstance()
    {
        return Yii::$app->get('curl');
    }
}
