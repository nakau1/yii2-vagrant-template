<?php
namespace app\commands;

use app\helpers\File;
use app\models\BatchManagement;
use app\models\MakeCedynaPaymentFile;
use Yii;

class MakeCedynaPaymentFileController extends BatchController
{
    private $sendFilesDirectory;
    private $processingFilesDirectory;
    private $completeFilesDirectory;

    public function init()
    {
        parent::init();

        $this->sendFilesDirectory = Yii::$app->runtimePath.'/hulft/send/send_payment_file';
        $this->processingFilesDirectory = Yii::$app->runtimePath.'/hulft/app/send_payment_file/processing';
        $this->completeFilesDirectory = Yii::$app->runtimePath.'/hulft/app/send_payment_file/complete';

        File::makeDirectoryIfNotExists($this->sendFilesDirectory);
        File::makeDirectoryIfNotExists($this->processingFilesDirectory);
        File::makeDirectoryIfNotExists($this->completeFilesDirectory);
    }

    /**
     * チャージ申請履歴から入金ファイルを作成
     */
    public function actionIndex()
    {
        //多重起動防止のため
        if (BatchManagement::isActive($this->getRoute())) {
            Yii::warning('During start-up in the other process : '.$this->getRoute());

            return;
        }
        BatchManagement::activate($this->getRoute());

        $model = new MakeCedynaPaymentFile(
            $this->sendFilesDirectory,
            $this->processingFilesDirectory,
            $this->completeFilesDirectory
        );
        $model->run($this->getRoute());

        //多重起動防止のため終了記録を更新
        BatchManagement::inactivate($this->getRoute());
    }
}
