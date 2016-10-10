<?php

namespace app\models;

use app\models\queries\PushNotificationTokenQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "push_notification_token".
 *
 * @property integer $id
 * @property integer $pollet_user_id
 * @property string  $device_id
 * @property string  $token
 * @property string  $platform
 * @property integer $updated_at
 * @property integer $created_at
 *
 * @property PolletUser $polletUser
 */
class PushNotificationToken extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'push_notification_token';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pollet_user_id', 'device_id', 'token', 'platform'], 'required'],
            [['pollet_user_id', 'updated_at', 'created_at'], 'integer'],
            [['device_id', 'token'], 'string', 'max' => 256],
            [['platform'], 'string', 'max' => 20],
            [['pollet_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => PolletUser::className(), 'targetAttribute' => ['pollet_user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'pollet_user_id' => 'ポレットユーザID',
            'device_id'      => 'デバイスID',
            'token'          => 'デバイストークン',
            'platform'       => 'プラットフォーム',
            'updated_at'     => '更新日時',
            'created_at'     => '作成日時',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPolletUser()
    {
        return $this->hasOne(PolletUser::className(), ['id' => 'pollet_user_id']);
    }

    /**
     * @inheritdoc
     * @return PushNotificationTokenQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PushNotificationTokenQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
