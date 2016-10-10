<?php

namespace app\models;

use app\models\queries\CashWithdrawalQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "cash_withdrawal".
 * チャージ元からの引き落とし情報を管理する
 *
 * @property integer $id
 * @property string  $charge_source_code
 * @property integer $value 引き落とし額
 * @property integer $updated_at
 * @property integer $created_at
 *
 * @property ChargeSource           $chargeSource
 * @property ChargeRequestHistory[] $chargeRequestHistories
 */
class CashWithdrawal extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cash_withdrawal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['charge_source_code', 'value'], 'required'],
            [['value', 'updated_at', 'created_at'], 'integer'],
            [['charge_source_code'], 'string', 'max' => 10],
            [['charge_source_code'], 'exist', 'skipOnError' => true, 'targetClass' => ChargeSource::className(), 'targetAttribute' => ['charge_source_code' => 'charge_source_code']],
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
            'value'              => '引き落とし額',
            'updated_at'         => '更新日時',
            'created_at'         => '作成日時',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getChargeSource()
    {
        return $this->hasOne(ChargeSource::className(), ['charge_source_code' => 'charge_source_code']);
    }

    /**
     * @return ActiveQuery
     */
    public function getChargeRequestHistories()
    {
        return $this->hasMany(ChargeRequestHistory::className(), ['cash_withdrawal_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return CashWithdrawalQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CashWithdrawalQuery(get_called_class());
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
     * 新しく引き落とし情報を追加する
     *
     * 他の処理と同一トランザクション内で行うため、
     * このメソッド内ではトランザクションを張りません
     *
     * @param $chargeSourceCode string チャージ元コード
     * @param $price integer 引き落とし金額
     * @throws \Exception
     */
    public function add($chargeSourceCode, $price)
    {
        $this->charge_source_code = $chargeSourceCode;
        $this->value              = $price;

        if (!$this->save()) {
            throw new \Exception('failed add cash-withdrawal.');
        }
    }
}
