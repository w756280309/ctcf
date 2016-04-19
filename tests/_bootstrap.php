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
