<?php

namespace api\modules\v2\controllers;

class BaseController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            \common\filters\UserAccountAcesssControl::className(),
            \api\modules\v2\filters\ApiResponseFilter::className(),
        ];
    }
}
