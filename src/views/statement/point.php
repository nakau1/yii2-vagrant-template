<?php
/* @var $this \app\views\View */
/* @var $currentYear integer */
/* @var $currentMonth integer */
/* @var $nextMonthString string */
/* @var $prevMonthString string */
/* @var $pointHistories app\models\PolletPointHistory[] */

use app\helpers\Format;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

$this->isGrayBackground = false;
$this->title = 'Polletポイント';
?>
<?php Pjax::begin() ?>
<div class="main_box">
    <div class="details_head clearfix">
        <?php if ($prevMonthString): ?>
            <p class="fl_l details_head_arrw">
                <a href="<?= Url::to(['point', 'month' => $prevMonthString]) ?>">
                    <?= $this->img('ico_arrw_gray01') ?>
                </a>
            </p>
        <?php endif; ?>
        <p class="details_head_text"><span><?= $currentYear ?>年<?= $currentMonth ?>月</span></p>
        <?php if ($nextMonthString): ?>
            <p class="fl_r details_head_arrw">
                <a href="<?= Url::to(['point', 'month' => $nextMonthString]) ?>">
                    <?= $this->img('ico_arrw_gray02') ?>
                </a>
            </p>
        <?php endif; ?>
    </div>
    <div class="property_point_box clearfix">
        <p class="property_point_text">所有ポイント</p>
        <p class="property_point_price"><span><?= Format::formattedNumber($this->user->myChargedValue) ?></span>pt</p>
    </div>
</div>
<table cellpadding="0" cellspacing="0" class="details_table">
    <?php foreach ($pointHistories as $i => $pointHistory): ?>
    <tr<?php if ($pointHistory->isCharge): ?> class="charg_tr"<?php endif; ?>>
        <td class="day_td"><?= Format::formattedDay(strtotime($pointHistory->trading_date)) ?></td>
        <td class="shop_td"><?= Html::encode($pointHistory->title) ?></td>
        <td class="point_td"><?= Format::formattedPoint($pointHistory->point) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php Pjax::end() ?>