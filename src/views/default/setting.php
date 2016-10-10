<?php
/* @var $this \app\views\View */

use yii\helpers\Html;

//TODO: 現在セディナのページのURLは仮のもの

$this->title = '設定';
?>
<div class="main_box">
    <p class="btn_setting btn_01"><?= Html::a('<span>登録情報変更</span>', 'http://www.cedyna.co.jp/') ?></p>
    <p class="btn_setting btn_03"><?= Html::a('<span>パスワード変更</span>',  'http://www.cedyna.co.jp/') ?></p>
    <p class="btn_setting btn_02"><?= Html::a('<span>カード停止・再開</span>',  'http://www.cedyna.co.jp/') ?></p>
</div>