<?php

/* @var $this \app\views\View */
/* @var $mode string */
/* @var $pointSite \app\models\PointSite */
/* @var $formModel \app\models\forms\ChargePriceForm */
/* @var $chargeRemain integer */
/* @var $isFirst boolean */

use app\assets\AppAsset;
use app\assets\PriceSelectAsset;
use app\helpers\Format;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'チャージ金額選択';

AppAsset::register($this);
PriceSelectAsset::register($this);
?>
<header>
    <h1><?= Html::encode($this->title) ?></h1>
</header>

<?php $form = ActiveForm::begin(['id' => 'charge-price']); ?>

<h2>チャージ可能残高</h2>
<h3 class="alert alert-info"><?= Format::formattedJapaneseCurrency($chargeRemain) ?></h3>

<h2>チャージ額</h2>
<h3>
    <?= $form->field($formModel, 'price')->input('number', [
        'class' => 'form-control input-lg',
        'step'  => '100'
    ])->label(false) ?>
</h3>
<ul class="row list-unstyled">
    <li class="col-md-4">
        <?= Html::button('+100円', [
            'id' => 'add-100',
            'class' => 'btn btn-danger btn-lg btn-block',
        ])
        ?>
    </li>
    <li class="col-md-4">
        <?= Html::button('+1,000円', [
            'id' => 'add-1000',
            'class' => 'btn btn-danger btn-lg btn-block',
        ])
        ?>
    </li>
    <li class="col-md-4">
        <?= Html::button('+5,000円', [
            'id' => 'add-5000',
            'class' => 'btn btn-danger btn-lg btn-block',
        ])
        ?>
    </li>
</ul>

<?php if($isFirst): ?>
    <h2>初回カード発行手数料</h2>
    <h3 class="alert alert-danger">- <?= Format::formattedJapaneseCurrency($pointSite->chargeSource->card_issue_fee) ?></h3>
<?php endif; ?>
<p>注意事項などを掲載する文言です。注意事項などを掲載する文言です。注意事項などを掲載する文言です。注意事項などを掲載する文言です。注意事項などを掲載する文言です。注意事項などを掲載する文言です。注意事項などを掲載する文言です。</p>

<h2>ポイント交換総額</h2>
<h3 id="point-total-price" class="alert alert-info">10000円</h3>

<?= Html::buttonInput('確認', [
    'id' => 'confirm-charge-button',
    'class' => 'btn btn-primary btn-lg btn-block center-block',
]) ?>

<?php ActiveForm::end(); ?>

<?= Html::hiddenInput('card-issue-fee', $pointSite->chargeSource->card_issue_fee) ?>
<?= Html::hiddenInput('point-site-code', $pointSite->point_site_code) ?>

<a id="confirm-trigger" href="#confirm-dialog"></a>
<div style="display: none" id="confirm-dialog" class="center-block">
    <?= Html::img($pointSite->icon_image_url) ?>
    <h2 class="alert alert-warning"><span id="confirm-charge-price">0</span>円チャージ</h2>
    <?= Html::buttonInput('チャージする', [
        'id' => 'commit-charge-button',
        'class' => 'btn btn-primary btn-lg btn-block center-block',
    ]) ?>
</div>
