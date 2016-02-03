<?php
/**
 * Created by PhpStorm.
 * User: xhy
 * Date: 15-3-11
 * Time: 上午10:53
 */

namespace borrower\assets;

use yii\web\AssetBundle;

class WapAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/bootstrap.min.css',
        'css/base.css',
    ];
    public $js = [
    ];
    public $depends = [
    ];
}
