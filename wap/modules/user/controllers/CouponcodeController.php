<?php

namespace app\modules\user\controllers;

use Yii;
use app\controllers\BaseController;
use common\models\promo\PromoCouponCode;


class CouponcodeController extends BaseController
{
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
