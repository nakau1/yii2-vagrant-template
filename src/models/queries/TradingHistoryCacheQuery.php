<?php

namespace app\models\queries;

use app\models\TradingHistoryCache;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[TradingHistoryCache]].
 *
 * @see TradingHistoryCache
 */
class TradingHistoryCacheQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return TradingHistoryCache[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TradingHistoryCache|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
