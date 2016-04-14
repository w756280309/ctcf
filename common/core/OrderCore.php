<?php

namespace common\core;

use Yii;
use common\service\PayService;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\order\OrderQueue;

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
        if (empty($sn)) {
            return ['code' => PayService::ERROR_LAW, 'message' => '缺少参数'];   //参数为空,抛出错误信息
        }

        $model = OnlineProduct::findOne(['sn' => $sn]);
        if (null === $model) {
            return ['code' => PayService::ERROR_SYSTEM, 'message' => '找不到标的信息'];   //对象为空,抛出错误信息
        }

        if (OnlineOrder::xsCount($uid) >= 3 && 1 === $model->is_xs) {
            return ['code' => PayService::ERROR_SYSTEM, 'message' => '新手标只允许投3次'];
        }

        $user = \common\models\user\User::findOne($uid);
        $order = new OnlineOrder();
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

        //免密逻辑处理
        $res = Yii::$container->get('ump')->orderNopass($order);
        if ($res->isSuccessful()) {
            try {
                //OrderManager::confirmOrder($order);
                if (null === OrderQueue::findOne(['orderSn' => $order->sn])) {
                    OrderQueue::initForQueue($order)->save();
                }
                return ['code' => PayService::ERROR_SUCCESS, 'message' => '', 'tourl' => '/order/order/orderwait?osn='.$order->sn];
                //return ['code' => PayService::ERROR_SUCCESS, 'message' => '', 'tourl' => '/order/order/ordererror?osn='.$order->sn];
            } catch (\Exception $ex) {
                return ['code' => PayService::ERROR_MONEY_FORMAT, 'message' => $ex->getMessage(), 'tourl' => '/order/order/ordererror?osn='.$order->sn];
            }
        } else {
            return ['code' => PayService::ERROR_MONEY_FORMAT, 'message' => $res->get('ret_msg'), 'tourl' => '/order/order/ordererror?osn='.$order->sn];
        }
    }
}
