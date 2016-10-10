<?php

namespace app\models;

use app\models\queries\InformationQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "information".
 * お知らせを管理する
 *
 * @property integer $id
 * @property string  $heading
 * @property string  $body
 * @property string  $begin_date
 * @property string  $end_date
 * @property integer $sends_push プッシュ通知を送信するかどうか
 * @property integer $is_important 重要なお知らせかどうか
 * @property string  $publishing_status
 * @property integer $updated_at
 * @property integer $created_at
 *
 * @property PushInformationOpening[] $pushInformationOpenings
 */
class Information extends ActiveRecord
{
    /** @var string 公開状態…公開 */
    const PUBLISHING_STATUS_PUBLIC = 'public';
    /** @var string 公開状態…非公開*/
    const PUBLISHING_STATUS_PRIVATE = 'private';

    /** @var bool 既読かどうか */
    public $isOpened = true;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'information';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['heading', 'body', 'publishing_status'], 'required'],
            [['body'], 'string'],
            [['begin_date', 'end_date'], 'safe'],
            [['sends_push', 'is_important', 'updated_at', 'created_at'], 'integer'],
            [['heading'], 'string', 'max' => 50],
            [['publishing_status'], 'string', 'max' => 35],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'heading'           => '表題',
            'body'              => '本文',
            'begin_date'        => '開始日時',
            'end_date'          => '終了日時',
            'sends_push'        => 'プッシュ通知を送信するかどうか',
            'is_important'      => '重要なお知らせかどうか',
            'publishing_status' => '公開状態',
            'updated_at'        => '更新日時',
            'created_at'        => '作成日時',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPushInformationOpenings()
    {
        return $this->hasMany(PushInformationOpening::className(), ['information_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return InformationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new InformationQuery(get_called_class());
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
    
    /**
     * 指定したポレットIDのユーザに紐付くプッシュ通知のお知らせを取得するアクティブクエリを返す
     * @return ActiveQuery
     */
    public static function findNotifiedTo() //TODO: 引数としてpolletIdを取らせる(警告が出るから一旦消し)
    {
        return self::find(); //TODO: DB設計見直しがあるかもしれないので、一旦なにもフィルタしないアクティブクエリを返している
    }
}
