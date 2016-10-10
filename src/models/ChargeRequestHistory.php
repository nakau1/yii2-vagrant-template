<?php

namespace app\models;

use app\models\queries\ChargeRequestHistoryQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "charge_request_history".
 *
 * @property integer $id
 * @property integer $pollet_user_id
 * @property integer $cash_withdrawal_id
 * @property integer $charge_value チャージ額
 * @property string  $cause
 * @property string  $processing_status
 * @property integer $updated_at
 * @property integer $created_at
 *
 * @property ChargeErrorHistory[] $chargeErrorHistories
 * @property CashWithdrawal       $cashWithdrawal
 * @property PolletUser           $polletUser
 */
class ChargeRequestHistory extends ActiveRecord
{
    const STATUS_UNPROCESSED_FIRST_CHARGE = 'unprocessed_first_charge';
    const STATUS_READY                    = 'ready';
    const STATUS_IS_MAKING_PAYMENT_FILE   = 'is_making_payment_file';
    const STATUS_APPLIED_CHARGE           = 'applied_charge';
    const STATUS_ERROR                    = 'error';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'charge_request_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pollet_user_id', 'cash_withdrawal_id', 'charge_value', 'processing_status'], 'required'],
            [['pollet_user_id', 'cash_withdrawal_id', 'charge_value', 'updated_at', 'created_at'], 'integer'],
            //セディナのプリペイドシステムの入金ファイルの仕様に合わせて設定
            [['charge_value'], 'integer', 'min' => 1, 'max' => 9999999],
            [['cause'], 'string', 'max' => 100],
            [['processing_status'], 'string', 'max' => 35],
            [['cash_withdrawal_id'], 'exist', 'skipOnError' => true, 'targetClass' => CashWithdrawal::className(), 'targetAttribute' => ['cash_withdrawal_id' => 'id']],
            [['pollet_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => PolletUser::className(), 'targetAttribute' => ['pollet_user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                 => 'ID',
            'pollet_user_id'     => 'ポレットユーザID',
            'cash_withdrawal_id' => '引き落としID',
            'charge_value'       => 'チャージ額',
            'cause'              => 'チャージ理由',
            'processing_status'  => '処理状態',
            'updated_at'         => '更新日時',
            'created_at'         => '作成日時',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getChargeErrorHistories()
    {
        return $this->hasMany(ChargeErrorHistory::className(), ['charge_request_history_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCashWithdrawal()
    {
        return $this->hasOne(CashWithdrawal::className(), ['id' => 'cash_withdrawal_id']);
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
     * @return ChargeRequestHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ChargeRequestHistoryQuery(get_called_class());
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
     * 新しくチャージ申請履歴情報を追加する
     *
     * 他の処理と同一トランザクション内で行うため、
     * このメソッド内ではトランザクションを張りません
     *
     * @param $polletUserId integer ポレットユーザID
     * @param $cashWithdrawalId  integer 引き落としID
     * @param $price integer チャージ額
     * @throws \Exception
     */
    public function add($polletUserId, $cashWithdrawalId, $price)
    {
        $this->pollet_user_id     = $polletUserId;
        $this->cash_withdrawal_id = $cashWithdrawalId;
        $this->charge_value       = $price;
        $this->processing_status  = ChargeRequestHistory::STATUS_UNPROCESSED_FIRST_CHARGE;
        if (!$this->save()) {
            throw new \Exception('failed add charge-request-history.');
        }
    }


}
