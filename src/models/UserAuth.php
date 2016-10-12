<?php
namespace app\models;

use app\models\queries\UserAuthQuery;
use yii\db\ActiveRecord;

/**
 * Class UserAuth
 * @package app\models
 *
 * @property integer $user_id
 * @property string $password
 */
class UserAuth extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_auth';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'ユーザID',
            'password' => 'パスワード',
        ];
    }

    /**
     * @inheritdoc
     * @return UserAuthQuery
     */
    public static function find()
    {
        return new UserAuthQuery(get_called_class());
    }
}
