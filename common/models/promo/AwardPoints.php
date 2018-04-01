<?php

namespace common\models\promo;


class AwardPoints extends BasePromo
{
    /**
     * 订单成功后的调用，无返回值
     */
    public function doAfterSuccessLoanOrder($order)
    {
        //获得用户对象
        $user = $order->onlineUser;
        //根据当前累计年化金额送积分
        $this->sendRewardByConfig($user, '180402_p200', 50000);
    }
}
