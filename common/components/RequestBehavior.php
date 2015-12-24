<?php

namespace common\components;

use Yii;
use yii\base\Behavior;
use yii\web\Controller;
use yii\web\Response;

class RequestBehavior extends Behavior {

    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction'
        ];
    }
 
    public function beforeAction() {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
    }
 
}
