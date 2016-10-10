<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Class PriceSelectAsset
 * @package app\assets
 */
class TopAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/slick.css',
    ];
    public $js = [
        'js/jquery.min.js',
        'js/Chart.js',
        'js/slick.min.js',
        'js/imgLiquid-min.js',
        'js/top.js',
    ];

    public $jsOptions = ['position' => View::POS_HEAD,];
}
