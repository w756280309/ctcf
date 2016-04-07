<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@app/assets';
    //public $basePath = '@webroot';
    public $baseUrl = ASSETS_BASE_URI;
    public $css = [
        ASSETS_BASE_URI.'dist/frontend.css?v=20160407',
        ASSETS_BASE_URI.'dist/frontend_kc.css?v=20160407'
    ];
    public $js = [
        ASSETS_BASE_URI.'vendor/bootstrap/dist/js/bootstrap.min.js?v=20160407',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
