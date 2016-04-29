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
use common\service\LoanService;
use backend\modules\order\controllers\OnlinefangkuanController;
use common\models\epay\EpayUser;
use common\utils\TxUtils;
use yii\web\NotFoundHttpException;

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
     * 还款计划详情页.
     */
    public function actionIndex($pid)
    {
        if (empty($pid)) {
            throw new NotFoundHttpException();     //参数无效,抛出404异常
        }

        $deal = OnlineProduct::find()->select('title,status')->where(['id' => $pid])->one();
        $model = (new \yii\db\Query())
                ->select('orp.*,u.real_name,u.mobile')
                ->from(['online_repayment_plan orp'])
                ->innerJoin('user u', 'orp.uid=u.id')
                ->where(['orp.online_pid' => $pid])->all();

        if (null === $deal || empty($model)) {
            throw new NotFoundHttpException();     //对象为空时,抛出404异常
        }

        $total_bj = 0;
        $total_lixi = 0;
        $total_bx = 0;
        bcscale(14);

        $qimodel = null;
        foreach ($model as $val) {
            $total_bj = bcadd($total_bj, $val['benjin']);
            $total_lixi = bcadd($total_lixi, $val['lixi']);
            $total_bx = bcadd($total_bj, $total_lixi);
            $qimodel[$val['qishu']][] = $val;
        }

        //应还款人数
        $count = OnlineRepaymentPlan::find()->select('uid')->where(['online_pid' => $pid])->groupBy('uid')->count();

        $bcround = new BcRound();

        return $this->render('liebiao', [
            'count' => $count,
            'yhbj' => $bcround->bcround($total_bj, 2),
            'yhlixi' => $bcround->bcround($total_lixi, 2),
            'total_bx' => $bcround->bcround($total_bx, 2),
            'deal' => $deal,
            'model' => $qimodel,
        ]);
    }

    /**
     * 还款操作.
     */
    public function actionDorepayment()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return [
                'result' => 0,
                'message' => '非法请求',
            ];
        }

        $pid = Yii::$app->request->post('pid');
        $qishu = Yii::$app->request->post('qishu');

        $or = OnlineRepaymentPlan::tableName();
        $eu = EpayUser::tableName();

        //查询还款计划信息
        $orders = OnlineRepaymentPlan::findAll(['online_pid' => $pid, 'status' => OnlineRepaymentPlan::STATUS_WEIHUAN, 'qishu' => $qishu]);

        $_orders = (new \yii\db\Query())
            ->select("$or.*, $eu.appUserId, $eu.epayUserId")
            ->from($or)
            ->leftJoin($eu, "$or.uid = $eu.appUserId")
            ->where(["$or.online_pid" => $pid, "$or.status" => OnlineRepaymentPlan::STATUS_WEIHUAN, "$or.qishu" => $qishu])
            ->all();

        if (0 === count($orders)) {
            return ['result' => 0, 'message' => '没有需要还款的项目'];
        }

        if ($qishu !== OnlineRepaymentPlan::find()->where(['online_pid' => $pid, 'status' => OnlineRepaymentPlan::STATUS_WEIHUAN])->min('qishu')) {
            return ['result' => 0, 'message' => '不允许跨期还款'];
        }

        $deal = OnlineProduct::findOne(['id' => $pid]);

        if (!deal) {
            return ['result' => 0, 'message' => '标的信息不存在'];
        }

        if (!in_array($deal->status, [OnlineProduct::STATUS_HUAN, OnlineProduct::STATUS_OVER])) {
            return ['result' => 0, 'message' => '当前标的状态不允许此操作'];
        }

        $ump = Yii::$container->get('ump');

        //调用联动接口,查询标的信息
        $loanResp = $ump->getLoanInfo($pid);

        if ($loanResp->isSuccessful()) {
            if ('02' === $loanResp->get('project_account_state')) {
                return ['result' => 0, 'message' => '当前联动标的状态为冻结状态'];
            }
            if ('2' !== $loanResp->get('project_state')) {
                return ['result' => 0, 'message' => '当前联动标的状态不允许此操作'];
            }
        } else {
            return ['result' => 0, 'message' => $loanResp->get('ret_msg')];
        }

        $saleac = UserAccount::findOne(['uid' => $deal->borrow_uid, 'type' => UserAccount::TYPE_BORROW]);
        $epayUser = EpayUser::findOne(['appUserId' => $deal->borrow_uid]);

        if (!$saleac || !$epayUser) {
            return ['result' => 0, 'message' => '融资方信息不存在'];
        }

        //融资人需要扣除的金额计算
        $totalFund = 0;
        $bcround = new BcRound();
        bcscale(14);

        foreach ($orders as $val) {
            $totalFund = bcadd($totalFund, $val->benxi);
        }

        $total_repayment = $bcround->bcround($totalFund, 2);
        $balance = $bcround->bcround(bcsub($saleac->available_balance, $total_repayment), 2);

        if (0 >= bccomp($balance, 0)) {
            return ['result' => 0, 'message' => '融资用户账户余额不足'];
        }

        $orgResp = $ump->getMerchantInfo($epayUser->epayUserId);

        if ($orgResp->isSuccessful()) {
            if ('1' !== $orgResp->get('account_state')) {
                return ['result' => 0, 'message' => '当前联动端商户状态异常'];
            }

            if (-1 === bccomp($orgResp->get('balance'), $total_repayment * 100)) {
                return ['result' => 0, 'message' => '当前联动端商户余额不足'];
            }
        } else {
            return ['result' => 0, 'message' => $orgResp->get('ret_msg')];
        }

        $sum_benxi_yue = OnlineRepaymentPlan::find()->where(['online_pid' => $pid, 'status' => OnlineRepaymentPlan::STATUS_WEIHUAN])->andWhere("qishu not in ($qishu)")->sum('benxi'); //未还其它期数的总和
        $sum_benxi_yue = !$sum_benxi_yue ? 0 : $sum_benxi_yue;

        if (OnlineProduct::STATUS_HUAN === $deal->status) {
            $transaction = Yii::$app->db->beginTransaction();

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
            $smrecord->remark = '第'.$qishu.'期还款总计:'.$total_repayment.'元';

            if (!$smrecord->save()) {
                $transaction->rollBack();

                return ['result' => 0, 'message' => '还款失败，资金记录失败'];
            }

            if (empty($sum_benxi_yue)) {
                $opres = OnlineProduct::updateAll(['status' => OnlineProduct::STATUS_OVER, 'sort' => 60], ['id' => $pid]);
                if (!$opres) {
                    $transaction->rollBack();

                    return ['result' => 0, 'message' => '还款失败，修改标的状态错误'];
                }
            }

            $hkResp = $ump->huankuan(TxUtils::generateSn('HK'), $deal->id, $epayUser->epayUserId, $total_repayment);  //还款的订单日期只允许订单当日或订单前一天

            if ($hkResp->isSuccessful()) {
                $transaction->commit();
            } else {
                $transaction->rollBack();

                return ['result' => 0, 'message' => $hkResp->get('ret_code').$hkResp->get('ret_msg')];
            }
        }

        $repaymentrecord = new OnlineRepaymentRecord();
        $mrmodel = new MoneyRecord();
        foreach ($orders as $key => $order) {
            $transaction = Yii::$app->db->beginTransaction();

            $record = clone $repaymentrecord;
            $record->online_pid = $pid;
            $record->order_id = $order['order_id'];
            $record->order_sn = OnlineRepaymentRecord::createSN();
            $record->qishu = $qishu;
            $record->uid = $order['uid'];
            $record->lixi = $order['lixi'];
            $record->benxi = $order['benxi'];
            $record->benjin = $order['benjin'];
            $record->benxi_yue = $sum_benxi_yue;
            $record->status = OnlineRepaymentRecord::STATUS_DID;
            $record->refund_time = time();

            if (!$record->save()) {
                $transaction->rollBack();

                return ['result' => 0, 'message' => '还款失败，记录失败'];
            }

            $order->status = OnlineRepaymentPlan::STATUS_YIHUAN;
            if (!$order->save()) {
                $transaction->rollBack();

                return ['result' => 0, 'message' => '还款失败，状态修改失败'];
            }

            $ua = UserAccount::findOne(['uid' => $order['uid'], 'type' => UserAccount::TYPE_LEND]);

            //投资人账户调整
            $ua->available_balance = $bcround->bcround(bcadd($ua->available_balance, $record->benxi), 2); //将投标的钱再加入到可用余额中
            $ua->drawable_balance = $bcround->bcround(bcadd($ua->drawable_balance, $record->benxi), 2);
            $ua->in_sum = $bcround->bcround(bcadd($ua->in_sum, $record->benxi), 2);
            $ua->investment_balance = $bcround->bcround(bcsub($ua->investment_balance, $record->benjin), 2); //理财
            $ua->profit_balance = $bcround->bcround(bcadd($ua->profit_balance, $record->lixi), 2); //收益

            if (!$ua->save()) {
                $transaction->rollBack();

                return ['result' => 0, 'message' => '还款失败，投资人账户调整失败'];
            }

            //增加资金记录
            $money_record = clone $mrmodel;
            $money_record->account_id = $ua->id;
            $money_record->sn = MoneyRecord::createSN();
            $money_record->type = MoneyRecord::TYPE_HUIKUAN;
            $money_record->osn = $order->sn;
            $money_record->uid = $order['uid'];
            $money_record->in_money = $record->benxi;
            $money_record->balance = $ua->available_balance;
            $money_record->remark = '第'.$qishu.'期'.'本金:'.$order['benjin'].'元;利息:'.$record->lixi.'元;';

            if (!$money_record->save()) {
                $transaction->rollBack();

                return ['result' => 0, 'message' => '还款失败，资金记录失败'];
            }

            //调用联动返款接口,返款给投资用户
            $fkResp = $ump->fankuan($order->sn, $record->refund_time, $order->online_pid, $_orders[$key]['epayUserId'], $order->benxi);

            if ($fkResp->isSuccessful()) {
                $transaction->commit();
            } else {
                $transaction->rollBack();

                return ['result' => 0, 'message' => $fkResp->get('ret_msg')];
            }
        }

        $_repaymentrecord = OnlineRepaymentRecord::find()->where(['online_pid' => $pid, 'status' => OnlineRepaymentRecord::STATUS_DID])->groupBy('uid');
        $data = $_repaymentrecord->select('uid')->all();
        $product = OnlineProduct::findOne($pid);
        $sms = new SmsMessage([
            'level' => SmsMessage::LEVEL_LOW,
        ]);

        foreach ($data as $val) {
            $user = User::findOne($val->uid);
            $data_arr = $_repaymentrecord->having(['uid' => $val['uid']])->select('sum(benjin) as benjin, sum(lixi) as lixi')->andWhere(['qishu' => $qishu])->createCommand()->queryAll();

            $_sms = clone $sms;
            if (OnlineProduct::REFUND_METHOD_DAOQIBENXI === $product->refund_method) {
                $_sms['template_id'] = Yii::$app->params['sms']['daoqibenxi'];
                $message = [
                    $user->real_name,
                    $product->title,
                    $data_arr[0]['benjin'],
                    $data_arr[0]['lixi'],
                    Yii::$app->params['contact_tel'],
                ];
            } elseif (0 === $sum_benxi_yue) {
                $_sms['template_id'] = Yii::$app->params['sms']['lfenqihuikuan'];
                $message = [
                    $user->real_name,
                    $product->title,
                    $qishu,
                    $data_arr[0]['benjin'],
                    $data_arr[0]['lixi'],
                    Yii::$app->params['contact_tel'],
                ];
            } else {
                $_sms['template_id'] = Yii::$app->params['sms']['fenqihuikuan'];
                $message = [
                    $user->real_name,
                    $product->title,
                    $qishu,
                    $data_arr[0]['lixi'],
                    Yii::$app->params['contact_tel'],
                ];
            }

            $_sms->uid = $user->id;
            $_sms->mobile = $user->mobile;
            $_sms->message = json_encode($message);
            $_sms->save();
        }

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
        if (!in_array($product->status, [OnlineProduct::STATUS_FULL, OnlineProduct::STATUS_FOUND, OnlineProduct::STATUS_HUAN])) {
            return ['res' => 0, 'msg' => '标的状态异常，当前状态码：'.$product->status];
        }
        bcscale(14);
        $bcround = new BcRound();
        if (OnlineFangkuan::STATUS_EXAMINED === (int) $fk->status) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                LoanService::updateLoanState($product, OnlineProduct::STATUS_HUAN);
            } catch (\Exception $ex) {
                $transaction->rollBack();

                return ['res' => 0, 'msg' => $ex->getMessage()];
            }

            $ua = UserAccount::findOne(['uid' => $product->borrow_uid, 'type' => UserAccount::TYPE_BORROW]);
            $ua->account_balance = $bcround->bcround(bcadd($ua->account_balance, $product->money), 2);
            $ua->available_balance = $bcround->bcround(bcadd($ua->available_balance, $product->funded_money), 2);
            $ua->drawable_balance = $bcround->bcround(bcadd($ua->drawable_balance, $product->money), 2);
            $ua->in_sum = $bcround->bcround(bcadd($ua->in_sum, $product->money), 2);
            if (!$ua->save()) {
                $transaction->rollBack();

                return ['res' => 0, 'msg' => '更新用户融资账户异常'];
            }
            OnlineFangkuan::updateAll(['status' => OnlineFangkuan::STATUS_FANGKUAN], ['online_product_id' => $pid]); //将所有放款批次变为已经放款
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

                return ['res' => 0, 'msg' => '资金记录失败'];
            }

            $resp = Yii::$container->get('ump')->loanTransferToMer($fk);
            if (!$resp->isSuccessful()) {
                $transaction->rollBack();

                return ['res' => 0, 'msg' => '联动一侧：'.$resp->get('ret_msg')];
            }
            $transaction->commit();
        } elseif (OnlineFangkuan::STATUS_FANGKUAN === (int) $fk->status) {
            //放款未执行提现的
            //如果执行此步骤，将会执行提现,提现申请成功会修改放款状态为受理中STATUS_TIXIAN_APPLY
        } elseif (OnlineFangkuan::STATUS_TIXIAN_SUCC === (int) $fk->status) {
            return ['res' => 0, 'msg' => '放款金额已汇入借款人账户'];
        } elseif (OnlineFangkuan::STATUS_TIXIAN_APPLY === (int) $fk->status) {
            return ['res' => 0, 'msg' => '放款金额正在汇款中'];
        } else {
            return ['res' => 0, 'msg' => '放款操作必须是审核通过的'];
        }
        $res = OnlinefangkuanController::actionInit($pid);
        if (1 === $res['res']) {
            return [
                 'res' => 1,
                 'msg' => '放款成功',
             ];
        } else {
            return $res;
        }
    }
}