<?php
namespace tests\unit\fixtures;

use app\models\CashWithdrawal;
use app\models\ChargeRequestHistory;
use app\models\ChargeSource;
use app\models\PolletUser;
use Faker;
use yii\helpers\FileHelper;
use yii;

class CedynaPaymentFileFixture extends PolletDbFixture
{
    public $HULFT配信用ディレクトリ;
    public $作業ディレクトリ;
    public $完了ディレクトリ;

    /**
     * 初期化
     */
    public function init()
    {
        $this->HULFT配信用ディレクトリ = Yii::$app->runtimePath . '/hulft/send/send_payment_file';
        $this->作業ディレクトリ = Yii::$app->runtimePath . '/hulft/app/send_payment_file/processing';
        $this->完了ディレクトリ = Yii::$app->runtimePath . '/hulft/app/send_payment_file/complete';
    }

    /**
     * make-cedyna-payment-fileバッチで使用するテストデータの作成
     */
    public function load()
    {
        // 共通で使うチャージ元
        $chargeSource = $this->createChargeSource();

        // 入金ファイル作成中のユーザー1
        $user = $this->createUser(10001);
        $this->createChargeRequest(100001, ChargeRequestHistory::STATUS_IS_MAKING_PAYMENT_FILE, $user, $chargeSource);

        // 入金ファイル作成中のユーザー2
        $user = $this->createUser(10002);
        $this->createChargeRequest(100002, ChargeRequestHistory::STATUS_IS_MAKING_PAYMENT_FILE, $user, $chargeSource);

        // 処理待ちのユーザー1
        $user = $this->createUser(10003);
        $this->createChargeRequest(100003, ChargeRequestHistory::STATUS_READY, $user, $chargeSource);
    }

    /**
     * make-cedyna-payment-fileバッチで使用するテストデータの作成（複数ユーザー対象）
     */
    public function loadMultipleReadyUsers()
    {
        // 共通で使うチャージ元
        $chargeSource = $this->createChargeSource();

        // 処理待ちのユーザー1
        $user = $this->createUser(10011);
        $this->createChargeRequest(100011, ChargeRequestHistory::STATUS_READY, $user, $chargeSource);

        // 処理待ちのユーザー2
        $user = $this->createUser(10012);
        $this->createChargeRequest(100012, ChargeRequestHistory::STATUS_READY, $user, $chargeSource);
    }

    /**
     * make-cedyna-payment-fileバッチで使用するディレクトリの削除
     * @throws yii\base\ErrorException
     */
    public function removeDir()
    {
        FileHelper::removeDirectory($this->HULFT配信用ディレクトリ);
        FileHelper::removeDirectory($this->作業ディレクトリ);
        FileHelper::removeDirectory($this->完了ディレクトリ);
    }

    /**
     * @return ChargeSource
     */
    protected function createChargeSource()
    {
        $chargeSource = new ChargeSource();
        $chargeSource->charge_source_code = 'testcharge';
        $chargeSource->min_value = 300;
        $chargeSource->card_issue_fee = 0;
        $chargeSource->publishing_status = 'public';
        $chargeSource->save();

        return $chargeSource;
    }

    /**
     * @param int $polletId
     * @return PolletUser
     */
    protected function createUser(int $polletId)
    {
        $faker = Faker\Factory::create();
        $user = new PolletUser();
        $user->id = $polletId;
        $user->user_code_secret = $faker->md5;
        $user->cedyna_id = $faker->regexify('[0-9]{16}');
        $user->mail_address = $faker->email;
        $user->registration_status = 'finished_first_charge';
        $user->unread_notifications = 0;
        $user->save();

        return $user;
    }

    /**
     * @param int $id
     * @param string $processingStatus
     * @param PolletUser $user
     * @param ChargeSource $chargeSource
     * @return ChargeRequestHistory
     */
    protected function createChargeRequest(int $id, string $processingStatus, PolletUser $user, ChargeSource $chargeSource)
    {
        $cashWithdrawal = $this->createCashWithdrawal($chargeSource);
        $chargeRequestHistory = new ChargeRequestHistory();
        $chargeRequestHistory->id = $id;
        $chargeRequestHistory->pollet_user_id = $user->id;
        $chargeRequestHistory->cash_withdrawal_id = $cashWithdrawal->id;
        $chargeRequestHistory->charge_value = ($cashWithdrawal->value - $chargeSource->card_issue_fee);
        $chargeRequestHistory->processing_status = $processingStatus;
        $chargeRequestHistory->cause = 'テストチャージ';
        $chargeRequestHistory->save();

        return $chargeRequestHistory;
    }

    /**
     * @param ChargeSource $chargeSource
     * @return CashWithdrawal
     */
    protected function createCashWithdrawal(ChargeSource $chargeSource)
    {
        $cashWithdrawal = new CashWithdrawal();
        $cashWithdrawal->charge_source_code = $chargeSource->charge_source_code;
        $cashWithdrawal->value = 1000;
        $cashWithdrawal->save();

        return $cashWithdrawal;
    }
}
