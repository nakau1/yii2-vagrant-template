<?php

namespace app\models;

use app\models\queries\MonthlyTradingHistoryCacheQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "monthly_trading_history_cache".
 *
 * @property integer $id
 * @property integer $pollet_user_id
 * @property string  $records_json
 * @property string  $month
 * @property integer $updated_at
 * @property integer $created_at
 *
 * @property PolletUser $polletUser
 */
class MonthlyTradingHistoryCache extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'monthly_trading_history_cache';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pollet_user_id', 'records_json', 'month'], 'required'],
            [['pollet_user_id', 'updated_at', 'created_at'], 'integer'],
            [['records_json'], 'string'],
            [['month'], 'string', 'max' => 4],
            [['pollet_user_id', 'month'], 'unique', 'targetAttribute' => ['pollet_user_id', 'month'], 'message' => 'The combination of Pollet User ID and Month has already been taken.'],
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
            'records_json'   => '利用履歴データ',
            'month'          => '利用月',
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
     * @return MonthlyTradingHistoryCacheQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MonthlyTradingHistoryCacheQuery(get_called_class());
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
