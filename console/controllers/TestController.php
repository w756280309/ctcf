<?php
namespace console\controllers;

use common\models\user\MoneyRecord;
use yii\console\Controller;

/**
 * 临时脚本
 *
 * Class TestController
 * @package console\controllers
 */
class TestController extends Controller
{
    /**
     * [临时代码] 修复“ 南金交--宁富65号”系列标的还款数据
     *
     * 2017-06-15日开发，用完删除 test/repair-repayment
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
AND p.title LIKE  '%南金交--宁富65号%'
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
                    $this->stdout("成功, 剩余{$groupCount}条");
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
}
