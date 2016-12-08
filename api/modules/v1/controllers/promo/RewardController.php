<?php

namespace api\modules\v1\controllers\promo;

use api\modules\v1\controllers\Controller;
use common\models\promo\Promo1212;
use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;

class RewardController extends Controller
{
    public function actionCash($id)
    {
        if (null !== ($user = User::findOne($id))) {
            try {
                $promo = RankingPromo::findOne(['key' => 'promo_12_12_21']);
                $promo1212 = new Promo1212($promo);
                return $promo1212->sendRedPacket($user);
            } catch (\Exception $ex) {

            }
        }
    }
}
