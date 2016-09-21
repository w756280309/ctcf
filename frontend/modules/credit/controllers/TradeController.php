<?php

namespace frontend\modules\credit\controllers;

use frontend\controllers\BaseController;
use yii\filters\AccessControl;

class TradeController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [ //登录控制,如果没有登录,则跳转到登录页面
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionAssets()
    {
        return $this->render('assets', [
        ]);
    }
}
