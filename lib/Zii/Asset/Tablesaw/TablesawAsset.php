<?php

namespace Zii\Asset\Tablesaw;

use yii\web\AssetBundle;

/**
 * Asset bundle for https://github.com/filamentgroup/tablesaw/tree/v3.0.0
 */
class TablesawAsset extends AssetBundle
{
    public $sourcePath = __DIR__.'/source';
    public $css = [
        'tablesaw.css',
    ];
    public $js = [
        'tablesaw.js',
        'tablesaw-init.js',
    ];
}
