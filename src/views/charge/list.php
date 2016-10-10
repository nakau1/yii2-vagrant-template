<?php
/* @var $this \app\views\View */
/* @var $pointSites \app\models\PointSite[] */

use app\controllers\ChargeController;
use app\assets\AppJQueryAsset;
use app\helpers\Format;
use yii\helpers\Html;
use yii\helpers\Url;

AppJQueryAsset::register($this);
$this->registerJsFile('/js/app/charge-list.js');

$this->isShowedFooterMenu = $this->user->isActivatedUser();
$this->title = $this->user->isActivatedUser()? 'チャージ手段' : 'どこからチャージしますか';
?>
<?php if (!$pointSites): ?>
    <p class="alert alert-warning">サイトがありません</p>
<?php else: ?>
    <?php foreach ($pointSites as $pointSite): ?>
    <!--item-->
    <div class="charge_means_box clearfix">
        <?php if ($pointSite->isAuthorized): ?>
        <a class="clearfix" href="<?= Url::to(['charge/price', 'code' => $pointSite->point_site_code, 'mode' => ChargeController::PRICE_MODE_NORMAL]) ?>">
        <?php else: ?>
        <a class="clearfix fancybox" href="<?= Url::to(['charge/detail', 'id' => $pointSite->id]) ?>">
        <?php endif; ?>
            <p class="charge_means_rogo"><?= Html::img($pointSite->icon_image_url) ?></p>
            <div class="charge_means_text_box">
                <p class="charge_means_sitename"><?= Html::encode($pointSite->site_name); ?></p>
                <p class="charge_means_point_text">
                    <span><?= Format::formattedNumber($pointSite->introduce_charge_rate_point) ?></span><?= Html::encode($pointSite->denomination); ?>
                    <?= $this->img('img_arrw') ?>
                    <span><?= Format::formattedNumber($pointSite->introduce_charge_rate_price) ?></span>円</p>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
