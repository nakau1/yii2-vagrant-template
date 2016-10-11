<?php
namespace tests\unit\fixtures;

use app\models\ChargeSource;
use app\models\PointSite;
use app\models\PointSiteApi;
use app\models\PointSiteToken;
use app\models\PolletUser;
use Faker;
use Yii;

class PointSiteCooperationFixture extends PolletDbFixture
{
    public $polletUserId = 1;
    public $pointSiteCooperatedUser = 2;
    public static $tokenRequestCode = 'tokenRequestCode';
    public static $pointSite = 'test1';
    public static $pointSiteName = '公開サイト';
    public static $exchangeApiUrl = 'http://hogehoge.com/exchange/';
    public static $privatePointSite = 'test2';
    public static $privatePointSiteName = '非公開サイト';
    public static $privateExchangeApiUrl = 'http://hugahuga.com/exchange/';


    public function load()
    {
        $faker = Faker\Factory::create();
        $this->publicPointSite($faker);
        $this->privatePointSite($faker);

        //初回チャージ前のユーザー
        $user = new PolletUser();
        $user->id = $this->polletUserId;
        $user->user_code_secret = $faker->md5;
        $user->mail_address = $faker->email;
        $user->registration_status = PolletUser::STATUS_NEW_USER;
        $user->unread_notifications = 0;
        $user->save();

        //ポイントサイト連携済みのユーザー
        $faker = Faker\Factory::create();
        $user = new PolletUser();
        $user->id = $this->pointSiteCooperatedUser;
        $user->user_code_secret = $faker->md5;
        $user->mail_address = $faker->email;
        $user->registration_status = PolletUser::STATUS_NEW_USER;
        $user->unread_notifications = 0;
        $user->save();

        //ポイントサイト連携
        $pointSiteToken = new PointSiteToken();
        $pointSiteToken->pollet_user_id = $this->pointSiteCooperatedUser;
        $pointSiteToken->point_site_code = self::$pointSite;
        $pointSiteToken->token = $faker->regexify('[A-Z0-9._%+-]{10}');
        $pointSiteToken->save();
    }

    private function publicPointSite(Faker\Generator $faker)
    {
        //チャージ元の登録
        $chargeSource = new ChargeSource();
        $chargeSource->charge_source_code = self::$pointSite;
        $chargeSource->min_value = 300;
        $chargeSource->card_issue_fee = 100;
        $chargeSource->publishing_status = ChargeSource::PUBLISHING_STATUS_PUBLIC;
        $chargeSource->save();

        //ポイントサイトの登録
        $pointSite = new PointSite();
        $pointSite->point_site_code = self::$pointSite;
        $pointSite->site_name = self::$pointSiteName;
        $pointSite->url = $faker->url;
        $pointSite->icon_image_url = $faker->imageUrl();
        $pointSite->denomination = 'pt';
        $pointSite->introduce_charge_rate_point = 1;
        $pointSite->introduce_charge_rate_price = 1;
        $pointSite->description = $faker->text(100);
        $pointSite->auth_url = $faker->url;
        $pointSite->publishing_status = PointSite::PUBLISHING_STATUS_PUBLIC;
        $pointSite->save();

        //ポイントサイトAPI
        $pointSiteApi = new PointSiteApi();
        $pointSiteApi->point_site_code = self::$pointSite;
        $pointSiteApi->api_name = PointSiteApi::API_NAME_EXCHANGE;
        $pointSiteApi->url = self::$exchangeApiUrl;
        $pointSiteApi->publishing_status = PointSiteApi::PUBLISHING_STATUS_PUBLIC;
        $pointSiteApi->save();
    }

    private function privatePointSite(Faker\Generator $faker)
    {
        //チャージ元の登録
        $chargeSource = new ChargeSource();
        $chargeSource->charge_source_code = self::$privatePointSite;
        $chargeSource->min_value = 300;
        $chargeSource->card_issue_fee = 100;
        $chargeSource->publishing_status = ChargeSource::PUBLISHING_STATUS_PRIVATE;
        $chargeSource->save();

        //ポイントサイトの登録
        $pointSite = new PointSite();
        $pointSite->point_site_code = self::$privatePointSite;
        $pointSite->site_name = self::$privatePointSiteName;
        $pointSite->url = $faker->url;
        $pointSite->icon_image_url = $faker->imageUrl();
        $pointSite->denomination = 'pt';
        $pointSite->introduce_charge_rate_point = 1;
        $pointSite->introduce_charge_rate_price = 1;
        $pointSite->description = $faker->text(100);
        $pointSite->auth_url = $faker->url;
        $pointSite->publishing_status = PointSite::PUBLISHING_STATUS_PRIVATE;
        $pointSite->save();

        //ポイントサイトAPI
        $pointSiteApi = new PointSiteApi();
        $pointSiteApi->point_site_code = self::$privatePointSite;
        $pointSiteApi->api_name = PointSiteApi::API_NAME_EXCHANGE;
        $pointSiteApi->url = self::$privateExchangeApiUrl;
        $pointSiteApi->publishing_status = PointSiteApi::PUBLISHING_STATUS_PRIVATE;
        $pointSiteApi->save();
    }
}
