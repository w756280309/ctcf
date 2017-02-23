<?php

namespace console\controllers;

use common\lib\bchelp\BcRound;
use common\lib\user\UserStats;
use common\models\order\OnlineOrder;
use common\models\order\OnlineRepaymentPlan;
use common\models\order\OnlineRepaymentRecord;
use common\models\payment\Repayment;
use common\models\product\Issuer;
use common\models\product\OnlineProduct;
use common\models\promo\FirstOrderPoints;
use common\models\promo\LoanOrderPoints;
use common\models\user\User;
use common\models\user\UserInfo;
use common\utils\StringUtils;
use common\utils\TxUtils;
use common\view\LoanHelper;
use Ding\DingNotify;
use wap\modules\promotion\models\RankingPromo;
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

    //导出投资用户信息 data/export-lender-data
    public function actionExportLenderData()
    {
        $allData = UserStats::collectLenderData();
        $path  = Yii::getAlias('@backend').'/web/data/';

        $file = $path.'投资用户信息('.date('Y-m-d H:i:s').').xlsx';

        $objPHPExcel = UserStats::initPhpExcelObject($allData);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);

        $this->deleteUserDataFromPath($path, '投资用户信息', $file);//删除历史数据
        exit();
    }

    //删除包含指定指定文件名的文件,保留指定文件
    private function deleteUserDataFromPath($path, $fileNamePart, $file)
    {
        if (!file_exists($path)) {
            mkdir($path);
        }
        if ( is_dir($path)) {
            $handle = opendir( $path );
            if ($handle) {
                while ( false !== ( $item = readdir( $handle ) ) ) {
                    if ( $item != "." && $item != ".." ) {
                        $newFIle = rtrim($path, "/") . "/$item";
                        if (false !== strpos($item, $fileNamePart) && $newFIle !== $file) {
                            unlink($newFIle);
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

    //双十二抽奖活动，给用户发红包
    public function actionPromoAddUserCash($id)
    {
        $user = User::findOne($id);
        if (!empty($user)) {
            try {
                $promo = RankingPromo::findOne(['key' => 'promo_12_12_21']);
                if (class_exists($promo->promoClass)) {
                    $model = new $promo->promoClass($promo);
                    if (method_exists($model, 'doAfterSuccessLoanOrder')) {
                        return $model->doAfterSuccessLoanOrder($user);
                    }
                }
            } catch (\Exception $ex) {
                echo $ex->getMessage();
            }
        }
    }

    //立合旺通数据导出（一天时更新一次）data/export-issuer-record
    public function actionExportIssuerRecord($issuerId = 1)
    {
        $issuerId = intval($issuerId);
        $issuerData = Issuer::getIssuerRecords($issuerId);//获取立合旺通数据
        $path = Yii::getAlias('@backend') . '/web/data/';
        $file = $path . '立合旺通-' . date('YmdHis') . '.xlsx';
        $exportData[] = ['期数', '融资方', '发行方', '项目名称', '项目编号', '项目状态', '备案金额（元）', '募集金额（元）', '实际募集金额（元）', '年化收益率（%）', '开始融资时间', '满标时间', '起息日', '还款本金', '还款利息', '预计还款时间', '实际还款时间'];
        $records = $issuerData['model'];
        $issuer = $issuerData['issuer'];
        $repaymentPlan = $issuerData['plan'];
        $refundTime = $issuerData['refundTime'];
        foreach ($records as $key => $loan) {
            if (isset($repaymentPlan[$key])) {
                foreach ($repaymentPlan[$key] as $repayment) {
                    $exportData[] = [
                        $repayment['qishu'],
                        $loan->borrower->org_name,
                        $issuer->name,
                        $loan->title,
                        $loan->issuerSn,
                        \Yii::$app->params['deal_status'][$loan->status],
                        floatval($loan->filingAmount),
                        floatval($loan->money),
                        floatval($loan->funded_money),
                        LoanHelper::getDealRate($loan) . ($loan->jiaxi ? '+' . StringUtils::amountFormat2($loan->jiaxi) : ''),
                        empty($loan->start_date) ? '---' : date('Y-m-d', $loan->start_date),
                        empty($loan->full_time) ? '---' : date('Y-m-d', $loan->full_time),
                        empty($loan->jixi_time) ? '---' : date('Y-m-d', $loan->jixi_time),
                        floatval($repayment['totalBenjin']),
                        floatval($repayment['totalLixi']),
                        date('Y-m-d', $repayment['refund_time']),
                        isset($refundTime[$key][$repayment['qishu']]) ? date('Y-m-d', $refundTime[$key][$repayment['qishu']]) : '---'
                    ];
                }
            } else {
                $exportData[] = [
                    '---',
                    $loan->borrower->org_name,
                    $issuer->name,
                    $loan->title,
                    $loan->issuerSn,
                    \Yii::$app->params['deal_status'][$loan->status],
                    floatval($loan->filingAmount),
                    floatval($loan->money),
                    floatval($loan->funded_money),
                    LoanHelper::getDealRate($loan) . ($loan->jiaxi ? '+' . StringUtils::amountFormat2($loan->jiaxi) : ''),
                    empty($loan->start_date) ? '---' : date('Y-m-d', $loan->start_date),
                    empty($loan->full_time) ? '---' : date('Y-m-d', $loan->full_time),
                    empty($loan->jixi_time) ? '---' : date('Y-m-d', $loan->jixi_time),
                    '---',
                    '---',
                    '---',
                    '---'
                ];
            }
        }

        $objPHPExcel = UserStats::initPhpExcelObject($exportData);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);

        $this->deleteUserDataFromPath($path, '立合旺通', $file);//删除历史数据
        exit();
    }

    //导出每个用户投资每个标的|转让的数据 data/export-user161230
    public function actionExportUser161230()
    {
        $sql = "SELECT u.id AS user_id, u.real_name AS name, u.mobile AS mobile, u.idcard AS idcard, p.id AS loan_id, p.title title, FROM_UNIXTIME( p.finish_date ) AS finish_date, SUM( rp.benjin ) AS benjin, SUM( rp.lixi ) AS all_lixi, MAX( o.yield_rate ) AS rate
FROM online_repayment_plan AS rp
INNER JOIN user AS u ON rp.uid = u.id
INNER JOIN online_product AS p ON rp.online_pid = p.id
INNER JOIN online_order AS o ON rp.order_id = o.id
WHERE u.type =1
AND o.status =1
GROUP BY u.id, rp.online_pid
ORDER BY u.real_name ASC , rp.online_pid ASC ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($data as $item) {
            $result[$item['user_id']][$item['loan_id']] = [
                'name' => $item['name'],
                'mobile' => strval($item['mobile']) . "\t",
                'idcard' => strval($item['idcard']) . "\t",
                'title' => $item['title'],
                'finish_date' => $item['finish_date'],
                'rate' => floatval($item['rate']) * 100,
                'benjin' => floatval($item['benjin']),
                'all_lixi' => floatval($item['all_lixi']),
            ];
        }

        //已还
        $sql = "SELECT rp.uid AS user_id, rp.online_pid AS loan_id, SUM( rp.lixi ) AS y_lixi
FROM online_repayment_plan AS rp
WHERE rp.status
IN ( 1, 2 ) 
GROUP BY rp.uid, rp.online_Pid";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($data as $item) {
            if (isset($result[$item['user_id']][$item['loan_id']])) {
                $result[$item['user_id']][$item['loan_id']]['y_lixi'] = floatval($item['y_lixi']);
            }
        }
        //未还
        $sql = "SELECT rp.uid AS user_id, rp.online_pid AS loan_id, SUM( rp.lixi ) AS n_lixi
FROM online_repayment_plan AS rp
WHERE rp.status = 0 
GROUP BY rp.uid, rp.online_Pid";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($data as $item) {
            if (isset($result[$item['user_id']][$item['loan_id']])) {
                $result[$item['user_id']][$item['loan_id']]['n_lixi'] = floatval($item['n_lixi']);
            }
        }
        $file = 'user_loan_data' . date('YmdHis') . '.csv';
        header('Content-Disposition: attachment; filename="' . $file . '"');
        $out = fopen($file, 'w');

        fputs($out, "\xEF\xBB\xBF");//添加BOM头
        fputcsv($out, ['姓名', '手机号', '身份证号', '标的标题', '截止日期', '利率(%)', '投资金额', '总利息', '已还利息', '未还利息']);
        if (!empty($result)) {
            foreach ($result as $user_id => $userData) {
                if (!empty($userData) and  is_array($userData)) {
                    foreach ($userData as $loan_id => $loanData) {
                        if (!empty($loanData) && is_array($loanData)) {
                            fputcsv($out, $loanData);
                        }
                    }
                }
            }
        }

        fclose($out);
        exit();
    }

    //为2017-02-17日投资没有给积分用户补发首投积分 data/send-points
    public function actionSendPoints($run = false)
    {
        $promo = RankingPromo::find()->where(['key' => 'first_order_point'])->one();
        if (is_null($promo)) {
            return false;
        }
        $model = new $promo->promoClass($promo);
        $orders = OnlineOrder::find()->where(['status' => OnlineOrder::STATUS_SUCCESS])->andWhere(['between', 'created_at', strtotime('2017-02-17'), time()])->all();
        foreach ($orders as $order) {
            if ($model->canSendPoint($order)) {
                if ($run) {
                    $model->addUserPoints($order);
                } else {
                    echo $order->uid . ';';
                }
            }
        }
        return true;
    }

    /**
     * 补发首投积分和普通积分
     * @param $order_id
     */
    public function actionAddPoints($order_id) {
        $order = OnlineOrder::findOne($order_id);
        if (is_null($order)) {
            return;
        }
        //首投送积分活动
        $promo1 = RankingPromo::find()->where(['key' => 'first_order_point'])->one();
        if (!is_null($promo1)) {
            $model1 = new FirstOrderPoints($promo1);
            if ($model1->canSendPoint($order)) {
                $model1->addUserPoints($order);
            }
        }
        $promo2 = RankingPromo::find()->where(['key' => 'loan_order_points'])->one();
        if (!is_null($promo2)) {
            $model2 = new LoanOrderPoints($promo2);
            if ($model2->canSendPoint($order)) {
                $model2->addUserPointsWithLoanOrder($order);
            }
        }
    }

    //平台账号向企业账号转账
    public function actionTransfer($run = false)
    {
        //$epayUserId = '7601209';//测试环境融资方ID
        $epayUserId = '7301209';//立合旺通 在联动正式环境账号
        $ump = Yii::$container->get('ump');
        //商户信息
        $ret = $ump->getMerchantInfo($epayUserId);
        $banance1 = $ret->get('balance');
        echo '转账前账户余额：'.$banance1.PHP_EOL;

        if ($run) {
            $amount = 0.01;//默认转让0.01元
            $time = time();
            $sn = TxUtils::generateSn('TR');
            $ret = $ump->orgTransfer($sn, $epayUserId, $amount, $time);
            var_dump($ret);
        }

        //商户信息
        $ret = $ump->getMerchantInfo($epayUserId);
        $balance2 = $ret->get('balance');
        echo '账户余额：'.$balance2.PHP_EOL;

        echo PHP_EOL . '变动金额:' . (bcsub($banance1 , $balance2)) . '分' . PHP_EOL;

    }
}