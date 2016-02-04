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
//1454555878
$loan = $ump->getLoan(22);
if($loan->isSuccessful()){
    
}
var_dump($loan);
//echo time();
//$pro = $ump->registerLoan(
//        time(),
//        '标的测试1',
//        '100000000',
//        'UB201602011824050000000000043469',        
//        '20160229'
//    );
//    var_dump($pro,$pro->isCreateLoanSuccessfull());
//echo '<pre>';print_r($ump->getUserInfo('UB201602011824050000000000043469'));
exit;
$response = \Yii::$container->get('ump')->getUserInfo('UB201602011824050000000000043469');
if ($response->isSuccessful()) {

} else {
    $err = $response->getError();
    if ('?' === $err->getCode()) {
        $model->addError($name, $error->getMessage());
    }
}
