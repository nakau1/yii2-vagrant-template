<?php

namespace app\models;

use app\models\queries\InquiryQuery;
use app\models\traits\ValidateTrait;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "inquiry".
 *
 * @property integer $id
 * @property integer $pollet_user_id
 * @property string  $mail_address
 * @property string  $content
 * @property integer $updated_at
 * @property integer $created_at
 *
 * @property PolletUser $polletUser
 */
class Inquiry extends ActiveRecord
{
    use ValidateTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inquiry';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pollet_user_id', 'mail_address', 'content'], 'required'],
            [['pollet_user_id', 'updated_at', 'created_at'], 'integer'],
            [['content'], 'string'],
            [['mail_address'], 'string', 'max' => 256],
            [['mail_address'], 'validateLaxEmail'],
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
            'mail_address'   => 'メールアドレス',
            'content'        => '問い合わせ内容',
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
     * @return InquiryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new InquiryQuery(get_called_class());
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

    public function getModel()
    {
        return $this;
    }
}
