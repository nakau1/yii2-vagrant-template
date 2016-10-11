<?php
namespace tests\unit\fixtures;

use app\models\CardValueCache;
use app\models\MonthlyTradingHistoryCache;
use app\models\PolletUser;
use Faker;
use Yii;

class CedynaMyPageWithCacheFixture extends PolletDbFixture
{
    public static $polletUserIdHasNoCaches = 1;
    public static $polletUserIdHasOldCache = 2;
    public static $polletUserIdHasOldCacheAnother = 3;
    public static $polletUserIdHasLiveCache = 4;
    public static $notExistsCacheMonth = '1501';
    public static $oldCacheMonth = '1502';
    public static $anotherCacheMonth = '1503';
    public static $liveCacheMonth = '1504';

    /** @var Faker\Generator */
    private $faker;

    public function init()
    {
        parent::init();

        $this->faker = Faker\Factory::create();
    }

    public function load()
    {
        $this->savePolletUser(self::$polletUserIdHasNoCaches);

        $this->savePolletUser(self::$polletUserIdHasOldCache);
        $this->saveCardValueCache(
            self::$polletUserIdHasOldCache,
            time() - 60 * 60 * 24 * 7 // 1週間前
        );
        $this->saveTradingHistoryCache(
            self::$polletUserIdHasOldCache,
            self::$oldCacheMonth,
            time() - 60 * 60 * 24 * 7 // 1週間前
        );

        $this->savePolletUser(self::$polletUserIdHasOldCacheAnother);
        $this->saveCardValueCache(
            self::$polletUserIdHasOldCacheAnother,
            time() - 60 * 60 * 24 * 7 // 1週間前
        );
        $this->saveTradingHistoryCache(
            self::$polletUserIdHasOldCacheAnother,
            self::$anotherCacheMonth,
            time() - 60 * 60 * 24 * 7 // 1週間前
        );

        $this->savePolletUser(self::$polletUserIdHasLiveCache);
        $this->saveCardValueCache(
            self::$polletUserIdHasLiveCache,
            time() - 60 * 1 // 1分前
        );
        $this->saveTradingHistoryCache(
            self::$polletUserIdHasLiveCache,
            self::$liveCacheMonth,
            time() - 60 * 1 // 1分前
        );
    }

    private function savePolletUser(int $id)
    {
        $user = new PolletUser();
        $user->id = $id;
        $user->user_code_secret = $this->faker->md5;
        $user->cedyna_id = $this->faker->regexify('[0-9]{10}');
        $user->password = $this->faker->password;
        $user->mail_address = $this->faker->email;
        $user->registration_status = PolletUser::STATUS_ACTIVATED;
        $user->unread_notifications = 0;
        $user->save();

        return $user;
    }

    /**
     * @param int $polletUserId
     * @param int $updatedAt
     * @return CardValueCache
     */
    private function saveCardValueCache(int $polletUserId, int $updatedAt)
    {
        /** @var CardValueCache $cache */
        $cache = new class() extends CardValueCache
        {
            // タイムスタンプの自動挿入を無効化
            public function behaviors()
            {
                return [];
            }
        };
        $cache->pollet_user_id = $polletUserId;
        $cache->value = $this->faker->numberBetween(0, 1000000);
        $cache->updated_at = $updatedAt;
        $cache->save();

        return $cache;
    }

    /**
     * @param int $polletUserId
     * @param string $month
     * @param int $updatedAt
     * @return MonthlyTradingHistoryCache
     */
    private function saveTradingHistoryCache(int $polletUserId, string $month, int $updatedAt)
    {
        /** @var MonthlyTradingHistoryCache $cache */
        $cache = new class() extends MonthlyTradingHistoryCache
        {
            // タイムスタンプの自動挿入を無効化
            public function behaviors()
            {
                return [];
            }
        };
        $cache->pollet_user_id = $polletUserId;
        $cache->month = $month;
        $cache->records_json = '[{"shop":"Nulla et unde quibusdam rerum rerum eligendi.","spent_value":670,"trading_date":"2015-02-09 06:40:35"},{"shop":"Ut incidunt saepe et libero necessitatibus at repellat.","spent_value":4334,"trading_date":"2015-02-01 23:05:23"},{"shop":"Quia dicta neque aut repellat voluptates.","spent_value":1186,"trading_date":"2015-02-03 07:28:35"}]';
        $cache->updated_at = $updatedAt;
        $cache->save();

        return $cache;
    }
}
