<?php

namespace console\modules\tx;

use Yii;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();
        if (Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'console\modules\tx\controllers';
        }
    }
}
