<?php

namespace api\modules\v2\controllers\app;

use api\modules\v2\controllers\BaseController;
use yii\filters\VerbFilter;

class AuthController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'captchas' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'captchas' => [
                'class' => 'api\modules\v2\actions\CaptchaAction',
                'minLength' => 4,
                'maxLength' => 4,
            ],
        ];
    }
}
