<?php
namespace tests\unit\fixtures;

use Faker;
use Yii;
use yii\helpers\FileHelper;

class ReceiveNumberedCedynaIdFileFixture extends PolletDbFixture
{
    public $receivedDirectory;
    public $receivedFileName;
    public $receivedFilePath;
    public $retryDirectory;
    public $retryFileName;
    public $retryFilePath;
    public $processingDirectory;
    public $completeDirectory;
    public $集信したpolletId = [10001, 10002];
    public $集信したセディナID = ['0001123456789001', '0001123456789002'];
    public $リトライするpolletId = [10003, 10004];
    public $リトライするセディナID = ['0001123456789003', '0001123456789004'];

    public $invalidLinesFileName;
    public $invalidLinesFilePath;
    public $ヘッダ行に存在するpolletId = 10005;
    public $データ行に存在するpolletId = 10006;
    public $トレーラ行に存在するpolletId = 10007;

    public $invalidUsersFileName;
    public $invalidUsersFilePath;
    public $存在しないpolletId = 10008;
    public $初回チャージ未処理でないpolletId = 10009;
    public $チャージ申請履歴が存在しないpolletId = 10010;

    public $duplicatedCedynaIdsFileName;
    public $duplicatedCedynaIdsFilePath;
    public $重複されるセディナIDを持つpolletId = 10011;
    public $重複するセディナIDを持つpolletId = 10012;

    public $invalidCedynaIdsFileName;
    public $invalidCedynaIdsFilePath;
    public $セディナIDに数字以外を含むpolletId = 10013;
    public $セディナIDが0より小さいpolletId = 10014;
    public $セディナIDが16桁より大きいpolletId = 10015;

    public function init()
    {
        $this->receivedDirectory = Yii::$app->runtimePath . '/hulft/recv/receive_numbered_cedyna_id';
        $this->receivedFileName = '160901123040.csv';
        $this->receivedFilePath = "{$this->receivedDirectory}/{$this->receivedFileName}";

        $this->retryDirectory = Yii::$app->runtimePath . '/hulft/app/receive_numbered_cedyna_id/retry';
        $this->retryFileName = '160801123040.csv';
        $this->retryFilePath = "{$this->retryDirectory}/{$this->retryFileName}";

        $this->processingDirectory = Yii::$app->runtimePath . '/hulft/app/receive_numbered_cedyna_id/processing';
        $this->completeDirectory = Yii::$app->runtimePath . '/hulft/app/receive_numbered_cedyna_id/complete';

        $this->invalidLinesFileName = '160101000000.csv';
        $this->invalidLinesFilePath = "{$this->receivedDirectory}/{$this->invalidLinesFileName}";
        $this->invalidUsersFileName = '160102000000.csv';
        $this->invalidUsersFilePath = "{$this->receivedDirectory}/{$this->invalidUsersFileName}";
        $this->duplicatedCedynaIdsFileName = '160103000000.csv';
        $this->duplicatedCedynaIdsFilePath = "{$this->receivedDirectory}/{$this->duplicatedCedynaIdsFileName}";

        $this->invalidCedynaIdsFileName = '160104000000.csv';
        $this->invalidCedynaIdsFilePath = "{$this->receivedDirectory}/{$this->invalidCedynaIdsFileName}";
    }

    public function load()
    {
        FileHelper::createDirectory($this->completeDirectory);
        FileHelper::createDirectory($this->processingDirectory);
        FileHelper::createDirectory($this->receivedDirectory);
        FileHelper::createDirectory($this->retryDirectory);

        $this->makeReceivedCsv();
        $this->makeRetryCsv();
        $this->makeCsvIncludesInvalidLines();
        $this->makeCsvIncludesInvalidUsers();
        $this->makeCsvDuplicatedCedynaIds();
        $this->makeCsvInvalidCedynaIds();
    }

    private function makeReceivedCsv()
    {
        $csv = <<<CSV
"S","2016/09/01 12:30:40"
"D","409336123456789000","{$this->集信したセディナID[0]}","{$this->集信したpolletId[0]}","abcd","20160801","20160805","20"
"D","409336123456789000","{$this->集信したセディナID[1]}","{$this->集信したpolletId[1]}","abcd","20160801","20160805","20"
"E",       2
CSV;
        file_put_contents($this->receivedFilePath, mb_convert_encoding($csv, 'SJIS'));
    }

    private function makeRetryCsv()
    {
        $csv = <<<CSV
"S","2016/08/01 12:30:40"
"D","409336123456789000","{$this->リトライするセディナID[0]}","{$this->リトライするpolletId[0]}","abcd","20160801","20160805","20"
"D","409336123456789000","{$this->リトライするセディナID[1]}","{$this->リトライするpolletId[1]}","abcd","20160801","20160805","20"
"E",       2
CSV;
        file_put_contents($this->retryFilePath, mb_convert_encoding($csv, 'SJIS'));
    }

    private function makeCsvIncludesInvalidLines()
    {
        $csv = <<<CSV
"S","409336123456789000","0123456789012300","{$this->ヘッダ行に存在するpolletId}","abcd","20160801","20160805","20"
"D","409336123456789000","0123456789012301","{$this->データ行に存在するpolletId}","abcd","20160801","20160805","20"
"E","409336123456789000","0123456789012302","{$this->トレーラ行に存在するpolletId}","abcd","20160801","20160805","20"
CSV;
        file_put_contents($this->invalidLinesFilePath, mb_convert_encoding($csv, 'SJIS'));
    }

    private function makeCsvIncludesInvalidUsers()
    {
        $csv = <<<CSV
"S","2016/01/02 00:00:00"
"D","409336123456789000","0123456789012303","{$this->存在しないpolletId}","abcd","20160801","20160805","20"
"D","409336123456789000","0123456789012304","{$this->初回チャージ未処理でないpolletId}","abcd","20160801","20160805","20"
"D","409336123456789000","0123456789012305","{$this->チャージ申請履歴が存在しないpolletId}","abcd","20160801","20160805","20"
"E",       3
CSV;

        file_put_contents($this->invalidUsersFilePath, mb_convert_encoding($csv, 'SJIS'));
    }

    private function makeCsvDuplicatedCedynaIds()
    {
        $csv = <<<CSV
"S","2016/01/02 00:00:00"
"D","409336123456789000","0123456789012306","{$this->重複されるセディナIDを持つpolletId}","abcd","20160801","20160805","20"
"D","409336123456789000","0123456789012306","{$this->重複するセディナIDを持つpolletId}","abcd","20160801","20160805","20"
"E",       2
CSV;
        file_put_contents($this->duplicatedCedynaIdsFilePath, mb_convert_encoding($csv, 'SJIS'));
    }

    private function makeCsvInvalidCedynaIds()
    {
        $csv = <<<CSV
"S","2016/01/02 00:00:00"
"D","409336123456789000","012345678901abc2","{$this->セディナIDに数字以外を含むpolletId}","abcd","20160801","20160805","20"
"D","409336123456789000","-000000000000001","{$this->セディナIDが0より小さいpolletId}","abcd","20160801","20160805","20"
"D","409336123456789000","12345678901234567","{$this->セディナIDが16桁より大きいpolletId}","abcd","20160801","20160805","20"
"E",       3
CSV;
        file_put_contents($this->invalidCedynaIdsFilePath, mb_convert_encoding($csv, 'SJIS'));
    }

    public function unload()
    {
        FileHelper::removeDirectory($this->completeDirectory);
        FileHelper::removeDirectory($this->processingDirectory);
        FileHelper::removeDirectory($this->receivedDirectory);
        FileHelper::removeDirectory($this->retryDirectory);
    }
}
