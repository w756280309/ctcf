<?php

namespace console\controllers;

use common\lib\bchelp\BcRound;
use common\lib\user\UserStats;
use common\models\order\OnlineOrder;
use common\models\order\OnlineRepaymentPlan;
use common\models\order\OnlineRepaymentRecord;
use common\models\payment\Repayment;
use common\models\product\OnlineProduct;
use common\models\user\UserInfo;
use Ding\DingNotify;
use yii\console\Controller;
use Yii;

class DataController extends Controller
{
    //初始化用户投资信息（上线时候执行一次）
    public function actionInitUserinfo()
    {
        try {
            UserInfo::initUserInfo();
        } catch (\Exception $e) {
            $this->stdout($e->getMessage());
        }
    }

    //初始化标的的还款信息 repayment 表.为所有计息标的添加repayment ; 为所所有已经还清的标的的指定期数，更新repayment
    public function actionInitRepayment()
    {
        //初始化历史数据：所有确认计息的标的
        $orders = OnlineOrder::find()->where(['status' => OnlineOrder::STATUS_SUCCESS])->all();
        $repayment = [];
        bcscale(14);
        $bc = new BcRound();
        foreach ($orders as $ord) {
            $loan = $ord->loan;
            if (!$loan) {
                throw new \Exception();
            }
            //只初始化确认计息的标的
            if ($loan->is_jixi && $loan->jixi_time) {
                //获取每个订单的还款金额详情
                $res_money = OnlineRepaymentPlan::calcBenxi($ord);
                if ($res_money) {
                    foreach ($res_money as $k => $v) {
                        $term = $k + 1;
                        $amount = $bc->bcround(bcadd($v[1], $v[2]), 2);
                        $principal = $bc->bcround($v[1], 2);
                        $interest = $bc->bcround($v[2], 2);
                        //统计还款数据
                        $totalAmount = isset($repayment[$ord->online_pid][$term]['amount']) ? bcadd($repayment[$ord->online_pid][$term]['amount'], $amount) : $amount;
                        $totalPrincipal = isset($repayment[$ord->online_pid][$term]['principal']) ? bcadd($repayment[$ord->online_pid][$term]['principal'], $principal) : $principal;
                        $totalInterest = isset($repayment[$ord->online_pid][$term]['interest']) ? bcadd($repayment[$ord->online_pid][$term]['interest'], $interest) : $interest;
                        $repayment[$ord->online_pid][$term] = ['amount' => $totalAmount, 'principal' => $totalPrincipal, 'interest' => $totalInterest, 'dueDate' => $v[0]];
                    }
                }
            }
        }
        foreach ($repayment as $k => $v) {
            $loan = OnlineProduct::findOne($k);
            if ($loan) {
                foreach ($v as $key => $val) {
                    $rep = Repayment::findOne(['loan_id' => $k, 'term' => $key]);
                    //没有初始化过的才初始化
                    if (!$rep) {
                        $rep = new Repayment([
                            'loan_id' => $k,
                            'term' => $key,
                            'dueDate' => $val['dueDate'],
                            'amount' => $val['amount'],
                            'principal' => $val['principal'],
                            'interest' => $val['interest']
                        ]);
                        $rep->save();
                    }
                }
            }
        }

        //更新历史数据：所有已还、提前款款的还款计划
        $plan = OnlineRepaymentPlan::find()->where(['status' => [OnlineRepaymentPlan::STATUS_YIHUAN, OnlineRepaymentPlan::STATUS_TIQIAM]])->all();
        if ($plan) {
            foreach ($plan as $v) {
                $rep = Repayment::find()->where(['loan_id' => $v['online_pid'], 'term' => $v['qishu'], 'isRepaid' => 0])->one();
                if ($rep) {
                    $rep->isRepaid = 1;
                    $rep->isRefunded = 1;
                    $rep->repaidAt = date('Y-m-d H:i:s', $v['refund_time']);
                    $rep->refundedAt = date('Y-m-d H:i:s', $v['refund_time']);
                    $rep->update();
                }
            }
        }
    }

    /**
     * 更新还款计划的实际还款时间数据(上线时候执行一次)
     */
    public function actionUpdateActualRefundTime()
    {
        $records = OnlineRepaymentRecord::find()->select(['online_pid', 'order_id', 'qishu', 'uid', 'refund_time'])->where(['status' => [1, 2]])->asArray()->all();
        foreach ($records as $record) {
            $plan = OnlineRepaymentPlan::find()
                ->where('actualRefundTime is null')
                ->andWhere(['status' => [1, 2]])
                ->andWhere([
                    'online_pid' => $record['online_pid'],
                    'order_id' => $record['order_id'],
                    'qishu' => $record['qishu'],
                    'uid' => $record['uid']
                ])->one();
            if (!empty($plan)) {
                $plan->actualRefundTime = date('Y-m-d H:i:s' ,$record['refund_time']);
                $plan->save(false);
            }
        }
    }

    //导出投资用户信息
    public function actionExportLenderData()
    {
        $allData = UserStats::collectLenderData();
        $path  = Yii::getAlias('@backend').'/web/data/';
        $this->deleteUserDataFromPath($path);//删除历史数据

        $file = $path.'lender_data('.date('Y-m-d H:i:s').').csv';
        $fp = fopen($file, 'w');
        fputs($fp, "\xEF\xBB\xBF");//添加BOM头
        foreach ($allData as $value) {
            fputcsv($fp, $value);
        }
        fclose($fp);
        exit();
    }

    //删除用户数据相关的历史导出文件
    private function deleteUserDataFromPath($path)
    {
        if ( is_dir($path)) {
            $handle = opendir( $path );
            if ($handle) {
                while ( false !== ( $item = readdir( $handle ) ) ) {
                    if ( $item != "." && $item != ".." ) {
                        if (false !== strpos($item, 'lender_data')) {
                            unlink( "$path/$item" );
                        }
                    }
                }
            }
            closedir( $handle );
        }
    }

    /**
     * 更新温都金服钉钉用户
     */
    public function actionUpdateWdjfDingUser()
    {
        $file = Yii::$app->basePath . '/../data/wdjf_ding_users.json';
        file_put_contents($file, '');
        $ding = new DingNotify();
        $data = $ding->getAllUser();
        $string = "[" . PHP_EOL;
        foreach ($data as $value) {
            $string .= "\t" . '{"name":"' . $value['name'] . '","userId":"' . $value['userid'] . '"},' . PHP_EOL;
        }
        $string = rtrim($string, ',' . PHP_EOL);
        $string .= PHP_EOL;
        $string .= "]" . PHP_EOL;
        file_put_contents($file, $string, FILE_APPEND);
    }
}