<?php
/* @var $this \app\views\View */
/* @var $percentage float*/

use app\assets\TopAsset;
use app\helpers\Format;
use app\controllers\ChargeController;
use yii\helpers\Html;
use yii\helpers\Url;

$this->contentsHtmlClass = ($this->user->isActivatedUser()) ?
    'after_activating' :
    'before_activating' ;

$this->isShowedHeaderBar = false;
$this->isGrayBackground = false;
$this->title = 'Pollet';

TopAsset::register($this);
?>
<!--ドーナツチャート-->
<div class="card_box clearfix">
    <div class="card_box_inner">
        <canvas class="graph" id="canvas" height="180" width="180"></canvas>
        <div class="balance_box">
            <p>残高</p>
            <p class="balance_price"><span><?= Format::formattedNumber($this->user->myChargedValue) ?></span>円</p>
            <p class="btn_reload"><?= Html::a('表示の更新', Url::to('/top')) ?></p>
        </div>
        <?php if ($this->user->isChargeRequested() || $this->user->isWaitingIssue()): ?>
            <div class="card_btn_box make_box">
                <p class="btn_red btn_before"><?= Html::a('カードを作る', Url::to('issuance')) ?></p>
            </div>
        <?php elseif ($this->user->isIssued()): ?>
            <div class="card_btn_box start_box">
                <p class="btn_red btn_before"><?= Html::a('使いはじめる', Url::to('auth/sign-in')) ?></p>
            </div>
        <?php elseif ($this->user->isActivatedUser()): ?>
            <div class="card_btn_box">
                <p class="btn_red btn_charge"><?= Html::a('チャージする', Url::to('charge/list')) ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
<!--チャージサイト追加-->
<div class="site_box clearfix">
    <p class="btn_until_card"><a href=""><span>カードが届くまで</span></a></p>
    <?php if ($this->user->isActivatedUser()): ?>
    <div class="slider multiple-items site_add">
        <?php foreach ($this->user->pointSites as $pointSite): ?>
        <div class="site_add_inner">
            <div class="site_rogo_img">
                <a href="<?= Url::to(['charge/price', 'code' => $pointSite->point_site_code, 'mode' => ChargeController::PRICE_MODE_NORMAL]) ?>">
                    <?= Html::img($pointSite->icon_image_url) ?>
                </a>
            </div>
            <p class="site_add_text">チャージ可能残高</p>
            <p class="site_add_price"><span><?= Format::formattedNumber($pointSite->myValidPoint) ?></span>円分</p>
        </div>
        <?php endforeach; ?>
        <div class="site_add_inner">
            <div class="site_rogo_img"><a href="<?= Url::to(['charge/list']) ?>"><?= $this->img('img_noimage') ?></a></div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
$per = (int)($percentage * 100);
$transparent = 'rgba(255,255,255,0.0)';
$color = $transparent;
$colorDefines = [
    20  => '#b02321',
    40  => '#dcb32e',
    60  => '#cdc95e',
    80  => '#ca8730',
    100 => '#7faa45',
];
foreach ($colorDefines as $n => $colorDefine) {
    if ($per <= $n) {
        $color = $colorDefine;
        break;
    }
}
?>
<script>
    var doughnutData = [
        {
            //チャージ残分(%)
            value : <?= $per ?>,
            color : "<?= $color ?>"
        },
        {
            //チャージ減分(%)
            value: <?= 100 - $per ?>,
            color: "<?= $transparent ?>"
        }
    ];
</script>