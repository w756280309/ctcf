<?php
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../common/config/session.php'),
    require(__DIR__ . '/../../common/config/session-local.php'),
    require(__DIR__ . '/../config/main.php')
);

$application = new yii\web\Application($config);
if (!defined('ASSETS_BASE_URI')) {
    define('ASSETS_BASE_URI', Yii::$app->params['pc_assets_base_uri']);
}
if (!defined('UPLOAD_BASE_URI')) {
    define('UPLOAD_BASE_URI', Yii::$app->params['upload_base_uri']);
}
if (!defined('FE_BASE_URI')) {
    define('FE_BASE_URI', Yii::$app->params['fe_base_uri']);
}
if (!defined('CLIENT_TYPE')) {
    define('CLIENT_TYPE', 'pc');
}
require(__DIR__ . '/../../common/config/di.php');//增加di引入，必须放置于此方可调用Yii::$app等
$application->run();
