<?php

namespace backend\modules\repayment\controllers;

use backend\controllers\BaseController;
use backend\modules\order\controllers\OnlinefangkuanController;
use backend\modules\order\core\FkCore;
use common\event\LoanEvent;
use common\event\RepayEvent;
use common\lib\bchelp\BcRound;
use common\lib\err\Err;
use common\models\adminuser\AdminLog;
use common\models\message\RepaymentMessage;
use common\models\order\OnlineOrder;
use common\models\payment\Repayment;
use common\models\product\OnlineProduct;
use common\models\order\OnlineRepaymentRecord;
use common\models\order\OnlineRepaymentPlan;
use common\models\order\OnlineFangkuan;
use common\models\payment\PaymentLog;
use common\models\user\MoneyRecord;
use common\models\user\User;
use common\models\user\UserAccount;
use common\service\LoanService;
use common\service\SmsService;
use common\utils\SecurityUtils;
use common\utils\TxUtils;
use Lhjx\Noty\Noty;
use Yii;
use yii\helpers\ArrayHelper;

class RepaymentController extends BaseController
{
    /**
     * 还款计划详情页.
     */
    public function actionIndex($pid)
    {
        if (empty($pid)) {
            throw $this->ex404();     //参数无效,抛出404异常
        }

        $deal = OnlineProduct::find()->where(['id' => $pid])->one();
        $model = (new \yii\db\Query())
                ->select('orp.*,u.real_name,u.safeMobile')
                ->from(['online_repayment_plan orp'])
                ->innerJoin('user u', 'orp.uid=u.id')
                ->where(['orp.online_pid' => $pid])->all();

        if (null === $deal || empty($model)) {
            throw $this->ex404();     //对象为空时,抛出404异常
        }

        $total_bj = 0;
        $total_lixi = 0;
        $total_origin_lixi = 0;
        $total_bx = 0;
        $couponsAmount = 0;
        $allBonusProfits = [];
        bcscale(14);
        $bcround = new BcRound();
        $qimodel = null;
        $today = date('Y-m-d');
        $days = $deal->getHoldingDays($today);//计算当前时间到计息日期的天数
        $isRefreshCalcLiXi = $this->isRefreshCalcLiXi($deal, $today);
        //查找还款批次表的期数及回款状态
        $repayment = Repayment::find()
            ->where(['loan_id' => $pid])
            ->indexBy('term')
            ->asArray()
            ->all();
        //查找还款计划表最后一期的信息
        $maxQiModel = OnlineRepaymentPlan::find()
            ->where(['qishu' => count($repayment)])
            ->andWhere(['online_pid' => $pid])
            ->asArray()
            ->all();
        //根据最后一期计算加息金额
        foreach ($maxQiModel as $value) {
            $onlineOrder = OnlineOrder::findOne($value['order_id']);
            $orderBonusProfit = $onlineOrder->getBonusProfit();
            $couponsAmount += $orderBonusProfit;
            $allBonusProfits[$value['order_id']] = $orderBonusProfit;
        }
        foreach ($model as $val) {
            $qishu = intval($val['qishu']);
            $total_origin_lixi = bcadd($total_origin_lixi, $val['lixi'], 14);
            $val['origin_lixi'] = max($val['lixi'], 0.01);
            //当没有还过款时候才重新计算利息
            $payed = $repayment[$qishu]['isRefunded'];
            if ($isRefreshCalcLiXi) {
                if (!$payed) {
                    $bonusProfit = $allBonusProfits[$val['order_id']];
                    if ($orderBonusProfit > 0) {
                        $val['lixi'] = max(0.01, $bcround->bcround(bcadd($bonusProfit, bcdiv(bcmul($days, bcsub($val['lixi'], $bonusProfit, 14), 14), $deal->expires, 14), 14), 2));
                    } else {
                        $val['lixi'] = max(0.01, $bcround->bcround(bcdiv(bcmul($days, $val['lixi'], 14), $deal->expires, 14), 2));
                    }
                    $val['lixi'] = max($val['lixi'], 0.01);
                    $val['benxi'] = bcadd($val['lixi'], $val['benjin'], 2);
                }
            }
            
            //分期产品提前还款只计算当期利息
            if ($deal->isAmortized()
            		&& $today <= date('Y-m-d',strtotime($repayment[$qishu]['dueDate']." -1 month"))
            		) {
            	$val['lixi'] = 0;
            	$val['benxi'] = bcadd($val['lixi'], $val['benjin'], 2);
            }
            
            $val['payed'] = (bool) $payed;
            $total_bj = bcadd($total_bj, $val['benjin'], 14);
            $total_lixi = bcadd($total_lixi, $val['lixi'], 14);
            $total_bx = bcadd($total_bj, $total_lixi, 14);
            $qimodel[$val['qishu']][] = $val;
        }
        //应还款人数
        $count = OnlineRepaymentPlan::find()->select('uid')->where(['online_pid' => $pid])->groupBy('uid')->count();

        return $this->render('liebiao', [
            'count' => $count,
            'yhbj' => $bcround->bcround($total_bj, 2),
            'yhlixi' => $bcround->bcround($total_lixi, 2),
            'total_bx' => $bcround->bcround($total_bx, 2),
            'couponsAmount' => $bcround->bcround($couponsAmount, 2),
            'total_origin_lixi' => $bcround->bcround($total_origin_lixi, 2),
            'deal' => $deal,
            'model' => $qimodel,
            'isInGracePeriod' => $deal->isInGracePeriod(),
        ]);
    }

    //是否需要重新计息
    private function isRefreshCalcLiXi(OnlineProduct $deal, $repayDate)
    {
        if (!$deal->isAmortized()
            && $deal->is_jixi
            && $repayDate >= date('Y-m-d', $deal->jixi_time)
            && $repayDate < date('Y-m-d', $deal->finish_date)
        ) {
            return true;
        }
        return false;
    }

    /**
     * 还款操作
     *
     * @param string $pid   标的ID
     * @param string $qishu 待还款期数
     *
     * 索引：
     * 初始阶段：数据检查及还款信息数据整理
     *      前台请求方式判断
     *      请求参数初始化
     *      检查对应标的该期是否可以还款
     *      获得还款准备的必要数据信息
     * 第一步：融资者回款到标的账户
     * 第二步：平台加息券贴现到标的账户
     * 第三步：标的账户返款到投资人账户
     * 第四步：更新用户资产回款状态及发送短信消息及微信推送
     *
     * @return array
     * [
     *      'result' => 0 or 1, //0 成功 1 失败
     *      'message' => '',
     * ]
     */
    public function actionDorepayment()
    {
        /** 前台请求方式判断 */
        if (!Yii::$app->request->isPost) {
            return [
                'result' => 0,
                'message' => '非法请求',
            ];
        }


        /** 请求参数初始化（待还款标的ID，待还款期数） */
        $pid = (int) Yii::$app->request->post('pid');
        $qishu = (int) Yii::$app->request->post('qishu');
        $loan = OnlineProduct::findOne($pid);

        try {
            /** 确认计息后直接还款 - 放款审核、放款环节 */
            if (null === $loan) {
                throw new \Exception('标的不存在');
            }

            if ($loan->flexRepay) {
                //放款审核操作
                if ($loan->allowFkExamined()) {
                    $fkcore = new FkCore();
                    $createfk = $fkcore->createFk($this->admin_id, $pid, 1);
                    if (0 === $createfk['res']) {
                        throw new \Exception($createfk['msg']);
                    }
                }

                //放款操作
                $loan->refresh();
                if ($loan->allowFk()) {
                    $fk = $loan->fangkuan;
                    if (null === $fk) {
                        throw new \Exception('放款记录不存在');
                    }
                    $this->loanToMer($fk, $loan);
                }
            }
        } catch (\Exception $ex) {
            return [
                'result' => 0,
                'message' => '【30000】还款失败：'.$ex->getMessage(),
            ];
        }

        /** 检查对应标的该期是否可以还款，并返回对应的标的信息及对应标的期数的还款计划以及标的还款信息 */
        try {
            $repaymentInfo = $this->checkAllowRepayment($pid, $qishu);
        } catch (\Exception $ex) {
            return [
                'result' => 0,
                'message' => '【40000】'.$ex->getMessage(),
            ];
        }

        /** 获得还款准备的必要数据信息 */
        $loan = $repaymentInfo['loan'];
        $plans = $repaymentInfo['plans'];
        $repayment = $repaymentInfo['repayment'];

        /** 第一步：融资者回款到标的账户 */
        try {
            $this->borrowerRefundToLoan($loan, $plans, $repayment->id);
        } catch (\Exception $ex) {
            return [
                'result' => 0,
                'message' => '【40000-01】'.$ex->getMessage(),
            ];
        }

        /** 第二步：平台加息券贴现到标的账户 */
        try {
            $this->bonusDiscount($loan, $repayment->id);
        } catch (\Exception $ex) {
            return [
                'result' => 0,
                'message' => '【40000-02】'.$ex->getMessage(),
            ];
        }

        /** 第三步：标的账户返款到投资人账户 */
        $isLhwxLoan = in_array($loan->sn, Yii::$app->params['lhwt_loan_sns']);
        try {
            $this->loanRefundToLender($loan, $plans, $repayment->id, $isLhwxLoan);
        } catch (\Exception $ex) {
            return [
                'result' => 0,
                'message' => '【40000-03】'.$ex->getMessage(),
            ];
        }

        /** 第四步：更新用户资产回款状态及发送短信消息及微信推送 */

        //更新资产回款状态（TX）
        $this->updateAssetRepaidStatus($loan);

        if (Yii::$app->params['microSystem.callback']) {
            //触发还款回调
            $repayEvent = new RepayEvent([
                'loan' => $loan,
                'term' => $qishu,
            ]);
            Yii::$app->trigger('hkSuccess', $repayEvent);
        }

        //非立合旺通投资用户
        if (!$isLhwxLoan) {
            //还款短信
            $userIds = array_unique(ArrayHelper::getColumn($plans, 'uid'));
            $this->sendRefundSms($loan, $qishu, $userIds);

            //微信推送给还款成功信息投资者
            $this->repaySuccessPush($plans, $repayment);
        }


        return [
            'result' => 1,
            'message' => '还款成功',
        ];
    }

    /**
     * 发送回款短信
     *
     * @param OnlineProduct $loan    标的
     * @param int           $term    期数
     * @param array         $userIds 待发送用户ID
     *
     * @return bool
     */
    private function sendRefundSms($loan, $term, $userIds)
    {
        if (empty($userIds)) {
            return false;
        }

        $repaymentRecords = OnlineRepaymentRecord::find()
            ->select("sum(benjin) as benjin, sum(lixi) as lixi, uid")
            ->where([
                'online_pid' => $loan->id,
                'status' => [OnlineRepaymentRecord::STATUS_DID, OnlineRepaymentRecord::STATUS_BEFORE]
            ])->andWhere([
                'qishu' => $term
            ])->andFilterWhere([
                'in', 'uid', $userIds
            ])->groupBy('uid')
            ->asArray()
            ->all();
        $lastTerm = (int) Repayment::find()
            ->where(['loan_id' => $loan->id])
            ->max('term');

        foreach ($repaymentRecords as $repaymentRecord) {
            $user = User::findOne($repaymentRecord['uid']);
            if (OnlineProduct::REFUND_METHOD_DAOQIBENXI === $loan->refund_method) {
                //到期本息
                $templateId = Yii::$app->params['sms']['daoqibenxi'];
                $message = [
                    $user->real_name,
                    $loan->title,
                    $repaymentRecord['benjin'],
                    $repaymentRecord['lixi'],
                    Yii::$app->params['platform_info.contact_tel'],
                ];
            } elseif ($lastTerm === $term) {
                //分期最后一期
                $templateId = Yii::$app->params['sms']['lfenqihuikuan'];
                $message = [
                    $user->real_name,
                    $loan->title,
                    $term,
                    $repaymentRecord['benjin'],
                    $repaymentRecord['lixi'],
                    Yii::$app->params['platform_info.contact_tel'],
                ];
            } elseif(OnlineProduct::REFUND_METHOD_DEBX === $loan->refund_method) {
                //等额本息不是最后一期
                $templateId = Yii::$app->params['sms']['debx_repay'];
                $message = [
                    $user->real_name,
                    $loan->title,
                    $term,
                    $repaymentRecord['benjin'],
                    $repaymentRecord['lixi'],
                    Yii::$app->params['platform_info.contact_tel'],
                ];
            } else {
                //分期(不是等额本息)不是最后一期
                $templateId = Yii::$app->params['sms']['fenqihuikuan'];
                $message = [
                    $user->real_name,
                    $loan->title,
                    $term,
                    $repaymentRecord['lixi'],
                    Yii::$app->params['platform_info.contact_tel'],
                ];
            }

            SmsService::send(SecurityUtils::decrypt($user->safeMobile), $templateId, $message, $user);
        }
    }

    /**
     * 更新资产回款状态（TX）
     *
     * @param OnlineProduct $loan 标的
     *
     * @return array
     */
    private function updateAssetRepaidStatus($loan)
    {
        $repayment = Repayment::find()
            ->where(['isRefunded' => false])
            ->orWhere(['isRepaid' => false])
            ->andWhere(['loan_id' => $loan->id])
            ->one();
        if (null === $repayment) {
            $response = \Yii::$container->get('txClient')->post('assets/update-repaid-status', [
                'loan_id' => $loan->id,
            ], function (\Exception $e) {
                $code = $e->getCode();
                if (200 !== $code) {
                    return false;
                }
            });
            if (!$response) {
                return [
                    'result' => 0,
                    'message' => '更新用户资产回款状态失败'
                ];
            }
        }
    }

    /**
     * 微信推送给还款成功信息投资者
     *
     * @param array     $plans     还款计划
     * @param Repayment $repayment 还款信息
     *
     * @return void
     */
    private function repaySuccessPush($plans, $repayment)
    {
        $repayment->refresh();
        if ($repayment->isRepaid && $repayment->isRefunded) {
            foreach ($plans as $plan) {
                Noty::send(new RepaymentMessage($plan));
            }
        }
    }

    /**
     * 检查某个标的第几期是否允许还款，返回检查过后的标的信息和还款计划
     *
     * @param int $loanId 标的id
     * @param int $term   期数
     *
     * 返回信息形如：
     * [
     *      'loan' => $loan,
     *      'plans' => $plans,
     *      'repayment' => $repayment,
     * ]
     * @return array
     * @throws \Exception
     */
    private function checkAllowRepayment($loanId, $term)
    {
        //判断参数是否错误
        if ($loanId <= 0 || $term <= 0) {
            throw new \Exception('还款参数错误');
        }

        //检查还款时间段【1:00-23:00才可进行还款处理】
        $h = intval(date('H'));
        if ($h < 1 || $h >= 23) {
            throw new \Exception('只能在01:00-22:59期间进行确认还款操作');
        }

        //检查标的是否存在
        $loan = OnlineProduct::findOne(['id' => $loanId]);
        if (null === $loan) {
            throw new \Exception('标的信息不存在');
        }

        //检查标的状态是否允许还款
        if (!in_array($loan->status, [OnlineProduct::STATUS_HUAN, OnlineProduct::STATUS_OVER])) {
            throw new \Exception('当前标的状态不允许此操作');
        }

        //检查还款计划是否存在
        $plans = OnlineRepaymentPlan::find()
            ->where(['online_pid' => $loanId])
            ->andWhere(['status' => OnlineRepaymentPlan::STATUS_WEIHUAN])
            ->andWhere(['qishu' => $term])
            ->all();
        if (0 === count($plans)) {
            throw new \Exception('没有需要还款的项目');
        }

        //检查是否跨期还款
        $minTerm = OnlineRepaymentPlan::find()
            ->where(['online_pid' => $loanId])
            ->andWhere(['status' => OnlineRepaymentPlan::STATUS_WEIHUAN])
            ->min('qishu');
        if ($term !== (int) $minTerm) {
            throw new \Exception('不允许跨期还款');
        }

        //检查还款记录
        $repayment = Repayment::find()
            ->where(['loan_id' => $loanId, 'term' => $term])
            ->one();
        if (null === $repayment) {
            throw new \Exception('还款信息不存在');
        }

        return [
            'loan' => $loan,
            'plans' => $plans,
            'repayment' => $repayment,
        ];
    }

    /**
     * 借款用户返款到对应标的账户
     *
     * @param OnlineProduct $loan
     * @param array         $plans
     * @param integer       $repaymentId
     *
     * @return bool
     * @throws \Exception
     */
    private function borrowerRefundToLoan(OnlineProduct $loan, $plans, $repaymentId)
    {
        //检查融资者返款到账户是否完成，若完成直接返回true，进行下一步
        $repaidRepayment = Repayment::find()
            ->where(['id' => $repaymentId])
            ->one();
        if (true === (bool) $repaidRepayment->isRepaid) {
            return true;
        }

        //检查融资者用户信息
        $borrower = $loan->repayer;
        if (null === $borrower) {
            throw new \Exception('还款用户信息不存在');
        }
        //检查融资者用户账户信息
        $borrowerAccount = $borrower->borrowAccount;
        if (null === $borrowerAccount) {
            throw new \Exception('还款用户账户信息不存在');
        }
        //检查融资者对应联动账户信息
        $borrowerEpayUser = $borrower->epayUser;
        if (null === $borrowerEpayUser) {
            throw new \Exception('还款对应联动账户信息不存在');
        }
        
        //查找还款批次表的期数及回款状态
        $repayment = Repayment::find()
        ->where(['loan_id' => $loan->id])
        ->indexBy('term')
        ->asArray()
        ->all();

        //当不允许访问联动时候，默认联动测处理成功，查看联动标的状态
        $ump = Yii::$container->get('ump');
        if (Yii::$app->params['ump_uat']) {
            //调用联动接口,查询标的信息
            $loanResp = $ump->getLoanInfo($loan->id);
            if (!$loanResp->isSuccessful()) {
                throw new \Exception($loanResp->get('ret_msg'));
            }
            if ('02' === $loanResp->get('project_account_state')) {
                throw new \Exception('当前联动标的状态为冻结状态');
            }
            if ('2' !== $loanResp->get('project_state')) {
                throw new \Exception('当前联动标的状态不允许此操作');
            }
        }

        //计算融资人实际需要偿付的金额
        $bcround = new BcRound();
        $today = date('Y-m-d');
        //算出是否要重新计息及实际需要还款的天数
        $days = $loan->getHoldingDays($today);
        $isRefreshCalcLiXi = $this->isRefreshCalcLiXi($loan, $today);
        $totalFund = 0; //融资人需要扣除的金额
        $totalBenxi = 0; //当前应还所有本息和
        $totalBonusProfit = 0; //当前加息券收益和
        $lastTerm = (int) OnlineRepaymentPlan::find()
            ->where(['online_pid' => $loan->id])
            ->andWhere(['status' => OnlineRepaymentPlan::STATUS_WEIHUAN])
            ->max('qishu');
        $currentTermIsFinal = $lastTerm === $repaidRepayment->term;  //当前是否是最后一期

        foreach ($plans as $plan) {
            $orderBonusProfit = 0;
            if ($plan->qishu === $lastTerm) {
                $order = $plan->order;
                //转让暂未处理
                $orderBonusProfit = $order->getBonusProfit();
                $totalBonusProfit = bcadd($totalBonusProfit, $orderBonusProfit, 2);
            }
            if ($isRefreshCalcLiXi) {
                if ($orderBonusProfit > 0) {
                    $plan->lixi = max(0.01, $bcround->bcround(bcadd($orderBonusProfit, bcdiv(bcmul($days, bcsub($plan->lixi, $orderBonusProfit, 14), 14), $loan->expires, 14), 14), 2));
                } else {
                    $plan->lixi = max(0.01, $bcround->bcround(bcdiv(bcmul($days, $plan->lixi, 14), $loan->expires, 14), 2));
                }
                $plan->benxi = bcadd($plan->benjin, $plan->lixi, 2);
                $plan->refund_time = time();
            }
            //分期产品提前还款只计算当期利息
            if ($loan->isAmortized()
            		&& $today <= date('Y-m-d',strtotime($repayment[$plan->qishu]['dueDate']." -1 month"))
            		) {
            			$plan->lixi = 0;
            			$plan->benxi = bcadd($plan->benjin, $plan->lixi, 2);
            }
            
            $totalBenxi = bcadd($totalBenxi, $plan->benxi, 2);
        }
        $totalFund = bcsub($totalBenxi, $totalBonusProfit, 2);

        //判断温都融资者账户余额是否足够
        if (bccomp($totalFund, $borrowerAccount->available_balance, 2) > 0) {
            throw new \Exception('还款用户账户余额不足');
        }
        //判断实际联动方融资者账户信息是否异常
        if (Yii::$app->params['ump_uat']) {
            if(User::USER_TYPE_PERSONAL === $borrower->type){//个人
                $orgResp = $ump->getUserInfo($borrowerEpayUser->epayUserId);
            }else{
                $orgResp = $ump->getMerchantInfo($borrowerEpayUser->epayUserId);
            }
            if ($orgResp->isSuccessful()) {
                if ('1' !== $orgResp->get('account_state')) {
                    throw new \Exception('当前联动端融资者账户状态异常');
                }
                //没有还款时候才需要判断融资方信息
                if (0 > bccomp($orgResp->get('balance'), $totalFund * 100, 2)) {
                    throw new \Exception('当前联动端融资者余额不足');
                }
            } else {
                throw new \Exception($orgResp->get('ret_msg'));
            }
        }

        //只有当前是还款中状态才可以还款
        if (OnlineProduct::STATUS_HUAN === $loan->status) {
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                //如果在宽限期内，需要将重新计算的还款计划保存，以及将还款记录更新还款金额及利息
                if ($isRefreshCalcLiXi || $loan->isAmortized()) {
                    $totalLixi = 0;
                    foreach ($plans as $plan) {
                        $totalLixi = bcadd($totalLixi, $plan->lixi, 2);
                        $plan->save(false);
                    }

                    if ($loan->isInGracePeriod() || $repaidRepayment->interest !== $totalLixi) {
                        //更新还款记录还款本息金额和以及利息
                        $sql = "update repayment set interest=:interest,amount=:amount where id = :repaymentId";
                        $updateRepayment = $db->createCommand($sql, [
                            'interest' => $totalLixi,
                            'amount' => $totalBenxi,
                            'repaymentId' => $repaymentId,
                        ])->execute();
                        if (!$updateRepayment) {
                            $transaction->rollBack();
                            throw new \Exception('宽限期内更新还款记录还款本息和及利息异常');
                        }
                    }
                }

                //如果当前计算到的加息券利息大于0，记录payment_log
                if ($totalBonusProfit > 0) {
                    $paymentLog = new PaymentLog([
                        'txSn' => TxUtils::generateSn('B'),
                        'amount' => $totalBonusProfit,
                        'toParty_id' => $loan->borrow_uid,
                        'loan_id' => $loan->id,
                        'ref_type' => 1,
                        'ref_id' => $repaymentId,
                    ]);
                    if (!$paymentLog->save()) {
                        $transaction->rollBack();
                        throw new \Exception('加息券交易记录生成失败');
                    }
                }

                //更新温都融资者账户余额
                $updateBorrowerAccountSql = "update user_account set account_balance=account_balance-:totalRefund,available_balance=available_balance-:totalRefund,drawable_balance=drawable_balance-:totalRefund,out_sum=out_sum+:totalRefund where id=:borrowerAccountId";
                $updateBorrowerAccount = $db->createCommand($updateBorrowerAccountSql, [
                    'totalRefund' => $totalFund,
                    'borrowerAccountId' => $borrowerAccount->id,
                ])->execute();
                if (!$updateBorrowerAccount) {
                    $transaction->rollBack();
                    throw new \Exception('当前融资者账户余额扣款异常');
                }

                //更新融资者账户资金还款流水记录
                $borrowerAccount->refresh();
                $borrowerMoneyRecord = new MoneyRecord([
                    'account_id' => $borrowerAccount->id,
                    'sn' => MoneyRecord::createSN(),
                    'type' => MoneyRecord::TYPE_HUANKUAN,
                    'osn' => '',
                    'uid' => $borrowerAccount->uid,
                    'out_money' => $totalFund,
                    'balance' => $borrowerAccount->available_balance,
                    'remark' => '还款记录ID：'.$repaymentId.',还款总计:'.$totalFund.'元',
                ]);
                if (!$borrowerMoneyRecord->save()) {
                    throw new \Exception('还款资金流水记录失败');
                }

                //如果是最后一期，应更新标的状态
                if ($currentTermIsFinal) {
                    //记录状态更新日志
                    $updateData = ['status' => OnlineProduct::STATUS_OVER, 'sort' => OnlineProduct::SORT_YHK];
                    $log = AdminLog::initNew(['tableName' => OnlineProduct::tableName(), 'primaryKey' => $loan->id], Yii::$app->user, $updateData);
                    $logLoan = $log->save();
                    if (!$logLoan) {
                        throw new \Exception('后台操作日志：修改标的状态从还款中到已还清记录失败');
                    }
                    $updateLoanSql = "update online_product set status=:status,sort=:sort where id=:loanId and status=5";
                    $updateLoan = $db->createCommand($updateLoanSql, [
                        'status' => OnlineProduct::STATUS_OVER,
                        'sort' => OnlineProduct::SORT_YHK,
                        'loanId' => $loan->id,
                    ])->execute();
                    if (!$updateLoan) {
                        $transaction->rollBack();
                        throw new \Exception('修改标的状态从还款中到已还清错误');
                    }
                }

                //联动一测融资用户还款到标的账户
                if ($totalFund > 0 && Yii::$app->params['ump_uat']) {
                    if ($loan->borrow_uid === $borrower->id) {
                    	Yii::info($borrowerEpayUser->epayUserId, 'xiaowei');
                    	
                    	if(User::USER_TYPE_PERSONAL === $borrower->type){//个人
                    		$hk = $ump->huankuan_gr(
                    				TxUtils::generateSn('HK'),
                    				$loan->id,
                    				$borrowerEpayUser->epayUserId,
                    				$totalFund);
                    		Yii::info($hk, 'xiaowei');
                    	}
                    	else {
                    		$hk = $ump->huankuan(
                    				TxUtils::generateSn('HK'),
                    				$loan->id,
                    				$borrowerEpayUser->epayUserId,
                    				$totalFund);
                    	}
                    	Yii::info($hk, 'xiaowei');
                    } else {
                        $hk = $ump->refundViaAltRepayer(
                            TxUtils::generateSn('HK'),
                            $loan->id,
                            $borrowerEpayUser->epayUserId,
                            $totalFund);
                    }

                    if (!$hk->isSuccessful()) {
                        $transaction->rollBack();
                        throw new \Exception($hk->get('ret_code').$hk->get('ret_msg'));
                    }
                }

                //更新还款记录还款本息金额和以及利息
                $sql = "update repayment set isRepaid=:isRepaid,repaidAt=:repaidAt where id = :repaymentId and isRepaid=false";
                $updateRepayment = $db->createCommand($sql, [
                    'isRepaid' => true,
                    'repaidAt' => date('Y-m-d H:i:s'),
                    'repaymentId' => $repaymentId,
                ])->execute();
                if (!$updateRepayment) {
                    $transaction->rollBack();
                    throw new \Exception('更新还款记录还款账户回款状态失败');
                }

                $transaction->commit();
            } catch (\Exception $ex) {
                $transaction->rollBack();
                throw $ex;
            }

            return true;
        }
    }

    /**
     * 平台加息券贴现到标的账户
     *
     * @param OnlineProduct $loan        标的
     * @param int           $repaymentId 还款记录ID
     *
     * @return bool
     * @throws \Exception
     */
    private function bonusDiscount($loan, $repaymentId)
    {
        //如果不存在加息券, 则直接返回true，跳过此步
        $paymentLog = PaymentLog::find()
            ->where(['ref_id' => $repaymentId])
            ->andWhere(['ref_type' => 1])
            ->one();
        if (null === $paymentLog) {
            return true;
        }

        //没有贴现过才会进行贴现，否则直接返回true，跳过此步
        if (!$loan->isBonusAmountTransferred()) {
            $payLog = PaymentLog::findOne(['loan_id' => $loan->id, 'ref_type' => 1]);

            //当不允许访问联动时候，默认联动处理成功
            if ($payLog && Yii::$app->params['ump_uat']) {
                $ret = Yii::$container->get('ump')->merOrder($payLog);
                if (!$ret->isSuccessful()) {
                    throw new \Exception($ret->get('ret_msg'));
                }
            }
        }

        return true;
    }

    /**
     * 标的账户返款到投资人账户
     *
     * @param OnlineProduct $loan        标的
     * @param array         $plans       还款计划
     * @param int           $repaymentId 还款记录ID
     * @param boolean       $isLhwxLoan  是否为立合旺通投资标的
     *
     * @return bool
     * @throws \Exception
     */
    private function loanRefundToLender($loan, $plans, $repaymentId, $isLhwxLoan)
    {
        //检查融资者返款到账户是否完成，若完成直接返回true，进行下一步
        $refundRepayment = Repayment::find()
            ->where(['id' => $repaymentId])
            ->one();
        if (true === (bool) $refundRepayment->isRefunded) {
            return true;
        }

        $repayer = $loan->repayer;
        $isOrgUser = $repayer->id === $loan->borrow_uid;
        //获得剩余本息余额
        $restBenxi = (float) OnlineRepaymentPlan::find()
            ->where([
                'online_pid' => $loan->id,
                'status' => OnlineRepaymentPlan::STATUS_WEIHUAN
            ])->andFilterWhere([
                '<>', 'qishu', $refundRepayment->term,
            ])->sum('benxi');
        $isRefreshCalcLiXi = $this->isRefreshCalcLiXi($loan, date('Y-m-d'));
        $db = Yii::$app->db;
        $ump = Yii::$container->get('ump');
        $transaction = $db->beginTransaction();
        try {
            foreach ($plans as $plan) {
                //新建还款记录
                $lenderRepaymentRecord = new OnlineRepaymentRecord([
                    'online_pid' => $loan->id,
                    'order_id' => $plan->order_id,
                    'order_sn' => OnlineRepaymentRecord::createSN(),
                    'qishu' => $plan->qishu,
                    'uid' => $plan->uid,
                    'lixi' => $plan->lixi,
                    'benxi' => $plan->benxi,
                    'benjin' => $plan->benjin,
                    'benxi_yue' => $restBenxi,
                    'refund_time' => time(),
                    'status' => $isRefreshCalcLiXi
                        ? OnlineRepaymentRecord::STATUS_BEFORE
                        : OnlineRepaymentRecord::STATUS_DID,
                ]);
                $lenderRepaymentRecord->save(false);

                //更新还款状态
                $plan->status = $isRefreshCalcLiXi
                    ? OnlineRepaymentPlan::STATUS_TIQIAM
                    : OnlineRepaymentPlan::STATUS_YIHUAN;
                $plan->actualRefundTime = date('Y-m-d H:i:s');
                $plan->save(false);

                //更新投资人账户余额
                $user = $plan->user;
                $lendAccount = $user->lendAccount;
                $updateLendAccountSql = "update user_account set available_balance=available_balance+:benxi,drawable_balance=drawable_balance+:benxi,in_sum=in_sum+:benxi,investment_balance=investment_balance-:benjin,profit_balance=profit_balance+:lixi where id=:lendAccountId";
                $updateLendAccount = $db->createCommand($updateLendAccountSql, [
                    'benxi' => $lenderRepaymentRecord->benxi,
                    'benjin' => $lenderRepaymentRecord->benjin,
                    'lixi' => $lenderRepaymentRecord->lixi,
                    'lendAccountId' => $lendAccount->id,
                ])->execute();
                if (!$updateLendAccount) {
                    $transaction->rollBack();
                    throw new \Exception('投资人还款失败：投资人账户余额更新失败');
                }

                //添加投资人流水更新
                $lendAccount->refresh();
                $lenderMoneyRecord = new MoneyRecord([
                    'account_id' => $lendAccount->id,
                    'sn' => MoneyRecord::createSN(),
                    'type' => null !== $plan->asset_id ? MoneyRecord::TYPE_CREDIT_HUIKUAN : MoneyRecord::TYPE_HUIKUAN,
                    'osn' => null !== $plan->asset_id ? $plan->asset_id : $plan->sn,
                    'uid' => $plan->uid,
                    'in_money' => $lenderRepaymentRecord->benxi,
                    'balance' => $lendAccount->available_balance,
                    'remark' => '第'.$plan->qishu.'期'.'本金:'.$lenderRepaymentRecord->benjin.'元;利息:'.$lenderRepaymentRecord->lixi.'元;',
                ]);
                $lenderMoneyRecord->save(false);

                //判断是否是联动正式环境，然后返款给投资用户
                if ($plan->benxi > 0 && Yii::$app->params['ump_uat']) {
                    //调用联动返款接口,返款给投资用户
                    //判断是否为立合旺通企业投资者投资标的
                    if ($isLhwxLoan) {
                        $fkResp = $ump->fkToOrgUser($plan->sn, $lenderRepaymentRecord->refund_time, $plan->online_pid, $user->epayUser->epayUserId, $plan->benxi);
                    } else {
                        //判断当前返款用户是否为融资用户，若不为则为偿付返款，否则为正常返款
                        if ($isOrgUser) {
                            $fkResp = $ump->fankuan($plan->sn, $lenderRepaymentRecord->refund_time, $plan->online_pid, $user->epayUser->epayUserId, $plan->benxi);
                        } else {
                            $fkResp = $ump->fankuan1($plan->sn, $lenderRepaymentRecord->refund_time, $plan->online_pid, $user->epayUser->epayUserId, $plan->benxi);
                        }
                    }
                    if (!$fkResp->isSuccessful()) {
                        $transaction->rollBack();
                        throw new \Exception($fkResp->get('ret_msg'));
                    }
                }
            }
            $updateRefundRepaymentSql = "update repayment set isRefunded=:isRefunded,refundedAt=:refundedAt where id = :repaymentId and isRefunded=false";
            $updateRefundRepayment = $db->createCommand($updateRefundRepaymentSql, [
                'repaymentId' => $repaymentId,
                'isRefunded' => true,
                'refundedAt' => date('Y-m-d H:i:s'),
            ])->execute();
            if (!$updateRefundRepayment) {
                throw new \Exception('更新');
            }
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

    /**
     * 是否可以放款.
     */
    private function allowFk(OnlineProduct $loan)
    {
        return in_array($loan->status, [
            OnlineProduct::STATUS_FULL,
            OnlineProduct::STATUS_FOUND,
            OnlineProduct::STATUS_HUAN,
        ]);
    }

    /**
     * 判断放款记录的状态.
     */
    private function validateFkStatus(OnlineFangkuan $fk)
    {
        $err = '';

        switch ($fk->status) {
            case OnlineFangkuan::STATUS_EXAMINED:
            case OnlineFangkuan::STATUS_FANGKUAN:
            case OnlineFangkuan::STATUS_TIXIAN_FAIL:
                break;
            case OnlineFangkuan::STATUS_TIXIAN_SUCC:
                $err = '放款金额已汇入借款人账户';
                break;
            case OnlineFangkuan::STATUS_TIXIAN_APPLY:
                $err = '放款金额正在汇款中';
                break;
            default:
                $err = '放款操作必须是审核通过的';
        }

        return $err;
    }

    /**
     * 标的放款.
     */
    public function actionFk()
    {
        $pid = Yii::$app->request->post('pid');

        if (empty($pid)) {
            return ['res' => 0, 'msg' => $this->code('000001').'参数异常'];
        }

        $product = OnlineProduct::findOne($pid);
        $fk = OnlineFangkuan::findOne(['online_product_id' => $pid]);

        if (null === $product || null === $fk) {
            return ['res' => 0, 'msg' => $this->code('000002').'找不到对应的标的或放款记录'];
        }

        if (!$this->allowFk($product)) {
            return ['res' => 0, 'msg' => $this->code('000004').'标的状态异常，当前状态码：'.$product->status];
        }

        $fkError = $this->validateFkStatus($fk);

        if ($fkError) {
            return ['res' => 0, 'msg' => $this->code('000005').$fkError];
        }

        try {
            $this->loanToMer($fk, $product);  //标的放款
        } catch (\Exception $e) {
            $code = $e->getCode() ? $this->code($e->getCode()) : '';

            return ['res' => 0, 'msg' => $code.$e->getMessage()];
        }

        $drawBack = OnlinefangkuanController::actionInit($pid);

        if (Yii::$app->params['microSystem.callback']) {
            //触发放款回调
            $loanEvent = new LoanEvent([
                'loan' => $product,
            ]);
            Yii::$app->trigger('fkSuccess', $loanEvent);
        }

        return [
            'res' => $drawBack['res'],
            'msg' => $drawBack['res'] ? '放款成功' : $drawBack['msg'],
        ];
    }

    /**
     * 标的账户放款到融资用户账户.
     */
    private function loanToMer(OnlineFangkuan $fk, OnlineProduct $product)
    {
        $fangkuanFang = $product->getFangKuanFang();
        if (null === $fangkuanFang) {
            throw new \Exception('无有效放款方，请重新设置', '000003');
        }

        if (OnlineFangkuan::STATUS_EXAMINED === $fk->status) {
            //没有贴现过才会进行贴现
            if (!$product->isCouponAmountTransferred()) {
                $payLog = PaymentLog::findOne(['loan_id' => $product->id, 'ref_type' => 0]);

                //当不允许访问联动时候，默认联动处理成功
                if ($payLog && Yii::$app->params['ump_uat']) {
                    $ret = Yii::$container->get('ump')->merOrder($payLog);
                    if (!$ret->isSuccessful()) {
                        throw new \Exception('联动一侧：'.$ret->get('ret_msg'));
                    }
                }
            }

            $transaction = Yii::$app->db->beginTransaction();

            try {
                /** 更新联动标的状态 */
                LoanService::updateLoanState($product, OnlineProduct::STATUS_HUAN);

                /** 更新收款方账户金额 */
                $realBorrowerId = $fangkuanFang->appUserId;
                $res = Yii::$app->db->createCommand("UPDATE `user_account` SET `account_balance` = `account_balance` + :money, `available_balance` = `available_balance` + :money, `drawable_balance` = `drawable_balance` + :money, `in_sum` = `in_sum` + :money WHERE `uid` = :uid and `type` = :userType", [
                    'money' => $product->funded_money,
                    'uid' => $realBorrowerId,
                    'userType' => UserAccount::TYPE_BORROW,
                ])->execute();
                if (!$res) {
                    throw new \Exception('更新融资账户异常', '000003');
                }

                /** 更新放款状态 */
                $updateFangkuan = OnlineFangkuan::updateAll(['status' => OnlineFangkuan::STATUS_FANGKUAN], ['online_product_id' => $product->id]); //将所有放款批次变为已经放款
                if (!$updateFangkuan) {
                    throw new \Exception('更新放款批次异常', '000003');
                }

                /** 添加资金流水 */
                $ua = UserAccount::findOne(['uid' => $realBorrowerId, 'type' => UserAccount::TYPE_BORROW]);
                $moneyRecord = new MoneyRecord([
                    'type' => MoneyRecord::TYPE_FANGKUAN,
                    'sn' => MoneyRecord::createSN(),
                    'osn' => $fk->sn,
                    'account_id' => $ua->id,
                    'uid' => $realBorrowerId,
                    'in_money' => $product->funded_money,
                    'remark' => '已放款',
                    'balance' => $ua->available_balance,
                ]);
                if (!$moneyRecord->save()) {
                    throw new \Exception('资金流水记录异常', '000003');
                }

                /** 当不允许访问联动时候，默认联动处理成功 */
                if (Yii::$app->params['ump_uat']) {
                    $ump = Yii::$container->get('ump');
                    /** 添加当资金使用方不为空时，标的将放款到资金使用方，且暂不支持个人 */
                    $fkSn = $fk->getTxSn();
                    $fkDate = date('Ymd');
                    $loanId = $fk->getLoanId();
                    $amount = $fk->getAmount();
                    $borrowerInfo = $ua->user->borrowerInfo;
                    if (null === $borrowerInfo) {
                        throw new \Exception('无法判断收款方账户类型信息');
                    }
                    $borrowerType = $borrowerInfo->isPersonal() ? 1 : 2;
                    if ($product->fundReceiver) {
                        $resp = $ump->loanTransferToFundReceiver($fkSn, $fkDate, $loanId, $fangkuanFang->epayUserId, $amount);
                    } else {
                        $resp = $ump->loanTransferToMer1($fk->getTxSn(), date('Ymd'), $loanId, $fangkuanFang->epayUserId, $amount, $borrowerType);
                    }
                    if (!$resp->isSuccessful()) {
                        throw new \Exception('联动一侧：'.$resp->get('ret_msg'));
                    }
                }

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();

                throw $e;
            }
        }
    }

    private function code($code)
    {
        return '['.Err::code($code).']';
    }
}
