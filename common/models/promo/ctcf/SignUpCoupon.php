<?php

namespace common\models\promo\ctcf;

use Yii;

/**
 * 新注册用户送代金券弹窗标识
 */
class SignUpCoupon
{
    public function addTicket($user, $source)
    {
        //设置弹窗标识,一年后过期
        $redis = Yii::$app->redis;
        $redis->set('oldUserRewardPop_' . $user->id, 2);
        $redis->expire('oldUserRewardPop_' . $user->id, 365 * 24 * 3600);
    }
}