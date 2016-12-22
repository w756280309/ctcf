<?php

namespace common\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class FeAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = FE_BASE_URI;
    public $css = [
    ];
    public $js = [
        'res/js/lib.js',
        'res/js/js.cookie.js',
        'res/js/hmsr.js',
    ];
    public $depends = [
        JqueryAsset::class,
    ];
    public $jsOptions = [
        'position' => 1,
    ];
}