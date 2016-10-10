<?php

/* @var $this \app\views\View */
/* @var $content string */

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

$backAction = $this->getBackAction();

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=375px, user-scalable=no">
        <meta charset="<?= Yii::$app->charset ?>">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <link rel="index" href="/" />
        <?php $this->head() ?>
    </head>

    <body<?php if ($this->isGrayBackground): ?> class="body_bggray"<?php endif; ?>>
    <?php $this->beginBody() ?>
    <!----ヘッダー---->
    <header>
        <p class="pollet_rogo"><?= $this->img('pollet_rogo') ?></p>
        <div class="head_mail">
            <a href="<?= Url::to(['information/']) ?>">
                <p class="ico_head_mail"><?= $this->img('ico_head_mail') ?></p>
                <?php if ($this->user->hasUnreadInformation): ?><p class="ico_unread">未</p><?php endif; ?>
            </a>
        </div>
    </header>

    <!----コンテンツ---->
    <div class="contents<?php if ($this->contentsHtmlClass): ?> <?= $this->contentsHtmlClass ?><?php endif; ?>">
        <?php if ($this->isShowedHeaderBar): ?>
            <div class="h_box">
                <?php if (!is_null($backAction)): ?>
                    <p class="btn_back">
                        <a href="<?= Url::to($backAction) ?>"><?= $this->img('ico_arrw_back') ?></a>
                    </p>
                <?php endif; ?>
                <p><?= !is_null($this->specifiedTitle) ? $this->specifiedTitle : Html::encode($this->title) ?></p>
            </div>
        <?php endif; ?>
        <?= $content ?>
    </div>

    <?php if ($this->isShowedFooterMenu): ?>
        <!----メニュー---->
        <div class="menu_box">
            <ul class="menu_list">
                <li class="menu_details"><?= Html::a('利用詳細', Url::to(['statement/trading'])) ?></li>
                <li class="menu_point"><?= Html::a('polletポイント', Url::to(['statement/point'])) ?></li>
                <li class="menu_charge"><?= Html::a('チャージ手段', Url::to(['charge/list'])) ?></li>
                <li class="menu_guide"><?= Html::a('利用ガイド', Url::to(['default/guide'])) ?></li>
                <li class="menu_setting"><?= Html::a('設定', Url::to(['default/setting'])) ?></li>
                <li class="menu_mail">
                    <a href="<?= Url::to(['information/']) ?>">
                        <?php if ($this->user->hasUnreadInformation): ?><p class="ico_unread">未</p><?php endif; ?>
                        お知らせ
                    </a>
                </li>
                <li class="menu_logout"><?= Html::a('ログアウト', Url::to(['auth/sign-out'])) ?></li>
            </ul>
        </div>
    <?php endif; ?>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>