<?php

\Yii::$container->set('account_service', 'common\\service\\AccountService');
\Yii::$container->set('paginator', 'Zii\\Paginator\\Paginator');
\Yii::$container->set('PayGate\\Ump\\LoggerInterface', 'common\\models\\Logger');
\Yii::$container->set('ump', 'PayGate\\Ump\\Client', [
    \Yii::$app->params['ump']['apiUrl'],
    \Yii::$app->params['ump']['merchant_id'],
    \Yii::$app->params['ump']['wdjf_key'],
    \Yii::$app->params['ump']['ump_cert'],
    isset(Yii::$app->request->hostInfo) ? Yii::$app->request->hostInfo : "",
    \Yii::$app->params['clientOption'],
]);
\Yii::$container->set('sms', 'SmsGate\\SmsRequest');
