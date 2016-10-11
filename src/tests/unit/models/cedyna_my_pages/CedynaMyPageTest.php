<?php

namespace tests\unit\models\cedyna_my_pages;

use app\models\cedyna_my_pages\CedynaMyPage;
use app\models\PolletUser;
use app\models\TradingHistory;
use tests\unit\fixtures\CedynaMyPageFixture;
use yii\codeception\TestCase;

class CedynaMyPageTest extends TestCase
{
    public $appConfig = '@app/config/console.php';

    public function setUp()
    {
        parent::setUp();
    }

    public function fixtures()
    {
        return [
            CedynaMyPageFixture::class,
        ];
    }

    /**
     * @test
     */
    public function カード残高が数値として取得できる()
    {
        /** @var PolletUser $user */
        $user = PolletUser::findOne(CedynaMyPageFixture::$polletUserId);
        $myPage = CedynaMyPage::getInstance();
        $myPage->login($user->cedyna_id, $user->password);
        $value = $myPage->cardValue();

        $this->assertTrue(is_int($value));
    }

    /**
     * @test
     */
    public function 利用履歴がオブジェクトの配列として取得できる()
    {
        /** @var PolletUser $user */
        $user = PolletUser::findOne(CedynaMyPageFixture::$polletUserId);
        $myPage = CedynaMyPage::getInstance();
        $myPage->login($user->cedyna_id, $user->password);

        $histories = $myPage->tradingHistories('1603');

        $this->assertTrue(is_array($histories));
        foreach ($histories as $history) {
            $this->assertInstanceOf(TradingHistory::class, $history);
        }
    }

    /**
     * @test
     */
    public function メールアドレスの送信結果を取得できる()
    {
        $myPage = CedynaMyPage::getInstance();
        // 送信できたらtrue
        $this->assertTrue($myPage->sendIssuingFormLink('point-wallet-system@oz-vision.co.jp'));
        // 送信できなかったらfalse
        $this->assertFalse($myPage->sendIssuingFormLink(''));
    }
}