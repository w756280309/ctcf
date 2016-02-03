<?php

require(__DIR__.'/../vendor/autoload.php');

use PayGate\Ump\Client as UmpClient;

$ump = new UmpClient(
    '7050209',
    __DIR__.'/../common/config/payment/ump/umpay.key',
    __DIR__.'/../common/config/payment/ump/umpay.crt'
);

/*$ump->register(
    '300',
    '李毛毛',
    1,
    '110228190001012074',
    '13900000009'
);*/

$response = \Yii::$container->get('ump')->getUserInfo('UB201602011824050000000000043469');
if ($response->isSuccessful()) {

} else {
    $err = $response->getError();
    if ('?' === $err->getCode()) {
        $model->addError($name, $error->getMessage());
    }
}
