<?php

namespace wap\assets;

use yii\web\AssetBundle;
use common\assets\FeAsset;

class WapAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = ASSETS_BASE_URI;
    public $css = [
        'css/bootstrap.min.css?v=20160407',
        'css/base.css?v=20170907',
    ];
    public $js = [
        'js/common.js?v=20170920',
    ];
    public $depends = [
        FeAsset::class,
    ];
    public $jsOptions = [
        'position' => 1,
    ];
}
