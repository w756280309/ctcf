<?php

namespace frontend\assets;

use common\assets\FeAsset;
use yii\web\AssetBundle;

class FrontAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = ASSETS_BASE_URI;
    public $css = [
        'css/base.css',
    ];
    public $js = [
    ];
    public $depends = [
        FeAsset::class,
    ];
    public $jsOptions = [
        'position' => 1,
    ];
}
