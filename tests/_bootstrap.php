<?php

require dirname(__DIR__).'/vendor/autoload.php';
require dirname(__DIR__).'/vendor/yiisoft/yii2/Yii.php';

Yii::setAlias('common', dirname(__DIR__).'/common');
Yii::setAlias('frontend', dirname(__DIR__).'/frontend');
Yii::setAlias('backend', dirname(__DIR__).'/backend');
Yii::setAlias('wap', dirname(__DIR__).'/wap');
Yii::setAlias('borrower', dirname(__DIR__).'/borrower');
Yii::setAlias('console', dirname(__DIR__).'/console');
Yii::setAlias('api', dirname(__DIR__).'/api');
Yii::setAlias('Test', dirname(__DIR__).'/tests');

$config = require(__DIR__.'/_config.php');
$localConfigFile = __DIR__.'/_config-local.php';
if (file_exists($localConfigFile)) {
    $config = array_merge($config, require($localConfigFile));
}

class WdjfTests
{
    public static $config;
}
WdjfTests::$config = $config;
