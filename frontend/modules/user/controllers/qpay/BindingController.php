<?php

namespace frontend\modules\user\controllers\qpay;

use common\action\user\BankVerifyAction;
use common\action\user\PayAgreementAction;
use frontend\controllers\BaseController;
use Yii;
use yii\base\Model;
use yii\filters\AccessControl;

class BindingController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
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

    public function actions()
    {
        return [
            'umpmianmi' => PayAgreementAction::className(),//免密
        ];
    }
}
