<?php
/* @var $this \app\views\View */
/* @var $formModel \app\models\forms\InquiryForm */

use app\views\View;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->backAction = ['guide/'];
$this->title = 'お問い合わせ';
?>
<?php $form = ActiveForm::begin(['id' => 'inquiry-form']); ?>
<div class="main_box">
    <p class="h_input mt50">メールアドレス</p>
    <?= $form->field($formModel, 'mail_address')->textInput(['class' => 'input_style'])->label(false) ?>
    <p class="h_input mt20">お問い合わせ内容</p>
    <?= $form->field($formModel, 'content')->textarea(['class' => 'textarea_style', 'rows' => 10])->label(false) ?>
    <p class="btn_red btn_contact mt40 mb50">
        <?=
        Html::a('送信', View::JS_VOID, [
            'onclick' => '$("#inquiry-form").submit()',
        ])
        ?>
    </p>
</div>
<?php ActiveForm::end(); ?>
