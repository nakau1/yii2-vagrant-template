<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Class PriceSelectAsset
 * @package app\assets
 */
class AppJQueryAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/jquery.fancybox.css',
    ];
    public $js = [
        'js/jquery-1.7.1.min.js',
        'js/jquery.mousewheel-3.0.6.pack.js',
        'js/jquery.fancybox.js',
    ];

    public $jsOptions = ['position' => View::POS_HEAD,];
}
