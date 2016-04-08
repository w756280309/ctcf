<?php
/**
 * Created by PhpStorm.
 * User: xhy
 * Date: 15-3-11
 * Time: 上午10:53
 */

namespace frontend\assets;

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
        //'js/jquery.js'
    ];
    public $depends = [
       // 'backend\assets\AppAsset.php',
    ];
}
