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
 * @property string $uuid
 * @property string $device_token
 * @property string $access_token
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
            [['password', 'uuid', 'device_token', 'access_token'], 'string', 'max' => 256],
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
            'uuid' => 'UUID',
            'device_token' => 'デバイストークン',
            'access_token' => 'アクセストークン',
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
