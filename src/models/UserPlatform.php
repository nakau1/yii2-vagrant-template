<?php
namespace app\models;

use app\models\queries\UserPlatformQuery;
use yii\db\ActiveRecord;

/**
 * Class UserPlatform
 * @package app\models
 *
 * @property integer $user_id
 * @property string $uuid
 * @property string $device_token
 * @property string $access_token
 */
class UserPlatform extends ActiveRecord
{
    const WEB     = 'web';
    const IOS     = 'iOS';
    const ANDROID = 'android';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_platform';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'device_token', 'access_token'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'ユーザID',
            'uuid' => 'UUID',
            'device_token' => 'デバイストークン',
            'access_token' => 'アクセストークン',
        ];
    }

    /**
     * @inheritdoc
     * @return UserPlatformQuery
     */
    public static function find()
    {
        return new UserPlatformQuery(get_called_class());
    }
}
