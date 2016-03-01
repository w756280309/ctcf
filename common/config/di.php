<?php

\Yii::$container->set('account_service', 'common\\service\\AccountService');
\Yii::$container->set('paginator', 'YiiPlus\\Paginator\\Paginator');
\Yii::$container->set('ump', 'PayGate\\Ump\\Client', [
    \Yii::$app->params['ump']['merchant_id'],
    \Yii::$app->params['ump']['wdjf_key'],
    \Yii::$app->params['ump']['ump_cert'],
    \Yii::$app->params['ump']['notify'],
]);
\Yii::$container->set('sms', 'SmsGate\\SmsRequest', [true, ['15810036547', '18518154492']]);
