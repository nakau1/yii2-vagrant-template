<?php

namespace app\models;

use app\models\queries\TradingHistoryCacheQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "trading_history_cache".
 *
 * @property integer $id
 * @property integer $pollet_user_id
 * @property string  $title
 * @property integer $spent_value 支払額
 * @property string  $trading_date
 * @property integer $updated_at
 * @property integer $created_at
 *
 * @property PolletUser $polletUser
 */
class TradingHistoryCache extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trading_history_cache';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pollet_user_id', 'title', 'spent_value'], 'required'],
            [['pollet_user_id', 'spent_value', 'updated_at', 'created_at'], 'integer'],
            [['trading_date'], 'safe'],
            [['title'], 'string', 'max' => 60],
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
            'title'          => '利用店名',
            'spent_value'    => '利用金額',
            'trading_date'   => '利用日',
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
     * @return TradingHistoryCacheQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TradingHistoryCacheQuery(get_called_class());
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
