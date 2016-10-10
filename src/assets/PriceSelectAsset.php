<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Class PriceSelectAsset
 * @package app\assets
 */
class PriceSelectAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'fancybox/jquery.fancybox.css',
    ];
    public $js = [
        'fancybox/jquery.fancybox.js',
        'js/charge-price.js',
    ];
}
