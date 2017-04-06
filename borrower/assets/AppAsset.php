<?php

namespace borrower\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@app/assets';
    public $css = [
        'dist/frontend.css',
    ];
    public $js = [
        'vendor/bootstrap/dist/js/bootstrap.min.js',
    ];
    public $depends = [
        JqueryAsset::class,
    ];
}
