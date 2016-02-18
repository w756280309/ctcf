<?php

\Yii::$container->set('account_service', 'common\\service\\AccountService');
\Yii::$container->set('paginator', 'YiiPlus\\Paginator\\Paginator');
\Yii::$container->set('ump', 'PayGate\\Ump\\Client', ['7050209', __DIR__.'/payment/ump/umpay.key', __DIR__.'/payment/ump/umpay.crt', \Yii::$app->params['ump']['notify']]);
\Yii::$container->set('sms', 'SmsGate\\SmsRequest', [true, ['15810036547', '18518154492']]);
