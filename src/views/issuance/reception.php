<?php

/* @var $this \app\views\View */

$this->title = 'カード発行手続き';

?>
<header>
    <h1>カード発行手続</h1>
</header>
<p>ご登録のメールアドレスに認証メールをお送りしました。手続きを進めてください</p>

<p><?= \yii\bootstrap\Html::a('発番する(開発用)', '/demo/issue') ?></p>
