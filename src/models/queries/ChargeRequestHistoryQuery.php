<?php

namespace app\models\queries;

use app\helpers\YearMonth;
use app\models\ChargeRequestHistory;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[ChargeRequestHistory]].
 *
 * @see ChargeRequestHistory
 */
class ChargeRequestHistoryQuery extends ActiveQuery
{
    /**
     * 指定した年月のものだけを抽出するクエリを返す
     * @param string|null $month 'yymm'の形式の月(2016年9月は'1609')
     * @return $this
     */
    public function atMonth($month = null)
    {
        list($from, $to) = YearMonth::getTimestampsFromTo($month);
        return $this->andWhere([
            '>=',
            ChargeRequestHistory::tableName() . '.created_at',
            $from,
        ])->andWhere([
            '<=',
            ChargeRequestHistory::tableName() . '.created_at',
            $to,
        ]);
    }

    /**
     * エラー状態ではないものだけを抽出するクエリを返す
     * @return $this
     */
    public function active()
    {
        return $this->andWhere([
            '<>',
            ChargeRequestHistory::tableName(). '.processing_status',
            ChargeRequestHistory::STATUS_ERROR,
        ]);
    }

    /**
     * @inheritdoc
     * @return ChargeRequestHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ChargeRequestHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
