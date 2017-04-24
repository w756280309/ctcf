<?php

namespace Xii\Crm;

use yii\base\Module as BaseModule;


class Module extends BaseModule
{

    public $controllerNamespace = 'Xii\Crm\Controller';

    public function init()
    {
        $this->layout = 'crm';

        parent::init();
    }
}