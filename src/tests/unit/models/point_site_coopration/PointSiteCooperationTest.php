<?php

namespace tests\unit\models\point_site_cooperation;

use app\models\CashWithdrawal;
use app\models\exceptions\PointSiteCooperation\NotCooperationException;
use app\models\exceptions\PointSiteCooperation\PointSiteApiNotFoundException;
use app\models\point_site_cooperation\PointSiteCooperation;
use app\models\PointSiteToken;
use app\models\PolletUser;
use linslin\yii2\curl\Curl;
use tests\unit\fixtures\PointSiteCooperationFixture;
use Yii;
use yii\codeception\TestCase;

class PointSiteCooperationTest extends TestCase
{
    public $appConfig = '@app/config/console.php';

    public function setUp()
    {
        parent::setUp();
    }

    public function fixtures()
    {
        return [
            'fixture' => PointSiteCooperationFixture::class,
        ];
    }

    /**
     * @test
     */
    public function 認証が完了する()
    {
        /** @var PolletUser $user */
        $user = PolletUser::findOne($this->fixture->polletUserId);
        $pointSite = PointSiteCooperationFixture::$pointSite;

        $result = PointSiteCooperation::saveAuthorization(
            PointSiteCooperationFixture::$tokenRequestCode,
            $user->id,
            $pointSite
        );

        $this->assertTrue($result);

        $pointSiteToken = PointSiteToken::find()->where(['pollet_user_id' => $user->id])->one();
        $this->assertEquals($pointSite, $pointSiteToken->point_site_code);
        $this->assertNotEmpty($pointSiteToken->token);
    }

    /**
     * @test
     */
    public function アクセストークンを取得する()
    {
        /** @var PolletUser $user */
        $user = PolletUser::findOne($this->fixture->pointSiteCooperatedUser);
        $pointSite = PointSiteCooperationFixture::$pointSite;

        $actualToken = PointSiteCooperation::getToken($pointSite, $user->id);
        $expectedToken = PointSiteToken::find()->where([
            'pollet_user_id'  => $user->id,
            'point_site_code' => $pointSite,
        ])->one()->token;

        $this->assertEquals($expectedToken, $actualToken);
    }

    /**
     * @test
     */
    public function アクセストークンがなければ例外が発生する()
    {
        /** @var PolletUser $user */
        $user = PolletUser::findOne($this->fixture->polletUserId);
        $pointSite = PointSiteCooperationFixture::$pointSite;

        $this->expectException(NotCooperationException::class);
        PointSiteCooperation::getToken($pointSite, $user->id);
    }

    /**
     * @test
     */
    public function 公開状態の交換APIが取得できる()
    {
        $expected = PointSiteCooperationFixture::$exchangeApiUrl;
        $actual = PointSiteCooperation::getExchangeApi(PointSiteCooperationFixture::$pointSite);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function 非公開状態の交換APIが取得できない()
    {
        $this->expectException(PointSiteApiNotFoundException::class);
        PointSiteCooperation::getExchangeApi(PointSiteCooperationFixture::$privatePointSite);
    }

    /**
     * @test
     */
    public function 交換申請時に引き落とし情報を追加する()
    {
        // 交換申請
        PointSiteCooperation::exchange(
            PointSiteCooperationFixture::$pointSite,
            500,
            $this->fixture->pointSiteCooperatedUser
        );

        // 交換申請額がテーブルに追加されている
        $cashWithdrawal = CashWithdrawal::find()->orderBy(['id' => SORT_DESC])->one();
        $this->assertEquals(PointSiteCooperationFixture::$pointSite, $cashWithdrawal->charge_source_code);
        $this->assertEquals(500, $cashWithdrawal->value);
    }

    /**
     * @test
     */
    public function 交換申請時に引き落とし情報を返却する()
    {
        // 交換申請
        $cashWithdrawal = PointSiteCooperation::exchange(
            PointSiteCooperationFixture::$pointSite,
            500,
            $this->fixture->pointSiteCooperatedUser
        );
        // テーブルに追加した引き落とし情報が返却される
        $this->assertEquals(PointSiteCooperationFixture::$pointSite, $cashWithdrawal->charge_source_code);
        $this->assertEquals(500, $cashWithdrawal->value);
    }

    /**
     * @test
     */
    public function 交換申請時にポイントサイトの交換APIにGETリクエストが発行される()
    {
        // モックを設定
        $curlMock = $this->createPartialMock(Curl::class, ['get']);
        Yii::$app->set('curl', $curlMock);

        // getメソッドが交換APIを引数に呼ばれることをassertする
        $curlMock->expects($this->once())
            ->method('get')
            ->with($this->stringStartsWith(PointSiteCooperationFixture::$exchangeApiUrl));

        // 実行
        PointSiteCooperation::exchange(
            PointSiteCooperationFixture::$pointSite,
            500,
            $this->fixture->pointSiteCooperatedUser
        );
    }
}