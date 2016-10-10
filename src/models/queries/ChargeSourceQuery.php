<?php

namespace app\models\queries;

use app\models\ChargeSource;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[ChargeSource]].
 *
 * @see ChargeSource
 */
class ChargeSourceQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return ChargeSource[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ChargeSource|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
