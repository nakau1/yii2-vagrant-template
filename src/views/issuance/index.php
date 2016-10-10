<?php

/* @var $this \app\views\View */
/* @var $formModel \app\models\forms\IssuanceForm */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'カード発行手続き';

?>
<header>
    <h1>カード発行手続</h1>
</header>

<?php $form = ActiveForm::begin(['id' => 'issuance']); ?>
<h3>
    <?= $form->field($formModel, 'mail_address')->textInput([
        'class' => 'form-control input-lg',
        'placeholder' => 'あなたのメールアドレス',
        'autofocus' => true,
    ])->label(false)
    ?>
</h3>
<?=
Html::submitButton('次に進む', [
    'class' => 'btn btn-primary btn-lg btn-block center-block'
]);
?>
<?php ActiveForm::end(); ?>
