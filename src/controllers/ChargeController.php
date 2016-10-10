<?php

namespace app\controllers;

use app\models\ChargeRequestHistory;
use app\models\forms\ChargePriceForm;
use app\models\point_site_cooperation\PointSiteCooperation;
use app\models\PointSite;
use app\models\PointSiteToken;
use app\models\PolletUser;
use Yii;
use yii\base\Exception;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class ChargeController
 * @package app\controllers
 */
class ChargeController extends CommonController
{
    const PRICE_MODE_FIRST  = 'first';
    const PRICE_MODE_NORMAL = 'charge';

    /**
     * 認証からのリダイレクト先
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionIndex()
    {
        // ユーザがチャージできるステータスかどうかの判定と、次画面への遷移分岐
        switch ($this->authorizedUser->registration_status) {
            case PolletUser::STATUS_NEW_USER:  $mode = self::PRICE_MODE_FIRST;  break;
            case PolletUser::STATUS_ACTIVATED: $mode = self::PRICE_MODE_NORMAL; break;
            default:
                throw new BadRequestHttpException('チャージの権限がありません');
                break;
        }

        //TODO: 外部認証サイトから送られてくる情報仕様は未定。サイトコードとトークンは必須だと思われる。一旦、仮にGET値から取得する
        $token    = Yii::$app->request->get('token');
        $siteCode = Yii::$app->request->get('site_code');

        if (!$token || !$siteCode) {
            throw new BadRequestHttpException('必要なパラメータがありません');
        }

        $pointSiteToken = new PointSiteToken();
        if (!$pointSiteToken->add($this->authorizedUser->id, $token, $siteCode)) {
            throw new BadRequestHttpException('処理に失敗しました');
        }

        // 初回チャージの場合は「初回サイト認証完了済」にステータス変更
        if ($this->authorizedUser->isNewUser()) {
            $this->authorizedUser->updateStatus(PolletUser::STATUS_SITE_AUTHENTICATED);
        }

        return $this->redirect('price?code=' . $pointSiteToken->pointSite->point_site_code . '&mode=' . $mode);
    }

    /**
     * 30. チャージ金額確認
     * 31. チャージ金額選択
     * @param string $code 提携サイトCode
     * @param string $mode 初回チャージ('first') or 通常チャージ('charge')
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionPrice($code = '', $mode = self::PRICE_MODE_NORMAL)
    {
        if (!$this->checkAccessActionPrice($mode)) {
            throw new BadRequestHttpException('このサイトは閲覧できません');
        }

        $pointSite = $this->authorizedUser->findMyPointSite($code);
        if (!$pointSite) {
            throw new BadRequestHttpException('このサイトは閲覧できません');
        }
        if($mode === self::PRICE_MODE_NORMAL){
            //初回チャージ以外はカード発行手数料はかからないので初期化
            $pointSite->chargeSource->card_issue_fee = 0;
        }

        $pointSiteRemain = 5000; // TODO: API経由でポイントサイトの残高(Pサイト残高)を取得する予定
        $chargeRemain = $pointSiteRemain - $pointSite->chargeSource->card_issue_fee;

        $formModel = new ChargePriceForm();
        $formModel->price        = $pointSite->chargeSource->min_value;
        $formModel->chargeRemain = $chargeRemain;
        $formModel->minValue     = $pointSite->chargeSource->min_value;
        $formModel->cardIssueFee = $pointSite->chargeSource->card_issue_fee;

        return $this->render('price', [
            'mode'         => $mode,
            'formModel'    => $formModel,
            'pointSite'    => $pointSite,
            'chargeRemain' => $chargeRemain,
            'isFirst'      => $mode === self::PRICE_MODE_FIRST,
        ]);
    }

    /**
     * AJAX経由でチャージ額申請を実行させるアクション
     *
     * POST値
     * {point_site_code} = ポイントサイトCode
     * {price} = 入力金額
     *
     * 戻り値
     * 正常に終了した場合HTTP/200を返す。失敗時はHTTP/400
     *
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionPriceRequest()
    {
        if (!$this->checkAccessActionPriceRequest()) {
            throw new BadRequestHttpException('チャージできる権限がありません');
        }

        /** @var $pointSiteCode string|null */
        $pointSiteCode = Yii::$app->request->post('point_site_code');
        /** @var $price integer|null */
        $price = Yii::$app->request->post('price');

        if (!$pointSiteCode || !$price) {
            throw new BadRequestHttpException('必要なパラメータがありません');
        }

        $pointSite = $this->authorizedUser->findMyPointSite($pointSiteCode);
        if (!$pointSite) {
            throw new BadRequestHttpException('パラメータが不正です');
        }

        $trans = Yii::$app->db->beginTransaction();
        try {
            // API経由でサイトに送信するチャージ額
            if ($this->authorizedUser->isSiteAuthenticated()) {
                //初回チャージ時のみカード発行手数料を合わせて交換する
                $sendPrice = $price + $pointSite->chargeSource->card_issue_fee;
            }else{
                $sendPrice = $price;
            }

            //ポイントサイトへの交換申請処理
            $cashWithdrawal = PointSiteCooperation::exchange($pointSiteCode, $sendPrice, $this->authorizedUser->id);
            if (!$cashWithdrawal) {
                throw new Exception('処理に失敗しました');
            }

            // 申請履歴には手数料別(初回チャージ時のみ)の金額を挿入
            $chargeRequestHistory = new ChargeRequestHistory();
            $chargeRequestHistory->add($this->authorizedUser->id, $cashWithdrawal->id, $price);

            // 初回チャージであれば、'初回チャージ済'ステータスに更新
            if ($this->authorizedUser->isSiteAuthenticated()) {
                $this->authorizedUser->registration_status = PolletUser::STATUS_CHARGE_REQUESTED;
                if (!$this->authorizedUser->save()) {
                    throw new \Exception('failed change status to carge_requested');
                }
            }

            $trans->commit();
        } catch (\Exception $e) {
            $trans->rollBack();
            Yii::error($e);
            throw new BadRequestHttpException('チャージに失敗しました');
        }

        return 'OK';
    }

    /**
     * チャージ完了時に遷移するアクション
     * @throws BadRequestHttpException
     */
    public function actionPriceFinished()
    {
        if ($this->authorizedUser->isChargeRequested()) {
            $this->redirect('../issuance');
        } else if ($this->authorizedUser->isActivatedUser()) {
            $this->redirect('/');
        } else {
            throw new BadRequestHttpException('このサイトは閲覧できません');
        }
    }

    /**
     * 2. チャージ先選択
     * 20. チャージ一覧
     * @return string
     */
    public function actionList()
    {
        return $this->render('list', [
            'pointSites' => PointSite::find()->joinAuthorized()->active()->all(),
        ]);
    }

    /**
     * 21. チャージ先詳細
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDetail($id = 0)
    {
        $pointSite = PointSite::find()->andWhere([
            PointSite::tableName() . '.id' => $id,
        ])->one();

        if (!$pointSite) {
            throw new NotFoundHttpException('指定のチャージ元が見つかりません');
        }

        $this->layout = false;
        return $this->render('detail', [
            "pointSite" => $pointSite,
        ]);
    }

    /**
     * チャージ画面にアクセスできるユーザかどうかを判定する
     * @param $mode string モード
     * @return bool
     */
    private function checkAccessActionPrice($mode)
    {
        // 初回サイト認証完了済のユーザのみ初回チャージが可能
        // アクティベート済ユーザのみ通常チャージが可能
        if (($this->authorizedUser->isSiteAuthenticated() && $mode == self::PRICE_MODE_FIRST)  ||
            ($this->authorizedUser->isActivatedUser()     && $mode == self::PRICE_MODE_NORMAL) ){
            return true;
        }
        return false;
    }

    /**
     * チャージ申請が可能なユーザかどうかを判定する
     * @return bool
     */
    private function checkAccessActionPriceRequest()
    {
        return ($this->authorizedUser->isSiteAuthenticated() || $this->authorizedUser->isActivatedUser());
    }
}
