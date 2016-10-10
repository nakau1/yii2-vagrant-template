<?php

namespace app\models\cedyna_my_pages;

use app\models\CardValueCache;
use app\models\exceptions\CedynaMyPage\ParsingHtmlException;
use app\models\exceptions\CedynaMyPage\NetworkException;
use app\models\exceptions\CedynaMyPage\UnauthorizedException;
use app\models\MonthlyTradingHistoryCache;
use app\models\PolletUser;
use app\models\TradingHistory;
use Yii;

/**
 * Class CedynaMyPageWithCache
 * セディナのマイページ。取得結果はキャッシュされる。
 *
 * @package app\models\cedyna_my_pages
 */
class CedynaMyPageWithCache extends CedynaMyPage
{
    private $cardValueCacheSeconds;
    private $tradingHistoryCacheSeconds;

    /**
     * 設定ファイルを反映したインスタンスを生成する
     *
     * @inheritdoc
     * @return CedynaMyPageWithCache
     */
    public static function getInstance()
    {
        return Yii::$app->get('cedynaMyPageWithCache');
    }

    /**
     * @param int $seconds
     * @return $this
     */
    public function setCardValueCacheSeconds(int $seconds)
    {
        $this->cardValueCacheSeconds = $seconds;

        return $this;
    }

    /**
     * @param int $seconds
     * @return $this
     */
    public function setTradingHistoryCacheSeconds(int $seconds)
    {
        $this->tradingHistoryCacheSeconds = $seconds;

        return $this;
    }

    /**
     * カード残高を取得する。取得結果はキャッシュする。
     *
     * @inheritdoc
     * @return int
     * @throws UnauthorizedException
     * @throws NetworkException
     * @throws ParsingHtmlException
     */
    public function cardValue(): int
    {
        // キャッシュから取得
        $polletUser = PolletUser::findByCedynaId($this->cedynaId);
        $cache = $polletUser->cardValueCache;
        if ($cache && $this->cardValueCacheIsLive($cache->updated_at)) {
            return $cache->value;
        }

        // スクレイピングで取得
        $cardValue = parent::cardValue();

        // キャッシュに保存
        if (!$cache) {
            $cache = new CardValueCache();
            $cache->pollet_user_id = $polletUser->id;
        }
        $cache->value = $cardValue;
        $cache->save();

        return $cardValue;
    }

    /**
     * カード利用履歴を取得する。取得結果はキャッシュする。
     *
     * @param string $month 'yymm'の形式の月
     * @return TradingHistory[]
     * @inheritdoc
     * @throws UnauthorizedException
     * @throws NetworkException
     * @throws ParsingHtmlException
     */
    public function tradingHistories(string $month): array
    {
        // キャッシュから取得
        $polletUser = PolletUser::findByCedynaId($this->cedynaId);
        /** @var MonthlyTradingHistoryCache $cache */
        $cache = $polletUser->getMonthlyTradingHistoryCaches()->where(['month' => $month])->one();
        if ($cache && $this->tradingHistoryCacheIsLive($cache->updated_at)) {
            return array_map(function (array $record) {
                return TradingHistory::createFromArray($record);
            }, json_decode($cache->records_json, true));
        }

        // スクレイピングで取得
        $histories = parent::tradingHistories($month);

        // キャッシュに保存
        if (!$cache) {
            $cache = new MonthlyTradingHistoryCache();
            $cache->pollet_user_id = $polletUser->id;
            $cache->month = $month;
        }
        $cache->records_json = json_encode(array_map(function (TradingHistory $history) {
            return $history->toArray();
        }, $histories));
        $cache->save();

        return $histories;
    }

    /**
     * @param int $updatedAt
     * @return bool
     */
    private function cardValueCacheIsLive(int $updatedAt): bool
    {
        return $this->cacheIsLive($updatedAt, $this->cardValueCacheSeconds);
    }

    /**
     * @param int $updatedAt
     * @return bool
     */
    private function tradingHistoryCacheIsLive(int $updatedAt): bool
    {
        return $this->cacheIsLive($updatedAt, $this->tradingHistoryCacheSeconds);
    }

    /**
     * @param int $updatedAt
     * @param int $cacheSeconds
     * @return bool
     */
    private function cacheIsLive(int $updatedAt, int $cacheSeconds): bool
    {
        $livingTime = time() - $updatedAt;

        return $livingTime <= $cacheSeconds;
    }
}
