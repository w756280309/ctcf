<?php

namespace app\controllers;

use common\controllers\HelpersTrait;
use yii\filters\AccessControl;
use yii\web\Controller;

class BaseController extends Controller
{
    use HelpersTrait;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], //登录用户退出
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (!\Yii::$app->request->referrer) {
            \Yii::$app->view->backUrl = '/';
        }

        return parent::beforeAction($action);
    }
}
