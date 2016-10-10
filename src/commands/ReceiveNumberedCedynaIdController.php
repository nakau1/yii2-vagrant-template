<?php
namespace app\commands;

use app\helpers\File;
use app\models\cedyna_files\CedynaFile;
use app\models\ReceiveNumberedCedynaId;
use Yii;

class ReceiveNumberedCedynaIdController extends BatchController
{
    private $receivedFilesDirectory;
    private $retryDirectory;
    private $processingDirectory;
    private $completeDirectory;

    public function init()
    {
        parent::init();

        $this->receivedFilesDirectory = Yii::$app->runtimePath.'/hulft/recv/receive_numbered_cedyna_id';
        $this->retryDirectory = Yii::$app->runtimePath.'/hulft/app/receive_numbered_cedyna_id/retry';
        $this->processingDirectory = Yii::$app->runtimePath.'/hulft/app/receive_numbered_cedyna_id/processing';
        $this->completeDirectory = Yii::$app->runtimePath.'/hulft/app/receive_numbered_cedyna_id/complete';

        File::makeDirectoryIfNotExists($this->receivedFilesDirectory);
        File::makeDirectoryIfNotExists($this->retryDirectory);
        File::makeDirectoryIfNotExists($this->processingDirectory);
        File::makeDirectoryIfNotExists($this->completeDirectory);
    }

    /**
     * セディナID発番通知受け取り処理
     * HULFTで受け取ったセディナID発番通知に対して、以下の処理を行う。
     * - 対象のユーザー情報にセディナIDを追加
     * - 対象のユーザー情報の登録状態を「カード未認証」に更新
     * - 対象のチャージ申請データの処理状態を「処理待ち」に更新
     */
    public function actionIndex()
    {
        $receivedFiles = CedynaFile::findAll($this->receivedFilesDirectory);
        $retryFiles = CedynaFile::findAll($this->retryDirectory);
        /** @var CedynaFile[] $files */
        $files = array_merge($receivedFiles, $retryFiles);

        // 1つのファイルを多重処理しないようにすべて処理中ディレクトリに移動する
        foreach ($files as $file) {
            $file->moveTo($this->processingDirectory);
        }

        $model = new ReceiveNumberedCedynaId($this->completeDirectory, $this->retryDirectory);
        foreach ($files as $file) {
            $model->acceptFile($file);
        }
    }
}
