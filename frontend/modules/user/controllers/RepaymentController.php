<?php

namespace app\modules\user\controllers;

use Yii;
use common\models\user\User;
use common\models\user\UserAccount;
use common\models\order\OnlineRepaymentRecord;
use frontend\controllers\BaseController;
use common\models\user\MoneyRecord;
use common\models\order\OnlineFangkuan;
use common\models\product\OnlineProduct;
use common\models\order\OnlineRepaymentPlan;
use common\lib\bchelp\BcRound;

class RepaymentController extends BaseController {

    public $layout = 'main';

    /**
     * 还款确认页面 缺少罚息时候罚息比例问题，分配到每个投资者该如何分配
     */
    public function actionIndex($pid = null) {
        $this->layout = FALSE;
        if (\Yii::$app->user->isGuest) {
            return $this->redirect('/user/repayment/error?error=101'); //访客无法还款
        }
        $uid = Yii::$app->user->getIdentity()->id;
        $saleac = UserAccount::getUserAccount($uid, 2);
        if (empty($pid)) {
            return $this->redirect('/user/repayment/error?error=100'); //缺少get参数
        }
        if (empty($saleac)) {
            return $this->redirect('/user/repayment/error?error=102'); //尚未开通融资账户
        }
        //$pid = \Yii::$app->request->post('pid');
        
        $fk = OnlineFangkuan::find()->where(['online_product_id' => $pid,'status'=>OnlineFangkuan::STATUS_FANGKUAN])->asArray()->all();
        if (!count($fk)) {
            return $this->redirect('/user/repayment/error?error=103'); //此项目还没有放款
        }

        $product = OnlineProduct::findOne($pid); //实际2015-10-9
        if (empty($product)) {
            return $this->redirect('/user/repayment/error?error=105'); //此项目不存在
        }
        if ($product->status != OnlineProduct::STATUS_HUAN) {
            return $this->redirect('/user/repayment/error?error=106'); //此项目状态异常
        }

        $min_time = \common\models\order\OnlineFangkuanDetail::find()->where(['online_product_id' => $pid])->select('order_time')->min('order_time');
        $pp = new \common\lib\product\ProductProcessor();
        $earlier = $pp->LoanTerms('d1', date('Y-m-d', $min_time), $product->expires); //d1代表到期本息
        //var_dump($min_time,  strtotime($earlier));
        // var_dump(time(),strtotime($earlier));exit;
        if (time() < strtotime($earlier)) {
            return $this->redirect('/user/repayment/error?error=115&response=' . urlencode("还款日应为：" . $earlier)); //没到还款日
        }
        $end_time = strtotime(date("Y-m-d", $product->end_date)); //2015-10-09
        $model = new OnlineRepaymentRecord();
        $model->online_pid = $pid;

        $yuqifaxi = 0;
        bcscale(14);
        //var_dump(ceil(($time-$end_time)/(60*60*24)),  ceil(2.1));

        $orders = OnlineRepaymentPlan::find();
        $weihuancount = $orders->where(['online_pid' => $pid, 'status' => OnlineRepaymentPlan::STATUS_WEIHUAN])->count();
        if ($weihuancount == 0) {
            return $this->redirect('/user/repayment/error?error=107'); //没有需要还款的项目
        }
        $orderarr = $orders->where(['online_pid' => $pid, 'status' => OnlineRepaymentPlan::STATUS_WEIHUAN])->all();
        $bcround = new BcRound();
        $total_faxi = 0;
        $total_benjin = 0;
        $total_lixi = 0;
        $time = strtotime(date("Y-m-d"));
        //if($days>1)
        {
            foreach ($orderarr as $key => $val) {
                $days = ceil(($time - strtotime(date('Y-m-d', $val['updated_at']))) / (60 * 60 * 24)); //实际计息天数
                $jixidays = ceil((strtotime($earlier) - strtotime(date('Y-m-d', $val['updated_at']))) / (60 * 60 * 24)); //
                //$days = 102;//测试罚息
                $jkqx = intval($product->expires);
                $day_diff = ($days - $jkqx) > 0 ? (($days - $jkqx)) : 0; //计算逾期天数
                //echo date('Y-m-d').'|||'.date('Y-m-d', $val['updated_at']).'----'.$jixidays."<br>";
                $orderarr[$key]['yuqi_day'] = $day_diff;
                $orderarr[$key]['overdue'] = $bcround->bcround(bcmul(bcmul($val['benjin'], $product->yuqi_faxi), $day_diff), 2);
                $total_faxi = $bcround->bcround(bcadd($total_faxi, $orderarr[$key]['overdue']), 2);
                $total_benjin = $bcround->bcround(bcadd($total_benjin, $val['benjin']), 2);
                //$total_lixi = $bcround->bcround(bcadd($total_lixi, $val['lixi']), 2);
                $lixi = bcmul(bcmul($val['benjin'], bcdiv($product->yield_rate,360)),$jixidays);
                //var_dump($val['benjin'],$product->yield_rate,$jixidays,  date("y-m-d H:i:s",$val['updated_at']));
                $total_lixi = $bcround->bcround(bcadd($total_lixi, $lixi), 2);
            }
        }
        //exit;
        $total = $bcround->bcround(bcadd(bcadd($total_benjin, $total_lixi), $total_faxi), 2);
        //var_dump($total_faxi,$total_benjin,$total_lixi,$total);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $repaymentrecord = new OnlineRepaymentRecord();
            $mrmodel = new MoneyRecord();
            $transaction = Yii::$app->db->beginTransaction();
            foreach ($orderarr as $order) {
                $plan = new OnlineRepaymentPlan();
                $order['status'] = OnlineRepaymentPlan::STATUS_YIHUAN;
                $pre = $plan->updateAll($order, 'id=:id', array(':id' => $order['id']));

                if (!$pre) {
                    $transaction->rollBack();
                    return $this->redirect('/user/repayment/error?error=108'); //还款失败，记录失败
                }
                $jixidays = ceil((strtotime($earlier) - strtotime(date('Y-m-d', $order['updated_at']))) / (60 * 60 * 24));
                $lixi = bcmul(bcmul($order['benjin'], bcdiv($product->yield_rate,360)),$jixidays);
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
                //$record->lixi = $order['lixi'];
                $record->lixi = $lixi;
                $record->overdue = $order['overdue'];
                $record->yuqi_day = $order['yuqi_day'];
                $record->benxi_yue = 0;
                $record->status = 1;
                $record->refund_time = $time;
                if (!$record->save()) {
                    $transaction->rollBack();
                    return $this->redirect('/user/repayment/error?error=108'); //还款失败，记录失败
                }
                $order->status = OnlineRepaymentPlan::STATUS_YIHUAN;
                $order->lixi = $lixi;
                if (!$order->save()) {
                    $transaction->rollBack();
                    return $this->redirect('/user/repayment/error?error=109'); //还款失败，状态修改失败
                }
                $ua = UserAccount::getUserAccount($order['uid']);
                //投资人账户调整
                $ua->freeze_balance = $bcround->bcround(bcsub($ua->freeze_balance, $order['benjin']), 2); //将投标的钱从冻结金额中减去
                $ua->available_balance = $bcround->bcround(bcadd($ua->available_balance, $order['benjin']), 2); //将投标的钱放回到可用资金中
                $lixiyuqi = $bcround->bcround(bcadd($lixi, $order['overdue']), 2);
                $ua->available_balance = $bcround->bcround(bcadd($ua->available_balance, $lixiyuqi), 2); //将投标的钱再加入到可用余额中
                $ua->account_balance = $bcround->bcround(bcadd($ua->account_balance, $lixiyuqi), 2); //将投标的钱再加入到可用余额中
                if (!$ua->save()) {
                    $transaction->rollBack();
                    return $this->redirect('/user/repayment/error?error=111'); //还款失败，投资人账户调整失败
                }
                //增加资金记录
                $money_record->account_id = $ua->id;
                $money_record->sn = MoneyRecord::createSN();
                $money_record->type = MoneyRecord::TYPE_HUANKUAN;
                $money_record->osn = $order->sn;
                $money_record->status = 1;
                $money_record->uid = $order['uid'];
                $benxiyuqi = $bcround->bcround(bcadd($record->benxi, $order['overdue']), 2);
                $money_record->in_money = ($benxiyuqi);
                $money_record->balance = $ua->available_balance;
                $money_record->remark = "本金:" . $order['benjin'] . "元;利息:" . $lixi . "元;逾期天数:" . $order['yuqi_day'] . "天;罚息:" . $order['overdue'] . '元';
                $mrres = $money_record->save();
                if (!$mrres) {
                    $transaction->rollBack();
                    return $this->redirect('/user/repayment/error?error=110'); //还款失败，资金记录失败
                }
            }
            //融资人需要扣除的金额计算
            $total_repayment = $bcround->bcround(bcadd(bcadd($total_benjin, $total_lixi), $total_faxi), 2);
            //var_dump($total_faxi , $total_benjin ,$total_lixi,$total_repayment);
            $balance = $bcround->bcround(bcsub($saleac->available_balance, $total_repayment), 2);

            if ($balance * 1 < 0) {
                $transaction->rollBack();
                return $this->redirect('/user/repayment/error?error=112'); //账户余额不足
            }
            $saleac->account_balance = $bcround->bcround(bcsub($saleac->account_balance, $total_repayment), 2);
            $saleac->available_balance = $bcround->bcround(bcsub($saleac->available_balance, $total_repayment), 2);
            $saleac->out_sum = $bcround->bcround(bcsub($saleac->out_sum, $total_repayment), 2);
            if (!$saleac->save()) {
                $transaction->rollBack();
                return $this->redirect('/user/repayment/error?error=113'); //账户余额扣款异常
            }

            $smrecord = new MoneyRecord();
            $smrecord->account_id = $saleac->id;
            $smrecord->sn = MoneyRecord::createSN();
            $smrecord->type = MoneyRecord::TYPE_HUANKUAN;
            $smrecord->osn = '';
            $smrecord->status = 1;
            $smrecord->uid = $saleac->uid;
            $smrecord->out_money = $total_repayment;
            $smrecord->balance = $saleac->available_balance;
            $smrecord->remark = '还款总计:' . $total_repayment . '元；应还本金:' . $total_benjin . '元；应还利息:' . $total_lixi . '元；应还罚息' . $total_faxi . '元；';
            $smrres = $smrecord->save();
            if (!$smrres) {
                $transaction->rollBack();
                return $this->redirect('/user/repayment/error?error=110'); //还款失败，资金记录失败
            }
            $product->setScenario('status');
            $product->status = OnlineProduct::STATUS_OVER;
            if (!$product->save()) {
                $transaction->rollBack();
                return $this->redirect('/user/repayment/error?error=114'); //修改状态错误
            }

            $transaction->commit(); //echo 123;exit;
            return $this->redirect('/user/repayment/repaysuccess');
        }
        return $this->render('index', ['model' => $model, 'pid' => $pid,
                    'total_faxi' => $total_faxi,
                    'total_benjin' => $total_benjin,
                    'total_lixi' => $total_lixi,
                    'total' => $total]);
    }

    public function actionError($error = null) {
        $this->layout = FALSE;
        return $this->render('index', ['error' => $error]);
    }

    /*
     * 还款成功
     */

    public function actionRepaysuccess() {
        $this->layout = FALSE;
        return $this->render('repaysuccess');
    }

}
