<?php
/* @var $this \app\views\View */
/* @var $pointSite \app\models\PointSite */

use app\helpers\Format;
use yii\helpers\Html;

$this->title = $pointSite->site_name;
?>
<div class="charge_means_fancybox" id="inline1" style="width:300px;">
    <p class="point_site_name"><?= Html::encode($pointSite->site_name) ?></p>
    <p class="point_site_rogo"><?= Html::img($pointSite->icon_image_url) ?></p>
    <div class="property_point_box clearfix">
        <p class="property_point_text">チャージレート</p>
        <p class="property_point_price">
            <span><?= Format::formattedNumber($pointSite->introduce_charge_rate_point) ?></span><?= Html::encode($pointSite->denomination) ?>
            <?= $this->img('ico_arrw_red', ['class' => 'ico_arrw_red']) ?>
            <span><?= Format::formattedNumber($pointSite->introduce_charge_rate_price) ?></span>円
        </p>
    </div>
    <div class="fancybox_site_text_box">
        <p><?= Html::encode($pointSite->description) ?></p>
    </div>
    <p class="btn_red btn_login">
        <?= Html::a('サイトでログイン', ['demo/authenticate', 'auth_url' => Html::encode($pointSite->auth_url)]) ?>
    </p>
</div>
