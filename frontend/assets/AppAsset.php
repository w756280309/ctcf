<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@app/assets';
    //public $basePath = '@webroot';
    public $baseUrl = ASSETS_BASE_URI;
    public $css = [
        'dist/frontend.css?v=20160407',
        'dist/frontend_kc.css?v=20160407'
    ];
    public $js = [
        'vendor/bootstrap/dist/js/bootstrap.min.js?v=20160407',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
