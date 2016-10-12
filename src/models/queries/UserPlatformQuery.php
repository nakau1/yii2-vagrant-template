<?php
namespace app\models\queries;

use app\models\UserPlatform;
use yii\db\ActiveQuery;

/**
 * Class UserPlatformQuery
 * @package app\models\queries
 * @see UserPlatform
 */
class UserPlatformQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return UserPlatform[]
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserPlatform|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
