<?php

namespace common\service;

use Yii;
use Exception;
use common\models\product\OnlineProduct;
use yii\data\Pagination;
use common\models\order\OnlineOrder;
use common\lib\bchelp\BcRound;
use common\models\user\MoneyRecord;
use common\models\sms\SmsMessage;
use common\service\PayService;
use common\models\user\User;

/**
 * Desc 主要用于订单获取
 * Created by zhy.
 * User: zhy
 * Date: 15-11-19
 * Time: 下午4:02.
 */
class OrderService
{
    private $pz = 5;//每页尺寸
    public function __construct()
    {
    }

    /**
     * 获取用户订单列表.
     *
     * @param type $uid
     *
     * @return bool
     */
    public function getUserOrderList($uid = null, $type = null, $page = 1)
    {
        if (empty($uid)) {
            return false;
        }
        $loan = new LoanService();
        $query1 = (new \yii\db\Query())
                ->select('order.*,p.title,p.status pstatus,p.end_date penddate,p.expires expiress,p.finish_date,p.jiaxi')
                ->from(['online_order order'])
                ->innerJoin('online_product p', 'order.online_pid=p.id')
                ->where(['order.uid' => $uid, 'order.status' => 1]);

        if (!empty($type)) {
            $query1->andWhere(['p.status' => $type]);
        }

        $querysql = $query1->orderBy('order.id desc')->createCommand()->getRawSql();
        $query = (new \yii\db\Query())
                ->select('*')
                ->from(['('.$querysql.')T']);

        $record = $query->all();
        $totalFund = 0;
        $daihuan = 0;
        foreach ($record as $val) {
            $totalFund = bcadd($totalFund, $val['order_money'], 2);
            if (OnlineProduct::STATUS_OVER !== $val['pstatus']) {
                $daihuan++;
            }
        }

        $count = $query->count();
        $pages = new Pagination(['totalCount' => $count, 'pageSize' => $this->pz]);
        $query = $query->offset(($page - 1) * ($this->pz))->limit($pages->limit)->all();
        $tp = ceil($count / $this->pz);
        $header = [
            'count' => intval($count),
            'size' => $this->pz,
            'tp' => $tp,
            'cp' => intval($page),
        ];
        $code = ($page > $tp) ? 1 : 0;
        $message = ($page > $tp) ? '数据错误' : '消息返回';
        foreach ($query as $key => $dat) {
            $query[$key]['statusval'] = Yii::$app->params['deal_status'][$dat['pstatus']];
            $query[$key]['order_time'] = $dat['order_time'] ? date('Y-m-d', $dat['order_time']) : '';
            $query[$key]['jiaxi'] = $dat['jiaxi'];
            if (in_array($dat['pstatus'], [OnlineProduct::STATUS_NOW])) {
                $query[$key]['profit'] = '--';
                $query[$key]['returndate'] = date('Y-m-d', $dat['finish_date']);
            } elseif (in_array($dat['pstatus'], [OnlineProduct::STATUS_HUAN, OnlineProduct::STATUS_FULL, OnlineProduct::STATUS_FOUND])) {
                //
                $replayment = \common\models\order\OnlineRepaymentPlan::findOne(['order_id' => $dat['id'], 'online_pid' => $dat['online_pid']]);
                if (null === $replayment) {
                    $query[$key]['profit'] = '--';
                } else {
                    $query[$key]['profit'] = $replayment->lixi;
                }
                $query[$key]['returndate'] = date('Y-m-d', $dat['finish_date']);
            } else {
                $replayment = \common\models\order\OnlineRepaymentRecord::findOne(['order_id' => $dat['id'], 'online_pid' => $dat['online_pid']]);
                $query[$key]['profit'] = $replayment->lixi;
                $query[$key]['returndate'] = date('Y-m-d', $replayment->refund_time);
            }
        }

        return ['header' => $header, 'data' => $query, 'code' => $code, 'message' => $message, 'totalFund' => $totalFund, 'daihuan' => $daihuan];
    }

    public static function confirmOrder($order)
    {
        if (OnlineOrder::STATUS_SUCCESS === $order->status) {
            return true;
        }
        $bcrond = new BcRound();
        $loan = OnlineProduct::findOne($order->online_pid);

        $user = $order->user;
        $ua = $user->type === User::USER_TYPE_PERSONAL ? $user->lendAccount : false;//当前限制投资人进行投资

        if ($ua === false) {
            throw new Exception(PayService::getErrorByCode(PayService::ERROR_UA));
        }
        //用户资金表
        $ua->available_balance = $bcrond->bcround(bcsub($ua->available_balance, $order->order_money), 2);
        if ($ua->available_balance * 1 < 0) {
            throw new Exception(PayService::getErrorByCode(PayService::ERROR_MONEY_LESS));
        }
        $transaction = Yii::$app->db->beginTransaction();
        $order->status = OnlineOrder::STATUS_SUCCESS;
        $order->save();

        $ua->drawable_balance = $bcrond->bcround(bcsub($ua->drawable_balance, $order->order_money), 2);
        $ua->freeze_balance = $bcrond->bcround(bcadd($ua->freeze_balance, $order->order_money), 2);
        $ua->out_sum = $bcrond->bcround(bcadd($ua->out_sum, $order->order_money), 2);
        $uare = $ua->save();
        if (!$uare) {
            $transaction->rollBack();
            throw new Exception(PayService::getErrorByCode(PayService::ERROR_UA_CAL));
        }

        //资金记录表
        $mrmodel = new MoneyRecord();
        $mrmodel->account_id = $ua->id;
        $mrmodel->sn = MoneyRecord::createSN();
        $mrmodel->type = MoneyRecord::TYPE_ORDER;
        $mrmodel->osn = $order->sn;
        $mrmodel->uid = $order->uid;
        $mrmodel->balance = $ua->available_balance;
        $mrmodel->out_money = $order->order_money;
        $mrmodel->remark = '资金流水号:'.$mrmodel->sn.',订单流水号:'.($order->sn).',账户余额:'.($ua->account_balance).'元，可用余额:'.($ua->available_balance).'元，冻结金额:'.$ua->freeze_balance.'元。';
        $mrres = $mrmodel->save();
        if (!$mrres) {
            $transaction->rollBack();
            throw new Exception(PayService::getErrorByCode(PayService::ERROR_MR));
        }

        /*修改标的完成比例  后期是否需要定时更新*/
        $summoney = OnlineOrder::find()->where(['status' => 1, 'online_pid' => $loan->id])->sum('order_money');
        $insert_sum = $summoney; //包含此笔募集的总金额
        $update = array();
        if (0 <= bccomp($insert_sum, $loan->money)) {//投资总和与融资总额比较。如果投资总和大于等于融资总额。要完成满标状态值的修改
            $update['finish_rate'] = 1;
            $update['full_time'] = time();//由于定时任务去修改满标状态以及生成还款计划。所以此处不设置修改满标状态
            $diff = \Yii::$app->functions->timediff(strtotime(date('Y-m-d', $loan->start_date)), strtotime(date('Y-m-d', $loan->finish_date)));
            OnlineOrder::updateAll(['expires' => $diff['day'] - 1], "online_pid=" . $loan->id . " and finish_date>0"); //对于此时设置有结束日期的要校准项目天数
        } else {
            $finish_rate = $bcrond->bcround(bcdiv($insert_sum, $loan->money), 2);
            if (0 === bccomp($finish_rate, 1) && 0 !== bccomp($insert_sum, $loan->money)) {//主要处理由于四舍五入造成的不应该募集完成的募集完成了：完成比例等于1了，并且包含此次交易成功所有金额不等于募集金额
                $finish_rate = 0.99;
            } else if(0 === bccomp($finish_rate, 0)) {
                $finish_rate = 0.01;
            }
            $update['finish_rate'] = $finish_rate;
        }

        $res = OnlineProduct::updateAll($update, ['id' => $loan->id]);
        if (false === $res) {
            $transaction->rollBack();
            throw new Exception(PayService::getErrorByCode(PayService::ERROR_SYSTEM));
        }
        $command = Yii::$app->db->createCommand('UPDATE '.OnlineProduct::tableName().' SET funded_money=funded_money+'.$order->order_money.' WHERE id='.$loan->id);
        $command->execute();//更新实际募集金额
        //投标成功，向用户发送短信
        $message = [
            $user->real_name,
            $loan->title,
            $order->order_money,
            Yii::$app->params['contact_tel']
        ];
        $sms = new SmsMessage([
            'uid' => $user->id,
            'template_id' => Yii::$app->params['sms']['toubiao'],
            'mobile' => $user->mobile,
            'level' => SmsMessage::LEVEL_LOW,
            'message' => json_encode($message)
        ]);
        $sms->save();
        $transaction->commit();
        return true;
    }

}
