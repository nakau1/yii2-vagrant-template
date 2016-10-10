<?php
namespace app\commands;

use app\models\PointSiteApi;
use app\models\PointSiteToken;
use Faker\Generator;
use Faker\Factory;
use Yii;
use yii\console\Controller;
use app\models\PolletUser;
use app\models\ChargeSource;
use app\models\PointSite;
use app\models\CashWithdrawal;
use app\models\ChargeRequestHistory;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;

/**
 * 初期データを投入する
 * TODO: 見通しが悪いのでファイル分割する
 * TODO: 本番で実行できないようにする
 *
 * Class SeedController
 * @package app\commands
 */
class SeedController extends Controller
{
    /** @var Generator */
    private $faker;

    public function init()
    {
        parent::init();
        $this->faker = Factory::create();
    }

    /**
     * データをすべて削除する
     *
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    public function actionClear()
    {
        // DBのデータをすべて削除する
        foreach (ActiveRecord::getDb()->getSchema()->tableSchemas as $table) {
            if ($table->fullName === 'migration') {
                // 消してしまうとマイグレーションが失敗する
                continue;
            }

            ActiveRecord::getDb()->createCommand()->delete($table->fullName)->execute();
            if ($table->sequenceName !== null) {
                ActiveRecord::getDb()->createCommand()->resetSequence($table->fullName, 1)->execute();
            }
        }
        // ファイルをすべて削除する
        FileHelper::removeDirectory(Yii::$app->runtimePath.'/hulft');
    }

    /**
     * demoに使うデータの生成
     */
    public function actionIndex()
    {
        $this->actionClear();

        $demoHapitas = $this->setDemoHapitas();

        $user = $this->setWaitingIssueUser(100001);
        $this->chargeFirstTime($user, $demoHapitas);
        $user = $this->setWaitingIssueUser(100002);
        $this->chargeFirstTime($user, $demoHapitas);
        $user = $this->setWaitingIssueUser(100003);
        $this->chargeFirstTime($user, $demoHapitas);
        $user = $this->setWaitingIssueUser(100004);
        $this->chargeFirstTime($user, $demoHapitas);
        $user = $this->setWaitingIssueUser(100005);
        $this->chargeFirstTime($user, $demoHapitas);

        $this->setIssuedUser(100006);
        $activatedUser = $this->setActivatedUser(100007);
        $this->outputUserInfo($activatedUser);
        $this->setPointSiteToken($activatedUser, $demoHapitas);

        $this->makeReceivedNumberedCedynaIdFile(
            [100001, 100002, 100003],
            [
                $this->faker->regexify('[0-9]{16}'),
                $this->faker->regexify('[0-9]{16}'),
                $this->faker->regexify('[0-9]{16}'),
            ]
        );
    }

    /**
     * ハピタスをdemo環境にチャージ元として設定する
     */
    private function setDemoHapitas()
    {
        $chargeSource = new ChargeSource();
        $chargeSource->charge_source_code = 'hapitas';
        $chargeSource->min_value          = 300;
        $chargeSource->card_issue_fee     = 250;
        $chargeSource->publishing_status  = ChargeSource::PUBLISHING_STATUS_PUBLIC;
        $chargeSource->save();

        $pointSite = new PointSite();
        $pointSite->point_site_code             = 'hapitas';
        $pointSite->site_name                   = 'ハピタス';
        $pointSite->url                         = 'http://hapitas.jp';
        $pointSite->icon_image_url              = 'http://img.hapitas.jp/img_rh/images/logo.png';
        $pointSite->denomination                = 'ポイント';
        $pointSite->introduce_charge_rate_point = 1;
        $pointSite->introduce_charge_rate_price = 1;
        $pointSite->description                 = 'ハピタスは「楽天」や「ヤフオク」など2,000社以上と提携。提携先の利用でポイントが貯まって、無料で現金やギフト券に交換できます。';
        //まだ認証画面がないので偽物。404返ってくる
        $pointSite->auth_url                    = 'http://hapitas.jp/dummy-auth';
        $pointSite->publishing_status           = PointSite::PUBLISHING_STATUS_PUBLIC;
        $pointSite->save();

        $pointSiteApi = new PointSiteApi();
        $pointSiteApi->point_site_code = 'hapitas';
        $pointSiteApi->api_name = PointSiteApi::API_NAME_EXCHANGE;
        $pointSiteApi->url = 'http://hapitas.jp/dummy-exchange';
        $pointSiteApi->publishing_status = PointSiteApi::PUBLISHING_STATUS_PUBLIC;
        $pointSiteApi->save();

        return $chargeSource;
    }

    /**
     * 初回チャージ申請を行った状態のデータを生成
     *
     * @param PolletUser $user
     * @param ChargeSource $chargeSource
     */
    private function chargeFirstTime(PolletUser $user, ChargeSource $chargeSource)
    {
        //引き落としデータ生成とチャージ申請履歴生成は同時に行う
        $cashWithdrawal = $this->setCashWithdrawal($chargeSource, $this->faker->numberBetween(5, 100) * 100);
        $this->setFirstChargeRequest($user, $cashWithdrawal, $chargeSource);
    }

    /**
     * @param int $polletId
     * @return PolletUser
     */
    private function setWaitingIssueUser(int $polletId)
    {
        $user = $this->makeDefaultPolletUser($polletId);
        $user->registration_status = PolletUser::STATUS_WAITING_ISSUE;
        $user->cedyna_id = null;
        $user->password = null;
        $user->save();

        return $user;
    }

    /**
     * @param int $polletId
     * @return PolletUser
     */
    private function setIssuedUser(int $polletId)
    {
        $user = $this->makeDefaultPolletUser($polletId);
        $user->registration_status = PolletUser::STATUS_ISSUED;
        $user->password = null;
        $user->save();

        return $user;
    }

    /**
     * @param int $polletId
     * @return PolletUser
     */
    private function setActivatedUser(int $polletId)
    {
        $user = $this->makeDefaultPolletUser($polletId);
        $user->registration_status = PolletUser::STATUS_ACTIVATED;
        $user->save();

        return $user;
    }

    /**
     * @param int $polletId
     * @return PolletUser
     */
    private function makeDefaultPolletUser(int $polletId)
    {
        $user = new PolletUser();

        $user->id = $polletId;
        $user->user_code_secret = $this->faker->md5;
        $user->cedyna_id = $this->faker->regexify('[0-9]{10}');
        $user->password = $this->faker->password();
        $user->mail_address = $this->faker->email;
        $user->unread_notifications = 0;

        return $user;
    }

    /**
     * 引き落としデータを生成
     *
     * @param ChargeSource $chargeSource
     * @param int $value
     *
     * @return CashWithdrawal
     */
    private function setCashWithdrawal(ChargeSource $chargeSource, int $value)
    {
        $cashWithdrawal = new CashWithdrawal();
        $cashWithdrawal->charge_source_code = $chargeSource->charge_source_code;
        $cashWithdrawal->value = $value;
        $cashWithdrawal->save();

        return $cashWithdrawal;
    }

    /**
     * 発番通知前の初回チャージ申請履歴データを生成
     *
     * @param PolletUser $user
     * @param CashWithdrawal $cashWithdrawal
     * @param ChargeSource $chargeSource
     *
     * @return ChargeRequestHistory
     */
    private function setFirstChargeRequest(
        PolletUser $user,
        cashWithdrawal $cashWithdrawal,
        chargeSource $chargeSource
    ) {
        $chargeRequestHistory = new ChargeRequestHistory();
        $chargeRequestHistory->pollet_user_id = $user->id;
        $chargeRequestHistory->cash_withdrawal_id = $cashWithdrawal->id;
        $chargeRequestHistory->charge_value = ($cashWithdrawal->value - $chargeSource->card_issue_fee);
        $chargeRequestHistory->processing_status = $chargeRequestHistory::STATUS_UNPROCESSED_FIRST_CHARGE;
        $pointSite = PointSite::find()->where(['point_site_code' => $chargeSource->charge_source_code])->one();
        $chargeRequestHistory->cause = $pointSite['site_name'].'からチャージ';
        $chargeRequestHistory->save();

        return $chargeRequestHistory;
    }

    /**
     * @param array $polletIds
     * @param array $cedynaIds
     */
    private function makeReceivedNumberedCedynaIdFile(array $polletIds, array $cedynaIds)
    {
        $receivedFilesDirectory = Yii::$app->runtimePath.'/hulft/recv/receive_numbered_cedyna_id';
        $retryDirectory = Yii::$app->runtimePath.'/hulft/app/receive_numbered_cedyna_id/retry';
        $processingDirectory = Yii::$app->runtimePath.'/hulft/app/receive_numbered_cedyna_id/processing';
        $completeDirectory = Yii::$app->runtimePath.'/hulft/app/receive_numbered_cedyna_id/complete';

        $this->makeDirectoryIfNotExists($receivedFilesDirectory);
        $this->makeDirectoryIfNotExists($retryDirectory);
        $this->makeDirectoryIfNotExists($processingDirectory);
        $this->makeDirectoryIfNotExists($completeDirectory);

        $csv = '"S","'.$this->faker->dateTime->format('Y/m/d H:i:s').'"'."\n";
        foreach (array_combine($polletIds, $cedynaIds) as $polletId => $cedynaId) {
            $formattedPolletId = sprintf('%016d', $polletId);
            $formattedCedynaId = sprintf('%016d', $cedynaId);
            $csv .= '"D","409336123456789000","'.$formattedCedynaId.'","'.$formattedPolletId.'","abcd","20160801","20160805","20"'."\n";
        }
        $csv .= '"E",'.sprintf('%8s', count($polletIds))."\n";
        $sjisCsv = mb_convert_encoding($csv, 'SJIS');
        file_put_contents($receivedFilesDirectory.'/'.$this->faker->unixTime.'.csv', $sjisCsv);
    }

    /**
     * @param string $path
     * @throws \yii\base\Exception
     */
    private function makeDirectoryIfNotExists(string $path)
    {
        if (!file_exists($path)) {
            FileHelper::createDirectory($path);
        }
    }

    /**
     * ユーザー情報を標準出力
     * @param PolletUser $user
     */
    private function outputUserInfo($user)
    {
        echo '-- user information --' . PHP_EOL;
        echo 'id        : ' . $user->id . PHP_EOL;
        echo 'pollet_id : ' . $user->user_code_secret . PHP_EOL;
        echo 'cedyna_id : ' . $user->cedyna_id . PHP_EOL;
        echo 'password  : ' . $user->password . PHP_EOL;
        echo 'status    : ' . $user->registration_status . PHP_EOL;
    }

    /**
     * ポイントサイトのユーザー認証済みデータの作成
     *
     * @param  PolletUser   $user
     * @param  ChargeSource $chargeSource
     * @return PointSiteToken
     */
    private function setPointSiteToken(PolletUser $user, ChargeSource $chargeSource)
    {
        $pointSiteToken = new PointSiteToken();
        $pointSiteToken->pollet_user_id  = $user->id;
        $pointSiteToken->point_site_code = $chargeSource->pointSite->point_site_code;
        $pointSiteToken->token           =  $this->faker->regexify('[A-Z0-9._%+-]{10}');
        $pointSiteToken->save();

        return $pointSiteToken;
    }
}
