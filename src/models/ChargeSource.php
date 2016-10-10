<?php

namespace app\models;

use app\models\queries\ChargeSourceQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "charge_source".
 *
 * @property integer $id
 * @property string  $charge_source_code
 * @property integer $min_value 最低交換金額
 * @property integer $card_issue_fee カード発行手数料
 * @property string  $publishing_status
 * @property integer $updated_at
 * @property integer $created_at
 *
 * @property CashWithdrawal[] $cashWithdrawals
 * @property PointSite        $pointSite
 */
class ChargeSource extends ActiveRecord
{
    /** @var string 公開状態…公開 */
    const PUBLISHING_STATUS_PUBLIC = 'public';
    /** @var string 公開状態…非公開*/
    const PUBLISHING_STATUS_PRIVATE = 'private';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'charge_source';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['charge_source_code', 'min_value', 'publishing_status'], 'required'],
            [['min_value', 'card_issue_fee', 'updated_at', 'created_at'], 'integer'],
            [['charge_source_code'], 'string', 'max' => 10],
            [['publishing_status'], 'string', 'max' => 35],
            [['charge_source_code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                 => 'ID',
            'charge_source_code' => 'チャージ元コード',
            'min_value'          => '最低金額',
            'card_issue_fee'     => '初回カード発行手数料',
            'publishing_status'  => '公開状態',
            'updated_at'         => '更新日時',
            'created_at'         => '作成日時',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCashWithdrawals()
    {
        return $this->hasMany(CashWithdrawal::className(), ['charge_source_code' => 'charge_source_code']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPointSite()
    {
        return $this->hasOne(PointSite::className(), ['point_site_code' => 'charge_source_code']);
    }

    /**
     * @inheritdoc
     * @return ChargeSourceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ChargeSourceQuery(get_called_class());
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
