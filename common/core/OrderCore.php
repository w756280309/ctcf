<?php

namespace common\core;

use Yii;
use common\lib\bchelp\BcRound;
use common\service\PayService;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\user\MoneyRecord;
use common\models\sms\SmsMessage;

/**
 * Desc 主要用于实时读取用户资金信息
 * Created by zhy.
 * User: zhy
 * Date: 15-11-19
 * Time: 下午4:02.
 */
class OrderCore
{
    /**
     * 创建用户标的订单.
     */
    public function createOrder($sn = null, $price = null, $uid = null)
    {
        $model = OnlineProduct::findOne(['sn' => $sn]);
        if (OnlineOrder::xsCount($uid) >= 3 && 1 === $model->is_xs) {
            return ['code' => PayService::ERROR_SYSTEM, 'message' => '新手标只允许投3次'];
        }
        $user = \common\models\user\User::findOne($uid);
        $uacore = new UserAccountCore();
        $bcrond = new BcRound();
        $order = new OnlineOrder();
        //$uid = Yii::$app->user->id;
        $order->order_money = $price;
        $order->uid = $uid;
        $time = time();
        bcscale(14);
        
        $order->sn = OnlineOrder::createSN();
        $order->online_pid = $model->id;
        $order->order_time = $time;
        $order->refund_method = $model->refund_method;
        $order->yield_rate = $model->yield_rate;
        $order->expires = $model->expires;
        $order->mobile = $user->mobile;
        $order->username = $user->real_name;
        if (!$order->validate()) {
            return ['code' => PayService::ERROR_MONEY_FORMAT,  'message' => current($order->firstErrors), 'tourl' => '/order/order/ordererror'];
        }
        $ore = $order->save(false);
        if (!$ore) {
            return ['code' => PayService::ERROR_ORDER_CREATE,  'message' => PayService::getErrorByCode(PayService::ERROR_ORDER_CREATE), 'tourl' => '/order/order/ordererror'];
        }
    
        $next = Yii::$container->get('ump')->registerOrder($order);
        return ['code' => PayService::ERROR_SUCCESS, 'message' => '', 'tourl' => $next];
    }
}
