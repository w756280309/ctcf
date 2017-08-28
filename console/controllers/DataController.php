<?php

namespace console\controllers;

use common\lib\user\UserStats;
use common\models\draw\DrawManager;
use common\models\order\OnlineOrder;
use common\models\order\OnlineFangkuan;
use common\models\product\Issuer;
use common\models\product\OnlineProduct;
use common\models\promo\Award;
use common\models\promo\FirstOrderPoints;
use common\models\promo\InviteRecord;
use common\models\promo\LoanOrderPoints;
use common\models\transfer\Transfer;
use common\models\user\MoneyRecord;
use common\models\user\User;
use common\models\user\UserAccount;
use common\service\AccountService;
use common\utils\StringUtils;
use common\utils\TxUtils;
use common\view\LoanHelper;
use wap\modules\promotion\models\RankingPromo;
use yii\console\Controller;
use Yii;

class DataController extends Controller
{
    //导出投资用户信息 data/export-lender-data
    public function actionExportLenderData()
    {
        $allData = UserStats::collectLenderData();
        $path = rtrim(Yii::$app->params['backend_tmp_share_path'], '/');
        $file = $path . '/all_investor_user.xlsx';
        if (file_exists($file)) {
            unlink($file);
        }
        $objPHPExcel = UserStats::initPhpExcelObject($allData);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        exit();
    }

    //立合旺通数据导出（一天时更新一次）data/export-issuer-record
    public function actionExportIssuerRecord($issuerId = 1)
    {
        $issuerId = intval($issuerId);
        $issuerData = Issuer::getIssuerRecords($issuerId);//获取立合旺通数据
        $path = rtrim(Yii::$app->params['backend_tmp_share_path'], '/');
        $file = $path . '/lihewangtong.xlsx';
        if (file_exists($file)) {
            unlink($file);
        }
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
        exit();
    }

    //导出每个用户投资每个标的|转让的数据:工具脚本 data/export-user161230
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

    /**
     * 补发首投积分和普通积分:修复工具
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

    /**
     * 修复标的工具:修复工具
     *
     * 联动成功,但是本地提现记录缺失.
     */
    public function actionDraw($pid = 1812)
    {
        $onlineProduct = OnlineProduct::findOne($pid);
        if (!$onlineProduct) {
            $this->stdout('标的信息不存在');

            return Controller::EXIT_CODE_ERROR;
        }

        $onlineFangkuan = OnlineFangkuan::findOne(['online_product_id' => $pid]);
        if (!$onlineFangkuan) {
            $this->stdout('放款记录不存在');

            return Controller::EXIT_CODE_ERROR;
        }

        if (!$this->allowDraw($onlineFangkuan)) {
            $this->stdout('当前放款状态不允许提现操作');

            return Controller::EXIT_CODE_ERROR;
        }

        $account = UserAccount::findOne(['uid' => $onlineFangkuan->uid, 'type' => UserAccount::TYPE_BORROW]);
        if (!$account) {
            $this->stdout('融资用户账户信息不存在');

            return Controller::EXIT_CODE_ERROR;
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            //融资方放款,不收取手续费
            $draw = DrawManager::initDraw($account, $onlineFangkuan->order_money);
            if (!$draw->save()) {
                throw new \Exception('提现申请失败', '000003');
            }

            $draw->orderSn = $onlineFangkuan->sn;
            if (!$draw->save()) {
                throw new \Exception('写入放款流水失败', '000003');
            }

            $onlineFangkuan->status = OnlineFangkuan::STATUS_TIXIAN_APPLY;
            if (!$onlineFangkuan->save()) {
                throw new \Exception('修改放款审核状态失败', '000003');
            }

            DrawManager::ackDraw($draw);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->stdout($e->getMessage());

            return Controller::EXIT_CODE_ERROR;
        }

        $this->stdout('放款成功!');
        return Controller::EXIT_CODE_NORMAL;
    }

    private function allowDraw(OnlineFangkuan $fangkuan)
    {
        return in_array($fangkuan->status, [
            OnlineFangkuan::STATUS_FANGKUAN,
            OnlineFangkuan::STATUS_TIXIAN_FAIL,
        ]);
    }

    //给邀请者补发投资红包:修复工具 php yii data/pay-cash
    public function actionPayCash($orderId)
    {
        $order = OnlineOrder::findOne($orderId);
        if (!is_null($order)) {
            $inviteRecord = InviteRecord::findOne(['invitee_id' => $order->uid]);
            if (!is_null($inviteRecord)) {
                $count = OnlineOrder::find()->where(['uid' => $inviteRecord->user_id, 'status' => 1])->count();
                $count = intval($count);
                if ($count > 0) {
                    $money = round($order->order_money / 1000, 1);
                    $this->stdout('为ID为 '.$inviteRecord->user_id.' 的用户补发红包 '. $money . ' 元'. PHP_EOL);
                    $moneyRecord = $this->sendUserCash($inviteRecord->user_id, $money);
                    if (is_bool($moneyRecord) && !$moneyRecord) {
                        $this->stdout($order->uid.'发送现金红包'.$money.'元失败！');
                    }
                    $promo = RankingPromo::find()
                        ->where(['key' => 'promo_invite_12'])
                        ->one();
                    if (null !== $promo && $moneyRecord instanceof MoneyRecord) {
                        $award = Award::cashAward($inviteRecord->user, $promo, $moneyRecord);
                        $award->save(false);
                        $this->stdout($inviteRecord->user_id.$money.'元现金红包成功！');
                    }
                }
            }
        }
    }

    //给用户发红包
    private function sendUserCash($userId, $money)
    {
        $user = User::findOne($userId);
        if (!is_null($user)) {
            $money = max(floatval($money), 0.01);
            return AccountService::userTransfer($user, $money);
        }
        return false;
    }

    /**
     * 为 sn 是 null 的 transfer 添加 sn
     * php yii data/add-sn
     *
     * 临时代码，时用完删除，2017-06-19
     */
    public function actionAddSn()
    {
        $transfers = Transfer::find()->where(['sn' => null, 'status' => Transfer::STATUS_INIT])->all();
        $count = count($transfers);
        $this->stdout("用{$count}条记录待处理 \n");
        $count=0;
        /**
         * @var Transfer $transfer
         */
        foreach ($transfers as $transfer) {
            $transfer->sn = TxUtils::generateSn('Tr');
            $transfer->save(false);
            $count++;
        }
        $this->stdout("成功处理{$count}条 \n");
    }
}
