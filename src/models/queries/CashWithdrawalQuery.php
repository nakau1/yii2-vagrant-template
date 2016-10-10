<?php

namespace app\models\queries;

use app\models\CashWithdrawal;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[CashWithdrawal]].
 *
 * @see CashWithdrawal
 */
class CashWithdrawalQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return CashWithdrawal[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return CashWithdrawal|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
