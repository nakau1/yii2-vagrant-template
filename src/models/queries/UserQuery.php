<?php
namespace app\models\queries;

use app\models\User;
use yii\db\ActiveQuery;

/**
 * Class UserQuery
 * @package app\models\queries
 * @see User
 */
class UserQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return User[]
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}