<?php

Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('frontend', dirname(dirname(__DIR__)).'/frontend');
Yii::setAlias('backend', dirname(dirname(__DIR__)).'/backend');
Yii::setAlias('wap', dirname(dirname(__DIR__)).'/wap');
Yii::setAlias('borrower', dirname(dirname(__DIR__)).'/borrower');
Yii::setAlias('console', dirname(dirname(__DIR__)).'/console');
Yii::setAlias('api', dirname(dirname(__DIR__)).'/api');
Yii::setAlias('channel', dirname(dirname(__DIR__)).'/channel');

\Yii::$container->set('account_service', 'common\\service\\AccountService');
\Yii::$container->set('paginator', 'YiiPlus\\Paginator\\Paginator');
\Yii::$container->set('ump', 'PayGate\\Ump\\Client', ['7050209', __DIR__.'/payment/ump/umpay.key', __DIR__.'/payment/ump/umpay.crt']);
\Yii::$container->set('sms', 'SmsGate\\SmsRequest', [true, ['15810036547', '18518154492']]);
