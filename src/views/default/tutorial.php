<?php
/* @var $this \app\views\View */

use yii\helpers\Html;

$this->isGrayBackground   = false;
$this->isShowedHeaderBar  = false;
$this->isShowedFooterMenu = false;
$this->title = 'はじめる';
?>
<div class="img_top_main"><?= $this->img('img_top_main') ?></div>
<div class="main_box">
    <p class="btn_red btn_common mb20"><?= Html::a('使い始める', 'charge/list') ?></p>
    <p class="btn_login_red center"><?= Html::a('ログイン', 'auth/sign-in') ?></p>
</div>
