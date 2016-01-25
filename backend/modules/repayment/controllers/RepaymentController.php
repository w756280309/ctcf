<?php

namespace backend\modules\repayment\controllers;

use Yii;
use common\models\order\OnlineRepaymentRecord;
use common\models\product\OnlineProduct;
use backend\controllers\BaseController;
use yii\web\Response;
use common\models\order\OnlineRepaymentPlan;
use common\models\user\MoneyRecord;
use common\models\user\UserAccount;
use common\lib\bchelp\BcRound;
use common\models\order\OnlineFangkuan;
use common\models\user\User;
use common\models\sms\SmsMessage;

/**
 * OrderController implements the CRUD actions for OfflineOrder model.
 */
class RepaymentController extends BaseController
{
    public $layout = 'frame';

    public function init()
    {
        parent::init();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
    }
    /**
     * Lists all OfflineOrder models.
     *
     * @return mixed
     */
    public function actionIndex($pid = null)
    {
        $deal = OnlineProduct::find()->select('title,status')->where(['id' => $pid])->one();
        $model = (new \yii\db\Query())
                ->select('orp.*,u.real_name,u.mobile')
                ->from(['online_repayment_plan orp'])
                ->innerJoin('user u', 'orp.uid=u.id')
                ->where(['orp.online_pid' => $pid])->all();
        $total_bj = 0;
        $total_lixi = 0;
        $total_bx = 0;
        bcscale(14);
        foreach ($model as $val) {
            $total_bj = bcadd($total_bj, $val['benjin']);
            $total_lixi = bcadd($total_lixi, $val['lixi']);
            $total_bx = bcadd($total_bj, $total_lixi);
        }
        //应还款人数
        $count = OnlineRepaymentPlan::find()->where(['online_pid' => $pid])->groupBy('uid')->count();

        $bcround = new BcRound();

        return $this->render('liebiao', [
                    'count' => $count,
                    'yhbj' => $bcround->bcround($total_bj, 2),
                    'yhlixi' => $bcround->bcround($total_lixi, 2),
                    'total_bx' => $bcround->bcround($total_bx, 2),
                    'deal' => $deal,
                    'model' => $model,
        ]);
    }

    /**
     * 还款操作.
     */
    public function actionDorepayment()
    {
        if (!Yii::$app->request->isPost) {
            return [
                'result' => 0,
                'message' => '非法请求',
            ];
        }
        $pid = Yii::$app->request->post('pid');
        $deal = OnlineProduct::findOne(['id' => $pid]);
        $saleac = UserAccount::findOne(['uid' => $deal->borrow_uid, 'type' => UserAccount::TYPE_BORROW]);
        $bcround = new BcRound();
        bcscale(14);
        $time = strtotime(date('Y-m-d'));
        $orders = OnlineRepaymentPlan::find();
        $weihuancount = $orders->where(['online_pid' => $pid, 'status' => OnlineRepaymentPlan::STATUS_WEIHUAN])->count();
        if ($weihuancount == 0) {
            return ['result' => 0, 'message' => '没有需要还款的项目']; //
        }
        $orderarr = $orders->where(['online_pid' => $pid, 'status' => OnlineRepaymentPlan::STATUS_WEIHUAN])->all();
        $total_faxi = 0;
        $total_benjin = 0;
        $total_lixi = 0;
        $diff = \Yii::$app->functions->timediff(strtotime(date('Y-m-d', $deal->start_date)), strtotime(date('Y-m-d', $deal->finish_date)));
        foreach ($orderarr as $key => $val) {
            $jixidays = $diff['day'] - 1;
            $orderarr[$key]['yuqi_day'] = 0;
            $orderarr[$key]['overdue'] = 0;
            $total_faxi = $bcround->bcround(bcadd($total_faxi, $orderarr[$key]['overdue']), 2);
            $total_benjin = $bcround->bcround(bcadd($total_benjin, $val['benjin']), 2);
            $lixi = bcmul(bcmul($val['benjin'], bcdiv($deal->yield_rate, 360)), $jixidays);
            $total_lixi = $bcround->bcround(bcadd($total_lixi, $lixi), 2);
        }

        $repaymentrecord = new OnlineRepaymentRecord();
        $mrmodel = new MoneyRecord();
        $transaction = Yii::$app->db->beginTransaction();
        foreach ($orderarr as $order) {
            $jixidays = $diff['day'] - 1;
            $lixi = bcmul(bcmul($order['benjin'], bcdiv($deal->yield_rate, 360)), $jixidays);
            //var_dump($plan);
            $record = clone $repaymentrecord;
            $money_record = clone $mrmodel;
            $record->online_pid = $pid;
            $record->order_id = $order['order_id'];
            $record->order_sn = OnlineRepaymentRecord::createSN();
            $record->qishu = 1; //默认期数1，到期本息
            $record->uid = $order['uid'];
            $record->benxi = $bcround->bcround(bcadd($lixi, $order['benjin']), 2);
            $record->benjin = $order['benjin'];
            $record->lixi = $lixi;
            $record->overdue = $order['overdue'];
            $record->yuqi_day = $order['yuqi_day'];
            $record->benxi_yue = 0;
            $record->status = 1;
            $record->refund_time = $time;
            if (!$record->save()) {
                $transaction->rollBack();

                return ['result' => 0, 'message' => '还款失败，记录失败']; //
            }
            $order->status = OnlineRepaymentPlan::STATUS_YIHUAN;
            if (!$order->save()) {
                $transaction->rollBack();
                return ['result' => 0, 'message' => '还款失败，状态修改失败'];
            }
            $ua = UserAccount::findOne(['uid' => $order['uid'], 'type' => UserAccount::TYPE_LEND]);
            //投资人账户调整
            $lixiyuqi = $bcround->bcround(bcadd($lixi, $order['overdue']), 2);
            $draw_in_sum = $bcround->bcround(bcadd($order['benjin'], $lixiyuqi), 2);//计算本金利息逾期
            $ua->available_balance = $bcround->bcround(bcadd($ua->available_balance, $draw_in_sum), 2); //将投标的钱再加入到可用余额中
            $ua->drawable_balance = $bcround->bcround(bcadd($ua->drawable_balance, $draw_in_sum), 2);
            $ua->in_sum = $bcround->bcround(bcadd($ua->in_sum, $draw_in_sum), 2);
            $ua->investment_balance = $bcround->bcround(bcsub($ua->investment_balance, $order['benjin']), 2);//理财
            $ua->profit_balance = $bcround->bcround(bcadd($ua->profit_balance, $lixiyuqi), 2);//收益
            if (!$ua->save()) {
                $transaction->rollBack();

                return ['result' => 0, 'message' => '还款失败，投资人账户调整失败'];
            }
            //增加资金记录
            $money_record->account_id = $ua->id;
            $money_record->sn = MoneyRecord::createSN();
            $money_record->type = MoneyRecord::TYPE_HUIKUAN;
            $money_record->osn = $order->sn;
            $money_record->uid = $order['uid'];
            $money_record->in_money = $draw_in_sum;
            $money_record->balance = $ua->available_balance;
            $money_record->remark = '本金:'.$order['benjin'].'元;利息:'.$lixi.'元;逾期天数:'.$order['yuqi_day'].'天;罚息:'.$order['overdue'].'元';
            $mrres = $money_record->save();
            if (!$mrres) {
                $transaction->rollBack();

                return ['result' => 0, 'message' => '还款失败，资金记录失败'];
            }
        }
        //融资人需要扣除的金额计算
        $total_repayment = $bcround->bcround(bcadd(bcadd($total_benjin, $total_lixi), $total_faxi), 2);
        $balance = $bcround->bcround(bcsub($saleac->available_balance, $total_repayment), 2);

        if ($balance * 1 < 0) {
            $transaction->rollBack();

            return ['result' => 0, 'message' => '账户余额不足'];
        }
        $saleac->account_balance = $bcround->bcround(bcsub($saleac->account_balance, $total_repayment), 2);
        $saleac->available_balance = $bcround->bcround(bcsub($saleac->available_balance, $total_repayment), 2);
        $saleac->drawable_balance = $bcround->bcround(bcsub($saleac->drawable_balance, $total_repayment), 2);
        $saleac->out_sum = $bcround->bcround(bcadd($saleac->out_sum, $total_repayment), 2);
        if (!$saleac->save()) {
            $transaction->rollBack();

            return ['result' => 0, 'message' => '账户余额扣款异常'];
        }

        $smrecord = new MoneyRecord();
        $smrecord->account_id = $saleac->id;
        $smrecord->sn = MoneyRecord::createSN();
        $smrecord->type = MoneyRecord::TYPE_HUANKUAN;
        $smrecord->osn = '';
        $smrecord->uid = $saleac->uid;
        $smrecord->out_money = $total_repayment;
        $smrecord->balance = $saleac->available_balance;
        $smrecord->remark = '还款总计:'.$total_repayment.'元；应还本金:'.$total_benjin.'元；应还利息:'.$total_lixi.'元；应还罚息'.$total_faxi.'元；';
        $smrres = $smrecord->save();
        if (!$smrres) {
            $transaction->rollBack();

            return ['result' => 0, 'message' => '还款失败，资金记录失败'];
        }
        $opres = OnlineProduct::updateAll(['status' => OnlineProduct::STATUS_OVER, 'sort' => 60], ['id' => $pid]);
        if (!$opres) {
            $transaction->rollBack();

            return ['result' => 0, 'message' => '还款失败，修改标的状态错误'];
        }
        
        $_repaymentrecord = OnlineRepaymentRecord::find()->where(['online_pid' => $pid, 'status' => OnlineRepaymentRecord::STATUS_DID])->groupBy('uid');
        $data = $_repaymentrecord->all();
        $sms = new SmsMessage([
            'template_id' => Yii::$app->params['sms']['huikuan'],
            'level' => SmsMessage::LEVEL_LOW
        ]);

        foreach ($data as $val) {
            $user = User::findOne($val->uid);
            $data_arr = $_repaymentrecord->having(['uid' => $val['uid']])->select("sum(benjin) as benjin, sum(lixi) as lixi")->createCommand()->queryAll();
            $message = [
                $user->real_name,
                $data_arr[0]['benjin'],
                $data_arr[0]['lixi'],
                Yii::$app->params['contact_tel']
            ];

            $_sms = clone $sms;
            $_sms->uid = $user->id;
            $_sms->mobile = $user->mobile;
            $_sms->message = json_encode($message);

            $_sms->save();
        }
        
        $transaction->commit();

        return [
            'result' => 1,
            'message' => '还款成功',
        ];
    }

    public function actionFk()
    {
        $pid = Yii::$app->request->post('pid');
        $product = OnlineProduct::findOne($pid);
        $fk = OnlineFangkuan::findOne(['online_product_id' => $pid]);
        if (!in_array($product->status, [OnlineProduct::STATUS_FULL, OnlineProduct::STATUS_FOUND])) {
            return ['result' => 0, 'message' => '标的状态异常，当前状态码：'.$product->status];
        }
        if ($fk->status == 3) {
            return ['result' => 0, 'message' => '已经放过款了'];
        }
        bcscale(14);
        $bcround = new BcRound();
        $transaction = Yii::$app->db->beginTransaction();
        $opres = OnlineProduct::updateAll(['status' => OnlineProduct::STATUS_HUAN], ['id' => $pid]);
        if (!$opres) {
            $transaction->rollBack();

            return ['result' => 0, 'message' => '标的状态更新失败'];
        }
        $ua = UserAccount::findOne(['uid' => $product->borrow_uid, 'type' => UserAccount::TYPE_BORROW]);
       // var_dump($product->borrow_uid);exit;
        $ua->account_balance = $bcround->bcround(bcadd($ua->account_balance, $product->money), 2);
        $ua->available_balance = $bcround->bcround(bcadd($ua->available_balance, $product->funded_money), 2);
        $ua->drawable_balance = $bcround->bcround(bcadd($ua->drawable_balance, $product->money), 2);
        $ua->in_sum = $bcround->bcround(bcadd($ua->in_sum, $product->money), 2);
        if (!$ua->save()) {
            $transaction->rollBack();

            return ['result' => 0, 'message' => '更新用户融资账户异常'];
        }
        OnlineFangkuan::updateAll(['status' => 3], ['online_product_id' => $pid]);//将所有放款批次变为已经放款
        $mre_model = new MoneyRecord();
        $mre_model->type = MoneyRecord::TYPE_FANGKUAN;
        $mre_model->sn = MoneyRecord::createSN();
        $mre_model->osn = $fk->sn;
        $mre_model->account_id = $ua->id;
        $mre_model->uid = $product->borrow_uid;
        $mre_model->in_money = $fk->order_money;
        $mre_model->remark = '已放款';
        $mre_model->balance = $ua->available_balance;
        if (!$mre_model->save()) {
            $transaction->rollBack();

            return ['result' => 0, 'message' => '资金记录失败'];
        }
        $transaction->commit();

        return [
                'result' => 1,
                'message' => '放款成功',
            ];
    }
}
