<?php

namespace app\models;

use yii\base\Object;
use DateTime;

/**
 * 取引履歴
 * @package app\models
 */
class TradingHistory extends Object
{
    /** @var string */
    public $shop;
    /** @var int */
    public $spent_value;
    /** @var DateTime */
    public $trading_date;

    /** @var boolean */
    public $isCharge = false;

    /**
     * @param array $data
     * @return static
     */
    public static function createFromArray(array $data)
    {
        $result = new static();
        $result->shop = $data['shop'];
        $result->spent_value = $data['spent_value'];
        $result->trading_date = new DateTime($data['trading_date']);

        return $result;
    }

    /**
     * ChargeRequestHistoryの配列からTradingHistoryの配列を生成する
     * @param ChargeRequestHistory[] $chargeRequestHistories チャージ履歴の配列
     * @return static[] 取引履歴の配列
     */
    public static function createFromChargeRequestHistories($chargeRequestHistories)
    {
        $ret = [];
        foreach ($chargeRequestHistories as $chargeRequestHistory) {
            $tradingHistory = new TradingHistory();
            $tradingHistory->shop         = 'チャージ';
            $tradingHistory->spent_value  = $chargeRequestHistory->charge_value;
            $tradingHistory->trading_date = new DateTime(date('Y-m-d H:i:s', $chargeRequestHistory->created_at));
            $tradingHistory->isCharge     = true;
            $ret[] = $tradingHistory;
        }
        return $ret;
    }

    /**
     * 取引履歴の配列を取引日でソートする
     * @param TradingHistory[] $tradingHistories 対象の配列
     * @param integer $sort ソート (SORT_DESC or SORT_ASC)
     * @return TradingHistory[] ソート結果
     */
    public static function sortByTradingDate($tradingHistories, $sort = SORT_DESC)
    {
        if ($sort == SORT_DESC) {
            usort($tradingHistories, function(TradingHistory $a, TradingHistory $b) {
                return ($a->trading_date < $b->trading_date);
            });
        } else if ($sort == SORT_ASC) {
            usort($tradingHistories, function(TradingHistory $a, TradingHistory $b) {
                return ($a->trading_date >= $b->trading_date);
            });
        }
        return $tradingHistories;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'shop'         => $this->shop,
            'spent_value'  => $this->spent_value,
            'trading_date' => $this->trading_date->format('Y-m-d H:i:s'),
        ];
    }
}