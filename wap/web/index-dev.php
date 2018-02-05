<?php

require(__DIR__ . '/../../backend/web/access_limit.php');

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../config/main.php')
);

$application = new yii\web\Application($config);

if (!defined('ASSETS_BASE_URI')) {
    define('ASSETS_BASE_URI', Yii::$app->params['m_assets_base_uri']);
}
if (!defined('UPLOAD_BASE_URI')) {
    define('UPLOAD_BASE_URI', Yii::$app->params['upload_base_uri']);
}
if (!defined('FE_BASE_URI')) {
    define('FE_BASE_URI', Yii::$app->params['fe_base_uri']);
}
if (!defined('IN_APP') && false !== strpos(Yii::$app->request->hostInfo, '//app.')) {
    define('IN_APP', true);
}
if (!defined('CLIENT_TYPE')) {
    if (defined('IN_APP') && IN_APP) {
        define('CLIENT_TYPE', 'app');
    } else {
        define('CLIENT_TYPE', 'wap');
    }
}

require(__DIR__ . '/../../common/config/di.php');

$application->run();
