<?php
/* @var $this \app\views\View */
/* @var $pointSites \app\models\PointSite[] */

use app\helpers\Format;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'チャージ元選択';

?>
    <header>
        <h1>どこからチャージしますか？</h1>
    </header>

<?php if (!$pointSites): ?>
    <p class="alert alert-warning">サイトがありません</p>
<?php else: ?>
    <ul class="list-group col-md-5">
        <?php foreach ($pointSites as $pointSite): ?>
            <li class="list-group-item">
                <a href="<?= Url::to(['charge/detail?id=' . $pointSite->id]) ?>">
                    <?=
                    Html::img($pointSite->icon_image_url, [
                        'class' => 'img-responsive'
                    ])
                    ?>
                    <p><?= Html::encode($pointSite->site_name); ?></p>
                    <p>
                        <?= $pointSite->introduce_charge_rate_point ?><?= Html::encode($pointSite->denomination); ?>
                        →
                        <?= Format::formattedJapaneseCurrency($pointSite->introduce_charge_rate_price); ?>
                    </p>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

