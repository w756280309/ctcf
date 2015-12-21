<?php
/**
 * Created by PhpStorm.
 * User: xhy
 * Date: 15-3-11
 * Time: 上午10:53
 */

namespace backend\assets;

use yii\web\AssetBundle;
use backend\assets\AppAsset;


class MainAsset extends AssetBundle{

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
//        'css/site999.css',
    ];
    public $js = [
//        'js/jquery1.999.min.js',
    ];
    public $depends = [
//        'backend\assets\AppAsset.php',
    ];

}