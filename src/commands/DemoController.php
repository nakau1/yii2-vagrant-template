<?php
namespace app\commands;

use app\models\cedyna_my_pages\CedynaMyPage;
use app\models\cedyna_my_pages\CedynaMyPageWithCache;
use app\models\point_site_cooperation\PointSiteCooperation;
use app\models\PolletUser;
use Yii;
use yii\console\Controller;

/**
 * デモで動作確認するためのコマンドを提供する
 * TODO: 本番で実行できないようにする
 *
 * Class DemoController
 * @package app\commands
 */
class DemoController extends Controller
{
    /**
     * @param string $cedynaId
     * @param string $rawPassword
     */
    public function actionCedynaMyPageLogin(string $cedynaId, string $rawPassword)
    {
        $myPage = CedynaMyPageWithCache::getInstance();
        $loggedIn = $myPage->login($cedynaId, $rawPassword);

        echo $loggedIn ? 'true' : 'false', PHP_EOL;
    }

    /**
     * @param int $polletUserId
     * @return bool
     */
    public function actionCedynaMyPageCardValue(int $polletUserId)
    {
        /** @var PolletUser $user */
        $user = PolletUser::findOne($polletUserId);
        if (empty($user)) {
            echo 'ユーザーが存在しません', PHP_EOL;

            return false;
        }
        if (empty($user->cedyna_id) || empty($user->password)) {
            echo 'ログインできません', PHP_EOL;

            return false;
        }

        $myPage = CedynaMyPageWithCache::getInstance();
        $myPage->login($user->cedyna_id, $user->password);
        $cardValue = $myPage->cardValue();

        echo $cardValue.PHP_EOL;

        return true;
    }

    /**
     * @param int $polletUserId
     * @param string $yymm
     * @return bool
     */
    public function actionCedynaMyPageTradingHistory(int $polletUserId, string $yymm)
    {
        /** @var PolletUser $user */
        $user = PolletUser::findOne($polletUserId);
        if (empty($user)) {
            echo 'ユーザーが存在しません', PHP_EOL;

            return false;
        }
        if (empty($user->cedyna_id) || empty($user->password)) {
            echo 'ログインできません', PHP_EOL;

            return false;
        }

        /** @var CedynaMyPageWithCache $myPage */
        $myPage = CedynaMyPageWithCache::getInstance();
        $myPage->login($user->cedyna_id, $user->password);
        $histories = $myPage->tradingHistories($yymm);
        foreach ($histories as $history) {
            echo json_encode($history->toArray(), JSON_PRETTY_PRINT).PHP_EOL;
        }

        return true;
    }

    /**
     * カード発行申し込みリンクをメールで送信する
     *
     * @param string $email
     */
    public function actionCedynaMyPageSendIssuingFormLink(string $email)
    {
        $myPage = CedynaMyPage::getInstance();
        $isSuccess = $myPage->sendIssuingFormLink($email);

        echo $isSuccess ? 'true' : 'false', PHP_EOL;
    }

    /**
     * ポイントサイト連携の認証処理の実行コマンド(demo用)
     *
     * @param string $code
     * @param int $user
     * @param string $point_site_code
     */
    public function actionPointSiteSaveAuthorization(string $code, int $user, string $point_site_code)
    {
        $loggedIn = !!PointSiteCooperation::saveAuthorization($code, $user, $point_site_code);

        echo $loggedIn ? 'true' : 'false', PHP_EOL;
    }

    /**
     * ポイントサイト連携の通常チャージの交換の実行コマンド(demo用)
     *
     * @param string $charge_source_code
     * @param int $value
     * @param int $pollet_user_id
     */
    public function actionPointSiteExchange(string $charge_source_code, int $value, int $pollet_user_id)
    {
        $loggedIn = !!PointSiteCooperation::exchange($charge_source_code, $value, $pollet_user_id);

        echo $loggedIn ? 'true' : 'false', PHP_EOL;
    }
}