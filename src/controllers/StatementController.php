<?php

namespace app\controllers;

use app\helpers\YearMonth;
use app\models\cedyna_my_pages\CedynaMyPageWithCache;
use app\models\ChargeRequestHistory;
use app\models\PolletPointHistory;
use app\models\TradingHistory;
use yii\base\Exception;
use yii\web\BadRequestHttpException;

/**
 * Class StatementController
 * @package app\controllers
 */
class StatementController extends CommonController
{
    /**
     * 19. 利用明細
     * @param string|null $month 'yymm'の形式の月(2016年9月は'1609')
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionTrading($month = null)
    {
        $cedynaWithCache = CedynaMyPageWithCache::getInstance();
        // TODO: 毎回ログインは要らなくなる予定
        if (!$cedynaWithCache->login($this->authorizedUser->cedyna_id, $this->authorizedUser->password)) {
            $this->redirect('/auth/sign-out');
        }

        if (is_null($month)) {
            $month = date('ym');
        }

        try {
            $tradingHistories = $cedynaWithCache->tradingHistories($month);
            $chargeHistories  = ChargeRequestHistory::find()->active()->atMonth($month)->all();

            $tradingHistories = array_merge(
                $tradingHistories,
                TradingHistory::createFromChargeRequestHistories($chargeHistories)
            );

            $tradingHistories = TradingHistory::sortByTradingDate($tradingHistories);
        } catch (Exception $e) {
            throw new BadRequestHttpException('');
        }

        list($y, $m) = YearMonth::divideMonthString($month);
        return $this->render('trading', [
            'tradingHistories' => $tradingHistories,
            'currentYear'      => $y,
            'currentMonth'     => $m,
            'nextMonthString'  => YearMonth::getNextMonthString($month),
            'prevMonthString'  => YearMonth::getPrevMonthString($month),
        ]);
    }

    /**
     * 19. ポイント明細
     * @param string|null $month 'yymm'の形式の月(2016年9月は'1609')
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionPoint($month = null)
    {
        if (is_null($month)) {
            $month = date('ym');
        }

        $this->redirectIfNoneChargedValue();

        $pointHistories = PolletPointHistory::find()->ordered()->atMonth($month)->all();

        list($y, $m) = YearMonth::divideMonthString($month);
        return $this->render('point', [
            'pointHistories'  => $pointHistories,
            'currentYear'     => $y,
            'currentMonth'    => $m,
            'nextMonthString' => YearMonth::getNextMonthString($month),
            'prevMonthString' => YearMonth::getPrevMonthString($month),
        ]);
    }
}
