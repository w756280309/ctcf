<?php

namespace frontend\modules\user\controllers;

use Yii;
use frontend\controllers\BaseController;
use common\models\promo\PromoCouponCode;
use yii\filters\AccessControl;

class CouponcodeController extends BaseController
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

    public function actionDuihuan()
    {
        $code = Yii::$app->request->post('code');
        if (Yii::$app->user->isGuest) {
            return ['code'=>1, 'message'=>'', 'data'=>'', "requireLogin"=>1];
        }
        $res = PromoCouponCode::duihuan($code, $this->getAuthedUser());
        $res['requireLogin'] = 0;
        return $res;
    }
}
