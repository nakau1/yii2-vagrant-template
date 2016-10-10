<?php

namespace tests\unit\models;

use app\models\ChargeRequestHistory;
use app\models\cedyna_files\CedynaFile;
use tests\unit\fixtures\BacthManagementFixture;
use tests\unit\fixtures\CedynaPaymentFileWithoutReadyUserFixture;
use tests\unit\fixtures\CedynaPaymentFileFixture;
use tests\unit\fixtures\PolletDbFixture;
use Yii;
use yii\codeception\TestCase;

class MakeCedynaPaymentFileControllerTest extends TestCase
{
    public $appConfig = '@app/config/console.php';
    private $savedDb;
    private $batchName = 'make-cedyna-payment-file/index';

    public function setUp()
    {
        parent::setUp();
        $this->savedDb = Yii::$app->getDb();
        $cedynaPaymentFileFixture = new CedynaPaymentFileFixture();
        $cedynaPaymentFileFixture->removeDir();
    }

    public function fixtures()
    {
        return [
            'fixture' => PolletDbFixture::class,
        ];
    }

    private function runBatch()
    {
        Yii::$app->runAction($this->batchName);
    }

    /**
     * @test
     */
    public function チャージ申請履歴に処理待ちデータがあった場合入金ファイルを作成する()
    {
        //処理待ちのユーザーのデータを用意
        $cedynaPaymentFixture = new CedynaPaymentFileFixture();
        $cedynaPaymentFixture->load();

        $this->runBatch();

        $chargeRequestHistory = ChargeRequestHistory::find()->where(['id' => 100003])->one();
        $this->assertEquals(ChargeRequestHistory::STATUS_APPLIED_CHARGE,
            $chargeRequestHistory->processing_status);

        //バッチ処理開始前からファイル作成中ステータスの人は処理されていない理由を調査してからの対応が必要だから対象にしない
        $chargeRequestHistory = ChargeRequestHistory::find()->where(['id' => 100001])->one();
        $this->assertEquals(ChargeRequestHistory::STATUS_IS_MAKING_PAYMENT_FILE,
            $chargeRequestHistory->processing_status);

        $this->assertEmpty(CedynaFile::findAll($cedynaPaymentFixture->作業ディレクトリ));

        //中身のデータの担保は別のテストケースで行う
        $this->assertNotEmpty(CedynaFile::findAll($cedynaPaymentFixture->完了ディレクトリ));
        $this->assertNotEmpty(CedynaFile::findAll($cedynaPaymentFixture->HULFT配信用ディレクトリ));
    }

    /**
     * @test
     */
    public function 他プロセスで実行中だった場合終了する()
    {
        //バッチ管理テーブルを他プロセスで実行中の状態にする
        $bacthManagementFixture = new BacthManagementFixture($this->batchName);
        $bacthManagementFixture->load();
        //処理待ちユーザーの作成
        $CedynaPaymentFileFixture = new CedynaPaymentFileFixture();
        $CedynaPaymentFileFixture->load();

        $this->runBatch();

        //入金ファイル作成待ちのユーザーが更新されていないことを確認
        $chargeRequestHistory = ChargeRequestHistory::find()->where(['id' => 100003])->one();
        $this->assertEquals(ChargeRequestHistory::STATUS_READY,
            $chargeRequestHistory->processing_status);
    }

    /**
     * @test
     */
    public function 対象データが0件だった場合終了する_処理状態が処理待ち以外のデータのみ()
    {
        //入金ファイル作成中のユーザーのデータを用意
        $userNotReady = new CedynaPaymentFileWithoutReadyUserFixture();
        $userNotReady->load();

        $this->runBatch();

        //入金ファイル作成待ちのユーザーが更新されていないことを確認
        $chargeRequestHistory = ChargeRequestHistory::find()->where(['id' => 100001])->one();
        $this->assertEquals(ChargeRequestHistory::STATUS_IS_MAKING_PAYMENT_FILE,
            $chargeRequestHistory->processing_status);
    }

    /**
     * @test
     */
    public function 対象データが0件だった場合終了する_テーブルが空()
    {
        $this->runBatch();

        $this->assertEquals(0, ChargeRequestHistory::find()->count());
    }

    /**
     * テストケース9 https://github.com/oz-sysb/neroblu/issues/61
     * @test
     */
    public function 入金ファイル作成中のデータ件数だけデータ行が出力される_データ1件()
    {
        $CedynaPaymentFileFixture = new CedynaPaymentFileFixture();
        $CedynaPaymentFileFixture->load();
        $expectedCount = 1;

        $this->runBatch();

        $writtenFile = CedynaFile::findAll($CedynaPaymentFileFixture->HULFT配信用ディレクトリ)[0];
        $actualCount = 0;
        foreach ($writtenFile->readDataLinesAll() as $line) {
            $actualCount++;
        }
        $this->assertEquals($expectedCount, $actualCount);
    }

    /**
     * テストケース10 https://github.com/oz-sysb/neroblu/issues/61
     * @test
     */
    public function 入金ファイル作成中のデータ件数だけデータ行が出力される_データ複数件()
    {
        $CedynaPaymentFileFixture = new CedynaPaymentFileFixture();
        $CedynaPaymentFileFixture->loadMultipleReadyUsers();
        $expectedCount = 2;

        $this->runBatch();

        $writtenFile = CedynaFile::findAll($CedynaPaymentFileFixture->HULFT配信用ディレクトリ)[0];
        $actualCount = 0;
        foreach ($writtenFile->readDataLinesAll() as $line) {
            $actualCount++;
        }
        $this->assertEquals($expectedCount, $actualCount);
    }

    /**
     * テストケース11 https://github.com/oz-sysb/neroblu/issues/61
     * @test
     */
    public function 処理したデータ行の数が終端行に出力される_データ1件()
    {
        $CedynaPaymentFileFixture = new CedynaPaymentFileFixture();
        $CedynaPaymentFileFixture->load();
        $expectedCount = 1;

        $this->runBatch();

        $writtenFile = CedynaFile::findAll($CedynaPaymentFileFixture->HULFT配信用ディレクトリ)[0];
        $lastLine = null;
        foreach ($writtenFile->readLinesAll() as $line) {
            if ($line[0] === 'E') {
                $lastLine = $line;
            }
        }
        // 終端行の2カラム目
        $actualCount = intval($lastLine[1]);
        $this->assertEquals($expectedCount, $actualCount);
    }

    /**
     * テストケース12 https://github.com/oz-sysb/neroblu/issues/61
     * @test
     */
    public function 処理したデータ行の数が終端行に出力される_データ複数件()
    {
        $CedynaPaymentFileFixture = new CedynaPaymentFileFixture();
        $CedynaPaymentFileFixture->loadMultipleReadyUsers();
        $expectedCount = 2;

        $this->runBatch();

        $writtenFile = CedynaFile::findAll($CedynaPaymentFileFixture->HULFT配信用ディレクトリ)[0];
        $lastLine = null;
        foreach ($writtenFile->readLinesAll() as $line) {
            if ($line[0] === 'E') {
                $lastLine = $line;
            }
        }
        // 終端行の2カラム目
        $actualCount = intval($lastLine[1]);
        $this->assertEquals($expectedCount, $actualCount);
    }
}