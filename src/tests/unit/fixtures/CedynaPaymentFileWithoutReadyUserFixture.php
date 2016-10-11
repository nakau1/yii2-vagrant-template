<?php
namespace tests\unit\fixtures;

use app\models\ChargeRequestHistory;
use Faker;

class CedynaPaymentFileWithoutReadyUserFixture extends CedynaPaymentFileFixture
{
    public function load()
    {
        // 共通で使うチャージ元
        $chargeSource = $this->createChargeSource();

        // 入金ファイル作成中のユーザー1
        $user = $this->createUser(10001);
        $this->createChargeRequest(100001, ChargeRequestHistory::STATUS_IS_MAKING_PAYMENT_FILE, $user, $chargeSource);
    }
}
