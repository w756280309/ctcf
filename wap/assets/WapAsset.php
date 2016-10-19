<?php

namespace wap\assets;

use yii\web\AssetBundle;

class WapAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = ASSETS_BASE_URI;
    public $css = [
        'css/bootstrap.min.css?v=20160407',
        'css/base.css?v=20160407',
    ];
    public $js = [
        'js/common.js',
        'js/lib.js',
        'js/jquery.cookie.js',
        'js/hmsr.js?v=20161019',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    public $jsOptions = [
        'position' => 1,
    ];
}
