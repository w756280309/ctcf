<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class FrontAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = ASSETS_BASE_URI;
    public $css = [
        'css/base.css',
    ];
    public $js = [
        'js/jquery-1.8.3.min.js',
        'js/lib.js',
    ];
    public $depends = [
    ];
    public $jsOptions = [
        'position' => 1,
    ];
}