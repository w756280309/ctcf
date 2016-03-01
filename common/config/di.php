<?php

\Yii::$container->set('account_service', 'common\\service\\AccountService');
\Yii::$container->set('paginator', 'YiiPlus\\Paginator\\Paginator');
\Yii::$container->set('ump', 'PayGate\\Ump\\Client', ['7050209', __DIR__.'/payment/ump/wdjf_prod.key', __DIR__.'/payment/ump/ump_prod.crt', \Yii::$app->params['ump']['notify']]);
\Yii::$container->set('sms', 'SmsGate\\SmsRequest', [true, ['15810036547', '18518154492']]);
