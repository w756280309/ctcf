<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@app/assets';
    //public $basePath = '@webroot';
    //public $baseUrl = '@web';
    public $css = [
        'dist/frontend.css',
    ];
    public $js = [
        'vendor/jquery/dist/jquery.min.js',
        'vendor/bootstrap/dist/js/bootstrap.min.js',
    ];
    public $depends = [
    ];
}
