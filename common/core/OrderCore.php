<?php

namespace common\core;

use Yii;
use common\lib\bchelp\BcRound;
use common\service\PayService;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\user\MoneyRecord;

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
        $user = \common\models\user\User::findOne($uid);
        $uacore = new UserAccountCore();
        $bcrond = new BcRound();
        $order = new OnlineOrder();
        //$uid = Yii::$app->user->id;
        $order->order_money = $price;
        $order->uid = $uid;
        $time = time();
        bcscale(14);
        $transaction = Yii::$app->db->beginTransaction();
        $order->sn = OnlineOrder::createSN();
        $order->online_pid = $model->id;
        $order->order_time = $time;
        $order->refund_method = $model->refund_method;
        $order->yield_rate = $model->yield_rate;
        $order->expires = $model->expires;
        $order->mobile = $user->mobile;
        $order->username = $user->real_name;
        if (!$order->validate()) {
            $transaction->rollBack();

            return ['code' => PayService::ERROR_MONEY_FORMAT,  'message' => current($order->firstErrors), 'tourl' => '/order/order/ordererror'];
        }
        $ore = $order->save();
        if (!$ore) {
            $transaction->rollBack();

            return ['code' => PayService::ERROR_ORDER_CREATE,  'message' => PayService::getErrorByCode(PayService::ERROR_ORDER_CREATE), 'tourl' => '/order/order/ordererror'];
        }
        $ua = $uacore->getUserAccount($uid);
        if ($ua === false) {
            $transaction->rollBack();

            return ['code' => PayService::ERROR_UA,  'message' => PayService::getErrorByCode(PayService::ERROR_UA), 'tourl' => '/order/order/ordererror'];
        }
        //用户资金表
        $ua->available_balance = $bcrond->bcround(bcsub($ua->available_balance, $price), 2);
        if ($ua->available_balance * 1 < 0) {
            $transaction->rollBack();

            return ['code' => PayService::ERROR_MONEY_LESS,  'message' => PayService::getErrorByCode(PayService::ERROR_MONEY_LESS)];
        }
        $ua->drawable_balance = $bcrond->bcround(bcsub($ua->drawable_balance, $price), 2);
        $ua->freeze_balance = $bcrond->bcround(bcadd($ua->freeze_balance, $price), 2);
        $ua->out_sum = $bcrond->bcround(bcadd($ua->out_sum, $price), 2);
        $uare = $ua->save();
        if (!$uare) {
            $transaction->rollBack();

            return ['code' => PayService::ERROR_UA_CAL,  'message' => PayService::getErrorByCode(PayService::ERROR_UA_CAL), 'tourl' => '/order/order/ordererror'];
        }

        //资金记录表
        $mrmodel = new MoneyRecord();
        $mrmodel->account_id = $ua->id;
        $mrmodel->sn = MoneyRecord::createSN();
        $mrmodel->type = MoneyRecord::TYPE_ORDER;
        $mrmodel->osn = $order->sn;
        $mrmodel->uid = $uid;
        $mrmodel->balance = $ua->available_balance;
        $mrmodel->out_money = $price;
        $mrmodel->remark = '资金流水号:'.$mrmodel->sn.',订单流水号:'.($order->sn).',账户余额:'.($ua->account_balance).'元，可用余额:'.($ua->available_balance).'元，冻结金额:'.$ua->freeze_balance.'元。';
        $mrres = $mrmodel->save();
        if (!$mrres) {
            $transaction->rollBack();
            return ['code' => PayService::ERROR_MR,  'message' => PayService::getErrorByCode(PayService::ERROR_MR), 'tourl' => '/order/order/ordererror'];
        }

        /*修改标的完成比例  后期是否需要定时更新*/
        $summoney = OnlineOrder::find()->where(['status' => 1, 'online_pid' => $model->id])->sum('order_money');
        $update = array();
        if (0 === bccomp($summoney, $model->money)) {
            $update['finish_rate'] = 1;
            $update['full_time'] = time();//由于定时任务去修改满标状态以及生成还款计划。所以此处不设置修改满标状态
            $diff = \Yii::$app->functions->timediff(strtotime(date('Y-m-d', $model->start_date)), strtotime(date('Y-m-d', $model->finish_date)));
            OnlineOrder::updateAll(['expires' => $diff['day'] - 1], ['online_pid' => $model->id]);
        } else {
            $finish_rate = $bcrond->bcround(bcdiv($summoney, $model->money), 2);
            $update['finish_rate'] = $finish_rate;
        }

        $res = OnlineProduct::updateAll($update, ['id' => $model->id]);
        if (!$res) {
            $transaction->rollBack();
            return ['code' => PayService::ERROR_SYSTEM, 'message' => PayService::getErrorByCode(PayService::ERROR_SYSTEM), 'tourl' => '/order/order/ordererror'];
        }
        $transaction->commit();

        return ['code' => PayService::ERROR_SUCCESS,  'message' => ''];
    }
}
