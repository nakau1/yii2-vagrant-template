<?php
namespace app\models\queries;

use app\models\UserAuth;
use yii\db\ActiveQuery;

/**
 * Class UserAuthQuery
 * @package app\models\queries
 * @see UserAuth
 */
class UserAuthQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return UserAuth[]
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserAuth|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
