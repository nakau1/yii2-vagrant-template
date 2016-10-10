<?php

namespace app\models\queries;

use app\models\PolletUser;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[PolletUser]].
 *
 * @see PolletUser
 */
class PolletUserQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return PolletUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PolletUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
