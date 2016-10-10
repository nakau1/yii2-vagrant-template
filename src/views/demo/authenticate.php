<?php
/* @var $this \app\views\View */

use yii\helpers\Html;

$this->registerCss('html, body { background-color: #ffffe4 }');
?>
<h1>デモ用外部認証サイト</h1>

<?= Html::beginForm() ?>
<?=
Html::submitButton('認証する', [
    'class' => 'btn btn-normal btn-lg btn-block center-block',
])
?>
<?= Html::endForm() ?>
