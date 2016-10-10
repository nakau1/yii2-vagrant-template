<?php

namespace app\controllers;

use app\models\forms\IssuanceForm;
use app\models\PolletUser;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Yii;
use yii\web\BadRequestHttpException;

/**
 * カード発行手続き
 * Class IssuanceController
 * @package app\controllers
 */
class IssuanceController extends CommonController
{
    /**
     * 6. メールアドレス入力
     * @return string
     * @throws BadRequestHttpException
     * @throws InternalErrorException
     */
    public function actionIndex()
    {
        if (!$this->checkAccess()) {
            throw new BadRequestHttpException('このサイトは閲覧できません');
        }

        $formModel = new IssuanceForm();

        if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
            // 認証メール送信
            if ($formModel->send()) {
                $trans = Yii::$app->db->beginTransaction();
                try {
                    $this->authorizedUser->registration_status = PolletUser::STATUS_WAITING_ISSUE;
                    $this->authorizedUser->mail_address = $formModel->mail_address;
                    if (!$this->authorizedUser->save()) {
                        throw new \Exception('failed change to waiting-issue.');
                    }
                    $trans->commit();
                } catch (\Exception $e) {
                    $trans->rollBack();
                    throw new InternalErrorException('処理に失敗しました');
                }
                // 7. カード発行手続き完了画面を表示
                return $this->render('reception');
            } else {
                throw new InternalErrorException('処理に失敗しました');
            }
        }

        return $this->render('index', [
            "formModel" => $formModel,
        ]);
    }

    /**
     * カード発行手続きが可能なユーザかどうかを判定する
     * @return bool
     */
    private function checkAccess()
    {
        return ($this->authorizedUser->isChargeRequested() || $this->authorizedUser->isWaitingIssue());
    }
}
