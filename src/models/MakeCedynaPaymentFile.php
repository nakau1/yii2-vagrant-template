<?php
namespace app\models;

use app\models\cedyna_files\CedynaFile;
use app\models\cedyna_files\CedynaPaymentFile;
use Yii;
use yii\base\Model;

class MakeCedynaPaymentFile extends Model
{
    private $sendDirectory;
    private $processingDirectory;
    private $archiveCompleteDirectory;

    /**
     * MakeCedynaPaymentFile constructor.
     *
     * @param string $sendDirectory 送信するファイルを入れるディレクトリ
     * @param string $processingDirectory 処理中のファイルを入れておくディレクトリ
     * @param string $archiveCompleteDirectory 処理が完了したファイルをとっておくディレクトリ
     * @param array $config
     */
    public function __construct(
        string $sendDirectory,
        string $processingDirectory,
        string $archiveCompleteDirectory,
        $config = []
    ) {
        parent::__construct($config);

        $this->sendDirectory = $sendDirectory;
        $this->processingDirectory = $processingDirectory;
        $this->archiveCompleteDirectory = $archiveCompleteDirectory;
    }

    /**
     * チャージ申請履歴から入金ファイルを作成
     *
     * @param string $batchName
     */
    public function run(string $batchName)
    {
        Yii::info('begin processing make payment file: '.$batchName);

        $targetCharges = $this->findReadyCharges();
        if (count($targetCharges) === 0) {
            Yii::info('data does not exist : '.$batchName);

            return;
        }
        // 途中で中断しても対象データを引っ張れるように、いったん作成中にする
        $this->updateChargesToMakingFile($targetCharges);

        $paymentFile = $this->createPaymentFileInto($this->processingDirectory, $targetCharges);
        if ($paymentFile === null) {
            Yii::error('failure to file creation : '.$batchName);

            return;
        }
        $this->updateChargesToApplied($targetCharges);

        $paymentFile->copyTo($this->sendDirectory);
        $paymentFile->moveTo($this->archiveCompleteDirectory);
    }

    /**
     * 指定したディレクトリに入金ファイルを作成する
     *
     * @param string $directory
     * @param array $charges
     * @return CedynaFile|null 作成したファイル。失敗した場合null
     */
    private function createPaymentFileInto(string $directory, array $charges)
    {
        $now = date('YmdHis');
        if (false === touch("{$directory}/{$now}.csv")) {
            return null;
        }

        $paymentFile = new CedynaFile("{$directory}/{$now}.csv");
        $this->outputFirstRow($now, $paymentFile);
        $this->outputHeader($paymentFile);
        $this->outputData($paymentFile, $charges);
        $this->outputLastRow($paymentFile);

        return $paymentFile;
    }

    /**
     * 処理待ち状態のチャージ申請履歴レコード
     *
     * @return \app\models\ChargeRequestHistory[]|array
     */
    private function findReadyCharges()
    {
        return ChargeRequestHistory::find()->where(
            ['processing_status' => ChargeRequestHistory::STATUS_READY]
        )->all();
    }

    /**
     * 指定したチャージ申請履歴のレコードをすべてファイル作成中状態に更新する
     *
     * @param ChargeRequestHistory[] $charges
     */
    private function updateChargesToMakingFile(array $charges)
    {
        $ids = [];
        foreach ($charges as $charge) {
            $ids[] = $charge->id;
        }

        ChargeRequestHistory::updateAll(
            ['processing_status' => ChargeRequestHistory::STATUS_IS_MAKING_PAYMENT_FILE],
            ['in', 'id', $ids]
        );
    }

    /**
     * 指定したチャージ申請履歴のレコードをすべてチャージ申請済み状態に更新する
     *
     * @param ChargeRequestHistory[] $charges
     */
    private function updateChargesToApplied(array $charges)
    {
        $ids = [];
        foreach ($charges as $charge) {
            $ids[] = $charge->id;
        }

        ChargeRequestHistory::updateAll(
            ['processing_status' => ChargeRequestHistory::STATUS_APPLIED_CHARGE],
            ['in', 'id', $ids]
        );
    }

    /**
     * データ行の行数
     *
     * @param CedynaFile $file
     * @return int
     */
    private function countDataRow(CedynaFile $file)
    {
        $count = 0;
        foreach ($file->readDataLinesAll() as $line) {
            $count = ++$count;
        }

        return $count;
    }

    /**
     * 開始行を出力
     *
     * @param string $now
     * @param CedynaFile $paymentFile
     */
    private function outputFirstRow(string $now, CedynaFile $paymentFile)
    {
        $startRowDate = date('Y/m/d H:i:s', strtotime($now));
        $content = '"S",'.'"'.$startRowDate.'"'."\n";
        $paymentFile->setSaveContent($content);
        $paymentFile->save();
    }

    /**
     * ヘッダ行を出力
     *
     * @param CedynaFile $paymentFile
     */
    private function outputHeader(CedynaFile $paymentFile)
    {
        $content = '"H","入金種別","イシュアコード","提携先コード","カード種別区分","会員グループ番号","会員番号","カードID","入金額","加盟店名（チャージ理由）","処理結果","エラーコード"'."\n";
        $paymentFile->setSaveContent($content);
        $paymentFile->save(true);
    }

    /**
     * データ行を出力
     *
     * @param CedynaFile $paymentFile
     * @param ChargeRequestHistory[] $chargeRequests
     */
    private function outputData(CedynaFile $paymentFile, array $chargeRequests)
    {
        foreach ($chargeRequests as $row) {
            Yii::info("begin output to file : id : {$row->id}");

            $cedynaPaymentFile = new CedynaPaymentFile();

            $content = implode(',', $cedynaPaymentFile->outputRow($row))."\n";
            $paymentFile->setSaveContent($content);
            $paymentFile->save(true);

            Yii::info("finish output to file : id : {$row->id}");
        }
    }

    /**
     * 終端行を出力
     *
     * @param CedynaFile $paymentFile
     */
    private function outputLastRow(CedynaFile $paymentFile)
    {
        $content = '"E","'.$this->countDataRow($paymentFile).'"'."\n";
        $paymentFile->setSaveContent($content);
        $paymentFile->save(true);
    }
}
