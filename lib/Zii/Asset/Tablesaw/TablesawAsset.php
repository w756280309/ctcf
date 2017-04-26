<?php

namespace Zii\Asset\Tablesaw;

use yii\web\AssetBundle;

define('ZII_TABLESAW_SOURCE_PATH', __DIR__.'/source');

/**
 * Asset bundle for https://github.com/filamentgroup/tablesaw/tree/v3.0.0
 */
class TablesawAsset extends AssetBundle
{
    public $sourcePath = ZII_TABLESAW_SOURCE_PATH;
    public $css = [
        'tablesaw.css',
    ];
    public $js = [
        'tablesaw.js',
        'tablesaw-init.js',
    ];
}
