<?php
namespace console\controllers;

use common\models\order\OnlineOrder;
use common\models\order\OnlineRepaymentPlan;
use common\models\order\OnlineRepaymentRecord;
use common\models\payment\Repayment;
use common\models\product\OnlineProduct;
use common\models\user\MoneyRecord;
use common\models\promo\Award;
use common\models\sms\SmsMessage;
use common\models\user\User;
use common\models\user\UserAccount;
use wap\modules\promotion\models\RankingPromo;
use Wcg\Math\Bc;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * 临时脚本
 *
 * Class TestController
 * @package console\controllers
 */
class TestController extends Controller
{
    /**
     * [临时代码] 修复 “南金交--双担保国企北大青鸟集团项目” 系列标的还款数据
     *
     * 2017-06-26 日开发，用完删除 php yii test/repair-repayment
     *
     * 使用说明
     * 1. 预览检测出问题的还款数据 , 数据保存到 /tmp/repayment.csv
     * 2. 修改 online_repayment_plan\repayment 的利息和本息, 成功数据保存到 /tmp/repaymentSuccess.vsv, 失败数据保存到 /tmp/repaymentError.csv
     *
     * 需求总利息 = 投资金额 * 利率 * (项目截止日-项目计息日) / 365
     * 实际总利率 = 投资金额 * 利率 * 项目期限 / 12
     */
    public function actionRepairRepayment($run = false)
    {
        //获取相关还款计划
        $sql = "SELECT rp.id, rp.online_pid AS loan_id, rp.order_id, rp.uid AS user_id, rp.qishu AS term, rp.benjin AS principal, rp.lixi AS interest, rp.benxi AS repaymentAmount, DATE( FROM_UNIXTIME( rp.refund_time ) ) AS repaymentDate, rp.status AS repaymentStatus, rp.asset_id, p.refund_method AS repaymentMethod, p.status AS loanStatus, DATE( FROM_UNIXTIME( p.jixi_time ) ) AS startDate, DATE( FROM_UNIXTIME( p.finish_date ) ) AS endDate, p.expires, o.yield_rate AS rate,p.title
FROM online_repayment_plan AS rp
INNER JOIN online_product AS p ON rp.online_pid = p.id
INNER JOIN online_order AS o ON rp.order_id = o.id
WHERE DATEDIFF( DATE( FROM_UNIXTIME( p.finish_date ) ) , DATE( FROM_UNIXTIME( p.jixi_time ) ) ) /365 != p.expires /12
AND p.refund_method >2
AND p.isTest =0
AND p.status IN ( 5, 6 ) 
AND p.title LIKE  '%南金交--双担保国企北大青鸟集团项目%'
ORDER BY p.id ASC , rp.order_id ASC , IF( rp.asset_id, rp.asset_id, rp.uid ) ASC , rp.qishu ASC ";
        $repaymentData = \Yii::$app->db->createCommand($sql)->queryAll();
        $transferOrderIds = [];//转让的订单
        $groupedRepayment = [];
        $groupCount = 0;
        $loanData = [];
        $loanCount = 0;
        //准备数据
        foreach ($repaymentData as $repayment) {
            if (!empty($repayment['asset_id']) && !in_array($repayment['order_id'], $transferOrderIds)) {
                //是转让订单
                $transferOrderIds[] = $repayment['order_id'];
            }
            if ($repayment['principal'] > 0) {
                $groupCount++;
            }
            if (!in_array($repayment['loan_id'], $loanData)) {
                $loanData[] = $repayment['loan_id'];
                $loanCount++;
            }
        }
        //将还款计划分组
        foreach ($repaymentData as $repayment) {
            if (in_array($repayment['order_id'], $transferOrderIds)) {
                if (empty($repayment['asset_id'])) {
                    $groupedRepayment['order_' . $repayment['order_id']][$repayment['term']] = $repayment;
                } else {
                    $groupedRepayment['asset_' . $repayment['asset_id']][$repayment['term']] = $repayment;
                }
            } else {
                $groupedRepayment['order_' . $repayment['order_id']][$repayment['term']] = $repayment;
            }
        }
        $calcGroupCount = count($groupedRepayment);
        $this->stdout("有{$groupCount}个本金大于0的还款计划，有{$calcGroupCount}组还款计划，影响标的{$loanCount}个 \n");
        if ($calcGroupCount !== $groupCount) {
            $this->stderr("还款计划分组错误 \n");
            return self::EXIT_CODE_ERROR;
        }

        $file = '/tmp/repayment.csv';
        $fp = fopen($file, 'w');
        $successFile = '/tmp/repaymentSuccess.csv';
        $successFp = fopen($successFile, 'w');
        $errorFile = '/tmp/repaymentError.csv';
        $errorFp = fopen($errorFile, 'w');

        if (!$run) {
            fputcsv($fp, [
                '标的ID',
                '标的标题',
                '用户ID',
                '计息日',
                '截止日',
                '项目期限(月)',
                '订单实际利率',
                '还款方式',
                '是否是转让',
                '资产本金',
                '原按月方式计算总利息',
                '按日计息新利息',
                '应该补发利息',
            ]);
        }

        foreach ($groupedRepayment as $repayment) {
            $lastRepayment = end($repayment);//最后一期还款计划，应该是有本金的
            if (empty($lastRepayment['principal'])) {
                throw new \Exception("{$lastRepayment['id']} 最后一期没有本金");
            }
            $interest = array_sum(array_column($repayment, 'interest'));//累加得到的利息
            $exceptInterest = bcdiv(
                bcmul(
                    bcmul(
                        $lastRepayment['principal'],
                        $lastRepayment['rate'],
                        14
                    ),
                    (new \DateTime($lastRepayment['endDate']))->diff(new \DateTime($lastRepayment['startDate']))->days,
                    14
                ),
                365,
                2
            );//按日计算得到的利息
            $changeInterest = bcsub($exceptInterest, $interest, 2);//需要变更的利息
            if (bccomp($changeInterest, 0, 2) === 0) {
                $groupCount--;
                $this->stdout("变动金额为0,跳过, 剩余{$groupCount}条");
                continue;
            }
            $logData = [
                'loan_id' => $lastRepayment['loan_id'],
                'title' => $lastRepayment['title'],
                'user_id' => $lastRepayment['user_id'],
                'startDate' => $lastRepayment['startDate'],
                'endDate' => $lastRepayment['endDate'],
                'expires' => $lastRepayment['expires'],
                'rate' => $lastRepayment['rate'],
                'repaymentMethod' => isset(\Yii::$app->params['refund_method'][$lastRepayment['repaymentMethod']]) ? \Yii::$app->params['refund_method'][$lastRepayment['repaymentMethod']] : '',
                'isTransfer' => empty($lastRepayment['asset_id']) ? '否' : '是转让',
                'principal' => $lastRepayment['principal'],
                'interest' => $interest,
                'exceptInterest' => $exceptInterest,
                'changeInterest' => $changeInterest,
            ];

            if ($run) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    //更改online_repayment_plan
                    $sql = "update online_repayment_plan set lixi=lixi+:changeInterest, benxi=benxi+:changeInterest where id=:repaymentId and online_pid = :loanId and uid = :userId";
                    $affectedRows = \Yii::$app->db->createCommand($sql, [
                        'changeInterest' => $changeInterest,
                        'repaymentId' => $lastRepayment['id'],
                        'loanId' => $lastRepayment['loan_id'],
                        'userId' => $lastRepayment['user_id'],
                    ])->execute();
                    if ($affectedRows !== 1) {
                        throw new \Exception("更新{$lastRepayment['id']} online_repayment_plan 失败");
                    }
                    //更改repayment
                    $sql = "update repayment set interest = interest + :changeInterest, amount = amount + :changeInterest where loan_id = :loanId and term = :term and dueDate = :repaymentDate and isRepaid = 0 and isRefunded = 0";
                    $affectedRows = \Yii::$app->db->createCommand($sql, [
                        'changeInterest' => $changeInterest,
                        'loanId' => $lastRepayment['loan_id'],
                        'term' => $lastRepayment['term'],
                        'repaymentDate' => $lastRepayment['repaymentDate'],
                    ])->execute();
                    if ($affectedRows !== 1) {
                        throw new \Exception("更新{$lastRepayment['id']} repayment 失败");
                    }
                    fputcsv($successFp, $logData);
                    $groupCount--;
                    $transaction->commit();
                } catch (\Exception $e) {
                    $this->stderr("{$lastRepayment['id']} 更新失败, 失败信息: {$e->getMessage()}  \n");
                    $transaction->rollBack();
                    $logData['error'] = $e->getMessage();
                    fputcsv($errorFp, $logData);
                }
            } else {
                fputcsv($fp, $logData);
            }
        }
        fclose($successFp);
        fclose($errorFp);
        fclose($fp);
        return self::EXIT_CODE_NORMAL;
    }

    /**
     * 修复南金交资金流水
     * 开发于 2017-06-20 临时代码  php yii test/money-record
     */
    public function actionMoneyRecord($run = false)
    {
        $moneyRecords = MoneyRecord::find()
            ->where(['uid' => 53])
            ->andWhere(['>=', 'created_at', strtotime('2017-06-20')])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
        $lastBalance = null;
        foreach ($moneyRecords as $moneyRecord) {
            /**
             * @var MoneyRecord $moneyRecord
             */
            if (is_null($lastBalance)) {
                $lastBalance = $moneyRecord->balance;
                continue;
            }
            $change = bcsub($moneyRecord->in_money, $moneyRecord->out_money, 2);
            $exceptBalance = bcadd($lastBalance, $change, 2);
            $diff = bcsub($exceptBalance, $moneyRecord->balance, 2);


            if ($run) {
                $moneyRecord->balance = $exceptBalance;
                $moneyRecord->save(false);
                if (bccomp($diff, 0, 2) != 0) {
                    $this->stdout("ID: {$moneyRecord->id} | created_at: {$moneyRecord->created_at} | type: {$moneyRecord->type} | 上次资金: {$lastBalance} | 变动金额: {$change} | 现有金额: {$moneyRecord->balance} | 期望金额: {$exceptBalance} | 异常: {$diff} \n");
                }
            } elSE {
                $this->stdout("ID: {$moneyRecord->id} | created_at: {$moneyRecord->created_at} | type: {$moneyRecord->type} | 上次资金: {$lastBalance} | 变动金额: {$change} | 现有金额: {$moneyRecord->balance} | 期望金额: {$exceptBalance} | 异常: {$diff} \n");
            }

            $lastBalance = $moneyRecord->balance;
        }
    }
    
    /*
     * 同步用户历史邀请好友奖励
     *
     * 2017-06-26 日开发，用完可删除 php yii test/invite-award
     *
     * 正式环境 1880 条，查看时间 2017-06-21 13:17
     *
     * @param bool $run
     */
    public function actionInviteAward($run = false)
    {
        $smsData = SmsMessage::find()->where(['template_id' => '105818'])->all();
        $count = count($smsData);
        $this->stdout("待处理 {$count} 条数据 \n");
        $promo = RankingPromo::find()->where(['key' => 'promo_invite_12'])->one();
        if (is_null($promo)) {
            throw new \Exception('没有找到活动数据');
        }
        $errorCount = 0;
        foreach ($smsData as $sms) {
            /**
             * @var SmsMessage $sms
             */
            $user = User::findOne($sms->uid);
            $message = json_decode($sms->message, true);
            preg_match_all("/(\d+)元代金券/", $message[1], $match);
            $couponAmount = empty($match[1]) ? 0 : $match[1][0];
            preg_match_all("/(\d+(\.\d+)?)元现金红包/", $message[1], $match);
            $cashAmount = empty($match[1]) ? 0 : $match[1][0];
            if (empty($couponAmount) && empty($cashAmount)) {
                $this->stdout("用户 {$sms->uid} 获奖 {$message[1]} 数据异常 \n");
                $errorCount++;
            }

            if ($run) {
                if (!empty($couponAmount)) {
                    $award = Award::initNew($user, $promo);
                    $award->createTime = date('Y-m-d H:i:s', $sms->created_at);
                    $award->amount = $couponAmount;
                    $award->ref_type = Award::TYPE_COUPON;
                    $award->save(false);
                }

                if (!empty($cashAmount)) {
                    $award = Award::initNew($user, $promo);
                    $award->createTime = date('Y-m-d H:i:s', $sms->created_at);
                    $award->amount = $cashAmount;
                    $award->ref_type = Award::TYPE_CASH;
                    $award->save(false);
                }
            } else {
                $this->stdout("短信ID {$sms->id} 用户[{$user->id}] 由被邀请者{$message[0]} 获得： {$message[1]} \n");
            }
        }
        $this->stdout("失败数据 {$errorCount} \n");
    }

    /**
     * 临时代码：修复“重新计息”时候调整还款代码的bug，根据 online_repayment_plan 记录修复 repayment 记录
     * 开发时间：2017-07-20， 异常数据最早开始于 2017-06-28，脚本命令： php yii test/repayment
     */
    public function actionRepayment($run = false, $loanId = null)
    {
        $loanIds = [];
        if (empty($loanId)) {
            $loans = OnlineProduct::find()->where(['>', 'jixi_time', strtotime('2017-06-28')])->all();
            $loanIds = ArrayHelper::getColumn($loans, 'id');
        } else {
            $loan = OnlineProduct::findOne($loanId);
            if (!is_null($loan)) {
                $loans[] = $loan;
                $loanIds[] = $loan->id;
            }
        }
        $count = count($loanIds);
        if (empty($loanIds)) {
            $this->stdout("没有找到符合条件标的 \n");
            die;
        }
        $this->stdout("查到 $count 个标的 \n");
        $repayments = Repayment::find()->where(['in', 'loan_id', $loanIds])->all();
        $dirtyRepaymentCount = 0;
        $successCount = 0;
        /**
         * @var Repayment $repayment
         */
        foreach ($repayments as $repayment) {
            $plans = OnlineRepaymentPlan::find()
                ->where(['online_pid' => $repayment->loan_id])
                ->andWhere(['qishu' => $repayment->term])
                ->all();
            if (empty($plans)) {
                $this->stderr("没有找到 标的{$repayment->loan_id} 第 {$repayment->term} 期 的还款计划 \n", Console::FG_YELLOW);
                continue;
            }
            $amount = array_sum(ArrayHelper::getColumn($plans, 'benxi'));
            $principal = array_sum(ArrayHelper::getColumn($plans, 'benjin'));
            $interest = array_sum(ArrayHelper::getColumn($plans, 'lixi'));
            if (
                bccomp($amount, $repayment->amount, 2) !== 0
                || bccomp($principal, $repayment->principal, 2) !== 0
                || bccomp($interest, $repayment->interest, 2) !== 0
            ) {
                $dirtyRepaymentCount++;
                if ($run) {
                    $repayment->amount = $amount;
                    $repayment->principal = $principal;
                    $repayment->interest = $interest;
                    $res = $repayment->save(false);
                    if ($res) {
                        $successCount++;
                    }
                } else {
                    $this->stdout("标的{$repayment->loan_id} 第 {$repayment->term} 期数据异常, 根据 online_repayment_plan 得到 总本息、总本金、总利息分别为 {$amount} {$principal} {$interest}, 而 repayment 中本息、本金、利息 分别为 {$repayment->amount} {$repayment->principal} {$repayment->interest} \n");
                }
            }
        }

        $this->stdout("总共有 $dirtyRepaymentCount 条异常数据 \n");
        if ($run) {
            $this->stdout("总共成功修复 $successCount 条数据 \n");
        }
    }

    /**
     *  临时代码：修复还款数据，联动已经支付，但是温都未同步状态
     * php yii test/refund
     *
     * @param  int  $loanId     还款计划ID
     * @param  string   $date   实际支付日期
     */
    public function actionRefund($planId, $date = null)
    {
        /**
         * @var OnlineRepaymentPlan $plan
         * @var OnlineProduct $loan
         * @var OnlineOrder
         * @var OnlineRepaymentRecord $record
         */
        $plan = OnlineRepaymentPlan::findOne($planId);
        if (is_null($plan)) {
            throw new \Exception('没有找到还款计划');
        }

        if (in_array($plan->status, [OnlineRepaymentPlan::STATUS_YIHUAN, OnlineRepaymentPlan::STATUS_TIQIAM])) {
            throw new \Exception('温都状态已经是已还');
        }
        $loan = $plan->loan;
        if (empty($date)) {
            $record = OnlineRepaymentRecord::find()
                ->where(['online_pid' => $loan->id, 'qishu' => $plan->qishu])
                ->orderBy(['id' => SORT_DESC])
                ->one();
            if (is_null($record)) {
                throw new \Exception('未提供还款时间，未找到此标的同期还款时间');
            }
            $time = $record->refund_time;
        } else {
            $time = strtotime($date);
        }
        $today = date('Y-m-d');
        $days = $loan->getHoldingDays($today);

        $umpResp = Yii::$container->get('ump')->getTradeInfo($plan->sn, $time, '03');
        if ($umpResp->get('ret_code') !== '0000') {
            throw new \Exception('联动未成功支付, 联动支付状态妈:'.$umpResp->get('ret_code'));
        }

        if (!$loan->isAmortized()
            && $loan->is_jixi
            && $today >= date('Y-m-d', $loan->jixi_time)
            && $today < date('Y-m-d', $loan->finish_date)
        ) {
            $isRefreshCalcLiXi = true;
        } else {
            $isRefreshCalcLiXi = false;
        }
        if ($isRefreshCalcLiXi) {
            $plan->lixi = max(0.01, Bc::round(bcdiv(bcmul($days, $plan->lixi), $loan->expires), 2));//更新还款计划的利息
            $plan->benxi = bcadd($plan->benjin, $plan->lixi);//更新还款计划的本息
            $plan->refund_time = time();//更新还款计划的还款时间
        }

        $transaction = \Yii::$app->db->beginTransaction();

        $record = new OnlineRepaymentRecord();
        $record->online_pid = $plan->online_pid;
        $record->order_id = $plan->order_id;
        $record->order_sn = OnlineRepaymentRecord::createSN();
        $record->qishu = $plan->qishu;
        $record->uid = $plan->uid;
        $record->lixi = $plan->lixi;
        $record->benxi = $plan->benxi;
        $record->benjin = $plan->benjin;
        $record->benxi_yue = 0;
        if ($isRefreshCalcLiXi) {
            $record->status = OnlineRepaymentRecord::STATUS_BEFORE;
        } else {
            $record->status = OnlineRepaymentRecord::STATUS_DID;
        }

        $record->refund_time = time();

        if (!$record->save()) {
            $transaction->rollBack();

            throw new \Exception('还款失败，记录失败');
        }
        if ($isRefreshCalcLiXi) {
            $plan->status = OnlineRepaymentPlan::STATUS_TIQIAM;
        } else {
            $plan->status = OnlineRepaymentPlan::STATUS_YIHUAN;
        }
        $plan->actualRefundTime = date('Y-m-d H:i:s');//保存实际还款时间
        if (!$plan->save()) {
            $transaction->rollBack();

            throw new \Exception('还款失败，状态修改失败');
        }

        $ua = UserAccount::findOne(['uid' => $plan->uid, 'type' => UserAccount::TYPE_LEND]);

        //投资人账户调整
        $ua->available_balance = bcadd($ua->available_balance, $record->benxi, 2); //将投标的钱再加入到可用余额中
        $ua->drawable_balance = bcadd($ua->drawable_balance, $record->benxi, 2);
        $ua->in_sum = bcadd($ua->in_sum, $record->benxi, 2);
        $ua->investment_balance = bcsub($ua->investment_balance, $record->benjin, 2); //理财
        $ua->profit_balance = bcadd($ua->profit_balance, $record->lixi, 2); //收益

        if (!$ua->save()) {
            $transaction->rollBack();

            throw new \Exception('还款失败，投资人账户调整失败');
        }

        //增加资金记录
        $money_record = new MoneyRecord();
        $money_record->account_id = $ua->id;
        $money_record->sn = MoneyRecord::createSN();
        $money_record->type = null !== $plan->asset_id ? MoneyRecord::TYPE_CREDIT_HUIKUAN : MoneyRecord::TYPE_HUIKUAN;
        $money_record->osn = null !== $plan->asset_id ? $plan->asset_id : $plan->sn;
        $money_record->uid = $plan->uid;
        $money_record->in_money = $record->benxi;
        $money_record->balance = $ua->available_balance;
        $money_record->remark = '第'.$plan->qishu.'期'.'本金:'.$plan->benjin.'元;利息:'.$plan->lixi.'元;';

        if (!$money_record->save()) {
            $transaction->rollBack();

           throw new \Exception('还款失败，资金记录失败');
        }

        $transaction->commit();

    }
}
