<?php

namespace Xii\Crm;

use Yii;
use yii\base\Module as BaseModule;

class Module extends BaseModule
{

    public $controllerNamespace = 'Xii\Crm\Controller';

    public function init()
    {
        $this->layout = 'crm';

        parent::init();

        //指定console的命名空间
        if (Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'Xii\Crm\Command';
        }
    }
}