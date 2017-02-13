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
        'res/js/js.cookie.js',
        'res/js/lib.js?v=20170210',
    ];
    public $depends = [
        JqueryAsset::class,
    ];
    public $jsOptions = [
        'position' => 1,
    ];
}
