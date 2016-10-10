<?php

namespace app\models;

use app\models\queries\PolletPointHistoryQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "pollet_point_history".
 *
 * @property integer $id
 * @property integer $trading_history_id
 * @property integer $pollet_user_id
 * @property string  $point 獲得したポイント数
 * @property string  $title
 * @property string  $raw_data
 * @property string  $trading_date
 * @property string  $merchant_code
 * @property integer $spent_value 支払額
 * @property integer $updated_at
 * @property integer $created_at
 * @property double  $point_rate_percentage
 *
 * @property PolletUser $polletUser
 * @property bool $isCharge
 */
class PolletPointHistory extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pollet_point_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['trading_history_id', 'pollet_user_id', 'point', 'title', 'raw_data', 'spent_value', 'point_rate_percentage'], 'required'],
            [['trading_history_id', 'pollet_user_id', 'spent_value', 'updated_at', 'created_at'], 'integer'],
            [['point', 'point_rate_percentage'], 'number'],
            [['raw_data'], 'string'],
            [['trading_date'], 'safe'],
            [['title'], 'string', 'max' => 60],
            [['merchant_code'], 'string', 'max' => 15],
            [['pollet_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => PolletUser::className(), 'targetAttribute' => ['pollet_user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                    => 'ID',
            'trading_history_id'    => 'セディナ取引履歴ID',
            'pollet_user_id'        => 'ポレットユーザID',
            'point'                 => 'ポイント数',
            'title'                 => '表示名',
            'raw_data'              => '取引履歴rawデータ',
            'trading_date'          => '取引日',
            'merchant_code'         => '加盟店コード',
            'spent_value'           => '処理額',
            'updated_at'            => '更新日時',
            'created_at'            => '作成日時',
            'point_rate_percentage' => 'ポイント還元率',
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
     * チャージかどうか
     * @return bool
     */
    public function getIsCharge()
    {
        return ($this->point < 0);
    }

    /**
     * @inheritdoc
     * @return PolletPointHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PolletPointHistoryQuery(get_called_class());
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
