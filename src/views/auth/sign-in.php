<?php
/* @var $this \app\views\View */
/* @var $signInForm app\models\forms\SignInForm */

use app\views\View;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->isShowedFooterMenu = false;

if ($this->user->isIssued()) {
    $this->title = 'カード認証';
} else {
    $this->title = 'ログイン';
}
?>
<div class="main_box">
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
    ]);
    ?>
    <p class="h_input mt80">ログインID</p>
    <?= $form->field($signInForm, 'cedyna_id')->textInput([
        'class' => 'input_style',
        'autofocus' => true,
        'required' => true,
    ])->label(false)
    ?>
    <?php if ($signInForm->isNecessityInputPassword()): ?>
        <p class="h_input mt30">パスワード</p>
        <?= $form->field($signInForm, 'password')->passwordInput([
            'class' => 'input_style',
            'required' => true,
        ])->label(false)
        ?>
    <?php endif; ?>
    <p class="btn_red btn_login mt50">
        <?=
        Html::a($this->user->isIssued() ? '認証' : 'ログイン', View::JS_VOID, [
            'onclick' => '$("#login-form").submit()',
        ])
        ?>
    </p>
    <?php ActiveForm::end() ?>
</div>
