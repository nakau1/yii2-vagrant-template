<?php
/* @var $this \app\views\View */
/* @var $currentYear integer */
/* @var $currentMonth integer */
/* @var $nextMonthString string */
/* @var $prevMonthString string */
/* @var $tradingHistories app\models\TradingHistory[] */

use app\helpers\Format;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

$this->isGrayBackground = false;
$this->title = '利用明細';
?>
<?php Pjax::begin() ?>
<div class="main_box">
    <div class="details_head clearfix">
        <?php if ($prevMonthString): ?>
            <p class="fl_l details_head_arrw">
                <a href="<?= Url::to(['trading', 'month' => $prevMonthString]) ?>">
                    <?= $this->img('ico_arrw_gray01') ?>
                </a>
            </p>
        <?php endif; ?>
        <p class="details_head_text"><span><?= $currentYear ?>年<?= $currentMonth ?>月</span></p>
        <?php if ($nextMonthString): ?>
            <p class="fl_r details_head_arrw">
                <a href="<?= Url::to(['trading', 'month' => $nextMonthString]) ?>">
                    <?= $this->img('ico_arrw_gray02') ?>
                </a>
            </p>
        <?php endif; ?>
    </div>
</div>
<table cellpadding="0" cellspacing="0" class="details_table">
    <?php foreach ($tradingHistories as $tradingHistory): ?>
        <tr<?php if ($tradingHistory->isCharge): ?> class="charg_tr"<?php endif; ?>>
            <td class="day_td"><?= $tradingHistory->trading_date->format('j') ?>日</td>
            <td class="shop_td"><?= Html::encode($tradingHistory->shop) ?></td>
            <td class="price_td"><?php if ($tradingHistory->isCharge): ?>チャージ<?php else: ?>決済<?php endif; ?><br>
                <span><?= Format::formattedNumber($tradingHistory->spent_value) ?></span>円</td>
        </tr>
    <?php endforeach; ?>
</table>
<?php Pjax::end() ?>
