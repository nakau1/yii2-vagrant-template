<?php

namespace app\models\queries;

use app\helpers\YearMonth;
use app\models\PolletPointHistory;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[PolletPointHistory]].
 *
 * @see PolletPointHistory
 */
class PolletPointHistoryQuery extends ActiveQuery
{
    /**
     *
     * @param string|null $month 'yymm'の形式の月(2016年9月は'1609')
     * @return $this
     */
    public function atMonth($month = null)
    {
        list($from, $to) = YearMonth::getDateStringsFromTo($month);
        return $this->andWhere([
            '>=',
            PolletPointHistory::tableName() . '.trading_date',
            $from,
        ])->andWhere([
            '<=',
            PolletPointHistory::tableName() . '.trading_date',
            $to,
        ]);
    }

    /**
     * 
     * @return $this
     */
    public function ordered()
    {
        return $this->addOrderBy([
            PolletPointHistory::tableName() . '.trading_date' => SORT_ASC,
        ]);
    }

    /**
     * @inheritdoc
     * @return PolletPointHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PolletPointHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
