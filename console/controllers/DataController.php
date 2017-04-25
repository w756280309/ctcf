<?php

namespace console\controllers;

use common\lib\bchelp\BcRound;
use common\lib\user\UserStats;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\draw\DrawManager;
use common\models\epay\EpayUser;
use common\models\mall\ThirdPartyConnect;
use common\models\order\BaoQuanQueue;
use common\models\order\OnlineOrder;
use common\models\order\OnlineFangkuan;
use common\models\order\OnlineRepaymentPlan;
use common\models\order\OnlineRepaymentRecord;
use common\models\payment\Repayment;
use common\models\product\Issuer;
use common\models\product\OnlineProduct;
use common\models\promo\FirstOrderPoints;
use common\models\promo\InviteRecord;
use common\models\promo\LoanOrderPoints;
use common\models\sms\SmsMessage;
use common\models\sms\SmsTable;
use common\models\user\DrawRecord;
use common\models\user\CoinsRecord;
use common\models\user\User;
use common\models\user\UserAccount;
use common\models\user\UserInfo;
use common\service\AccountService;
use common\utils\SecurityUtils;
use common\utils\StringUtils;
use common\utils\TxUtils;
use common\view\LoanHelper;
use Ding\DingNotify;
use wap\modules\promotion\models\RankingPromo;
use yii\console\Controller;
use Yii;
use yii\helpers\ArrayHelper;

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

    /**
     * 将转账方资金转给收款人并为收款人提现
     * 使用说明：
     * 1. php yii data/transfer 查看转账方、温都账户、收款方金额, 不尽兴任何实际操作
     * 2. 转一笔0.01元 php yii data/transfer 1
     * 3. 转指定金额 php yii data/transfer 1 $amount
     * 4. 可以使用 $from 和 $to 参数控制转账方和收款方, 默认转账方是南京交收款方是居莫愁
     * 5. 可以使用 $fromTransfer 参数控制时候进行 转账方转账给温都
     * 6. 可以使用 $toTransfer 参数控制是否进行温都转账给收款方
     * 7. 可以使用 $draw 参数控制是否发起收款方提现
     *
     * @param  bool     $run            是否转账
     * @param  float    $amount         待转金额
     * @param  string   $from           转账方
     * @param  string   $to             收款方
     * @param  bool     $fromTransfer   是否进行转账方转账给温都账户
     * @param  bool     $toTransfer     是否进行温都账户转账给收款方
     * @param  bool     $draw           是否进行收款方提现
     */
    public function actionTransfer($run = false, $amount = 0.01, $from = 'njj', $to = 'jmc', $fromTransfer = true, $toTransfer = true, $draw = true)
    {
        $amount = max(floatval($amount), 0);
        $isTest = false;//测试环境


        if ($isTest) {
            $ePayUserIdList = [
                'njj' => '7601209',//测试环境只有一个账号
                'lhwt' => '7601209',//测试环境只有一个账号
                'jmc' => '7601209',//测试环境只有一个账号
            ];
        } else {
            $ePayUserIdList = [
                'njj' => '7302209',//正式环境（南京交）在联动ID
                'lhwt' => '7301209',//正式环境（立合旺通）在联动ID  转账流程已测试 正式转账已成功
                'jmc' => '7303209',//正式环境（居莫愁）在联动ID
            ];
        }


        $fromEPayUser = EpayUser::findOne(['epayUserId' => $ePayUserIdList[$from]]);//转账方联动账户
        $toEPayUser = EpayUser::findOne(['epayUserId' => $ePayUserIdList[$to]]);//收款方联动账户
        if (is_null($fromEPayUser) || is_null($toEPayUser)) {
            throw new \Exception('没有找到转账企业');
        }
        $toUserAccount = UserAccount::findOne(['uid' => $toEPayUser->appUserId]);


        $platformUserId = Yii::$app->params['ump']['merchant_id'];//平台在联动账户
        $ump = Yii::$container->get('ump');
        //平台信息
        $ret = $ump->getMerchantInfo($platformUserId);
        $this->stdout('平台(温都)账户余额：' . $ret->get('balance') . PHP_EOL);
        //转账方信息
        $ret = $ump->getMerchantInfo($fromEPayUser->epayUserId);
        $this->stdout('转账方账户余额：' . $ret->get('balance') . PHP_EOL);
        //收款方信息
        $ret = $ump->getMerchantInfo($toEPayUser->epayUserId);
        $this->stdout('收款方账户余额：' . $ret->get('balance') . PHP_EOL);


        if ($run) {
            if ($fromTransfer) {
                //转账方 转账到 温都账户
                $this->stdout('正在进行 转账方 转账到 温都账户' . PHP_EOL);
                $time = time();
                $sn = TxUtils::generateSn('TR');
                $ret = $ump->platformTransfer($sn, $fromEPayUser->epayUserId, $amount, $time);
                if ($ret->isSuccessful()) {
                    //更改温都数据库
                    $sql = "update user_account set available_balance = available_balance - :amount where uid = ( select appUserId from EpayUser where epayUserId = :epayUserId )";
                    $res = Yii::$app->db->createCommand($sql, [
                        'amount' => $amount,
                        'epayUserId' => $fromEPayUser->epayUserId,
                    ])->execute();
                    $this->stdout('温都数据库转账方数据更新：' . ($res ? '成功' : '失败') . PHP_EOL);

                    //转账方联动信息
                    $ret = $ump->getMerchantInfo($fromEPayUser->epayUserId);
                    $this->stdout('转账方账户余额：' . $ret->get('balance') . PHP_EOL);
                    //温都联动信息
                    $ret = $ump->getMerchantInfo($platformUserId);
                    $this->stdout('温都账户余额：' . $ret->get('balance') . PHP_EOL);


                    $this->stdout('转账方 转账成功' . PHP_EOL);

                } else {
                    $this->stdout('转账方 转账失败，联动返回信息：' . $ret->get('ret_msg') . PHP_EOL);
                }
            }
            if ($toTransfer) {
                //温都账户 转账到 收款方账户
                $this->stdout('正在进行 温都账户 转账到 收款方账户' . PHP_EOL);
                $time = time();
                $sn = TxUtils::generateSn('TR');
                $ret = $ump->orgTransfer($sn, $toEPayUser->epayUserId, $amount, $time);
                if ($ret->isSuccessful()) {
                    //平台信息
                    $ret = $ump->getMerchantInfo($platformUserId);
                    $this->stdout('平台账户余额：' . $ret->get('balance') . PHP_EOL);
                    //收款方信息
                    $ret = $ump->getMerchantInfo($toEPayUser->epayUserId);
                    $this->stdout('收款方账户余额：' . $ret->get('balance') . PHP_EOL);

                    $this->stdout('收款方 转账成功' . PHP_EOL);

                } else {
                    $this->stdout('收款方 转账失败，联动返回信息：' . $ret->get('ret_msg') . PHP_EOL);
                }
            }
            if ($draw) {
                //收款方提现
                $this->stdout('正在进行 收款方提现' . PHP_EOL);
                $sn = TxUtils::generateSn('DRAW');
                $time = time();

                //插入提现记录
                $draw = DrawManager::initNew($toUserAccount, $amount, Yii::$app->params['drawFee']);
                $draw->status = DrawRecord::STATUS_SUCCESS;
                $res = $draw->save();
                if ($res) {
                    $this->stdout('插入收款方提现记录' . PHP_EOL);
                    $ret = $ump->orgDraw($sn, $toEPayUser->epayUserId, $amount, $time);
                    if ($ret->isSuccessful()) {
                        var_dump($ret->toArray());

                        //收款方账户信息
                        $ret = $ump->getMerchantInfo($toEPayUser->epayUserId);
                        $this->stdout('商户账户余额：' . $ret->get('balance') . PHP_EOL);
                        $this->stdout('收款方 提现成功' . PHP_EOL);
                    } else {
                        $this->stdout('收款方提现失败，联动返回信息：' . $ret->get('ret_msg') . PHP_EOL);
                        $draw->status = DrawRecord::STATUS_FAIL;
                        $draw->save();
                        $this->stdout('将收款方提现记录改为失败' . PHP_EOL);
                    }
                }

            }

        }
    }

    /**
     * 修复标的"南金交--宁富20号中科建三期2号"的本地提现记录.
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

    //初始化温都金服用户信息加密key php yii data/init-key
    public function actionInitKey()
    {
        $randomKey = Yii::$app->security->generateRandomString(255);
        $file = Yii::$app->params['wdjf_security_key'];
        if (file_exists($file)) {
            $this->stdout('加密文件已经存在');
        } else {
            file_put_contents($file, $randomKey);
        }
    }

    //初始化用户加密手机号 php yii data/update-safe-mobile
    public function actionUpdateSafeMobile()
    {
        //更新user表
        $users = User::find()->where(['safeMobile' => null])->all();
        foreach ($users as $user) {
            $user->safeMobile = SecurityUtils::encrypt($user->mobile);
            $user->save(false);
        }
        //更新sms表
        $sms = SmsTable::find()->where(['safeMobile' => null])->all();
        foreach ($sms as $val) {
            $val->safeMobile = SecurityUtils::encrypt($val->mobile);
            $val->save(false);
        }

        //更新sms_message表
        $message = SmsMessage::find()->where(['safeMobile' => null])->all();
        foreach ($message as $val) {
            $val->safeMobile = SecurityUtils::encrypt($val->mobile);
            $val->save(false);
        }
    }

    /**
     * 历史数据-初始化身份证号及生日字段（目前系统的用户皆为18位身份证号）
     */
    public function actionUpdateIdentity()
    {
        //更新user表 加密的身份证号/生日字段
        $users = User::find()
            ->where(['safeIdCard' => null])
            ->andWhere(['is not', 'idcard', null])
            ->all();
        foreach ($users as $user) {
            $user->safeIdCard = SecurityUtils::encrypt(trim($user->idcard));
            $user->birthdate = date('Y-m-d', strtotime(substr($user->idcard, 6, 8)));
            $user->save(false);
        }
    }

    /**
     * 给投资次数2次及以上或累计投资金额5万及以上，且未使用APP投资的用户发放APP投资红包.
     *
     * 1. 该console只运行一次;
     * 2. 针对当前所有注册客户;
     */
    public function actionAppCoupon()
    {
        $couponType = CouponType::findOne(['sn' => '0023:10000-10']);

        if (null === $couponType) {
            echo 'APP投资红包信息不存在';

            return self::EXIT_CODE_ERROR;
        }

        $sql = <<<COUPON
            SELECT uid
            FROM (
            SELECT COUNT( id ) AS ordertimes, SUM( order_money ) AS totalmoney, uid
            FROM online_order
            WHERE uid NOT
            IN (
            SELECT DISTINCT uid
            FROM online_order
            WHERE investFrom =3
            AND STATUS =1
            )
            AND STATUS =1
            GROUP BY uid
            )t
            WHERE t.ordertimes >=2
            OR t.totalmoney >=50000;
COUPON;

        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $uids = ArrayHelper::getColumn($data, 'uid');
        $count = 0;

        foreach ($uids as $uid) {
            $user = User::findOne($uid);

            if ($user) {
                try {
                    $userCoupon = UserCoupon::addUserCoupon($user, $couponType);

                    if ($userCoupon->save()) {
                        ++$count;
                    }
                } catch (\Exception $e) {
                    echo $user->mobile.'发放失败, 原因: '.$e->getMessage();
                }
            }
        }

        echo '总共发放了APP投资红包'.$count.'张';

        return self::EXIT_CODE_NORMAL;
    }

    //给邀请者补发投资红包 php yii data/pay-cash
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
                    echo '为ID为 '.$inviteRecord->user_id.' 的用户补发红包 '. $money . ' 元'. PHP_EOL;
                    $res = $this->sendUserCash($inviteRecord->user_id, $money);
                    var_dump($res);
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
     * 给投资2次及以上超过一个月（包括30天及以上）未投资的用户发放周末红包.
     *
     * 1. 该console只运行一次;
     */
    public function actionWeekCoupon($couponId = 68, $orderNum = 2, $days = 30)
    {
        $couponType = CouponType::findOne($couponId);

        if (null === $couponType) {
            echo "周末红包信息不存在\n";

            return self::EXIT_CODE_ERROR;
        }

        $u = User::tableName();
        $ui = UserInfo::tableName();

        $users = User::find()
            ->innerJoin($ui, "$u.id = $ui.user_id")
            ->where(['>=', 'investCount', $orderNum])
            ->andWhere(['<', 'lastInvestDate', date('Y-m-d', strtotime('today - '.$days.' days'))])
            ->all();

        $count = 0;

        foreach ($users as $user) {
            try {
                $userCoupon = UserCoupon::addUserCoupon($user, $couponType);

                if ($userCoupon->save()) {
                    ++$count;
                }
            } catch (\Exception $e) {
                echo $user->mobile.'发放失败, 原因: '.$e->getMessage();
            }
        }

        echo "总共发放了周末红包".$count."张\n";

        return self::EXIT_CODE_NORMAL;
    }

    /**
     * 给注册50天以上，未投资的用户发放新人专享红包.
     *
     * 1. 该console只运行一次;
     */
    public function actionCouponForNew($couponIds = [73, 74], $days = 50)
    {
        $couponTypes = CouponType::findAll(['id' => $couponIds]);

        if (count($couponTypes) !== count($couponIds)) {
            echo "包含未知的红包信息\n";

            return self::EXIT_CODE_ERROR;
        }

        $u = User::tableName();
        $ui = UserInfo::tableName();

        $users = User::find()
            ->innerJoin($ui, "$u.id = $ui.user_id")
            ->where([
                "$ui.investCount" => 0,
                "$u.status" => User::STATUS_ACTIVE,
                "$u.type" => User::USER_TYPE_PERSONAL,
            ])
            ->andWhere(["<", "$u.created_at", strtotime("today - ".$days." days")])
            ->all();

        $count = 0;

        foreach ($users as $user) {
            foreach ($couponTypes as $couponType) {
                try {
                    $userCoupon = UserCoupon::addUserCoupon($user, $couponType);

                    if ($userCoupon->save(false)) {
                        ++$count;
                    }
                } catch (\Exception $e) {
                    echo $user->mobile."发放失败, 原因: ".$e->getMessage()."\n";
                }
            }
        }

        echo "总共给用户发放了".$count."张代金券\n";

        return self::EXIT_CODE_NORMAL;
    }

    /**
     * 1. 给截至2017-04-19仅投资一次, 且投资金额>=10000元的用户发放20元投资奖励红包;
     * 2. 给截至2017-04-19仅投资一次, 且投资金额<10000元的用户发放5元投资奖励红包;
     * 3. 给截至2017-04-19投资两次, 且投资金额>=10000元的用户发放20元投资奖励红包;
     * 4. 给截至2017-04-19投资两次, 且投资金额<10000元的用户发放5元投资奖励红包;
     *
     * PS: 该console只运行一次;
     */
    public function actionCouponForUser()
    {
        $couponTypes = CouponType::find()
            ->where(['id' => [76, 75]])
            ->indexBy('id')
            ->all();

        if (2 !== count($couponTypes)) {
            echo "包含未知的红包信息\n";

            return self::EXIT_CODE_ERROR;
        }

        $users = User::find()
            ->joinWith('info')
            ->andWhere(['between', 'investCount', 1, 2])
            ->andWhere(['<=', 'lastInvestDate', '2017-04-19'])
            ->all();

        $couponType20 = 0;
        $couponType05 = 0;

        foreach ($users as $user) {
            try {
                if (bccomp($user->info->investTotal, 10000, 2) >= 0) {
                    $userCoupon = UserCoupon::addUserCoupon($user, $couponTypes[76]);

                    if ($userCoupon->save(false)) {
                        ++$couponType20;
                    }
                } else {
                    $userCoupon = UserCoupon::addUserCoupon($user, $couponTypes[75]);

                    if ($userCoupon->save(false)) {
                        ++$couponType05;
                    }
                }
            } catch (\Exception $e) {
                echo $user->mobile."发放失败, 原因: ".$e->getMessage()."\n";
            }
        }

        echo "总共发放了20元投资奖励红包".$couponType20."张\n";
        echo "总共发放了5元投资奖励红包".$couponType05."张\n";

        return self::EXIT_CODE_NORMAL;
    }

    /**
     * 标的重复计息，财富值重复
     *
     *
    SELECT *
    FROM  `coins_record`
    WHERE user_id
    IN (

    SELECT DISTINCT uid
    FROM online_order
    WHERE online_pid =2267
    )
    AND DATE_FORMAT( createTime,  '%Y-%m-%d' ) =  '2017-03-31'
    ORDER BY  `coins_record`.`order_id` ASC
     */
    public function actionUpdateCoins()
    {
        $ids = [9256, 9257, 9258, 9259, 9260, 9261, 9262, 9263, 9264, 9265];
        $user_ids = [11521, 783, 3018, 10422, 9948, 1785, 10728];

        $coinsRecords = CoinsRecord::find()->where(['in', 'id', $ids])->orderBy(['id' => SORT_ASC])->all();
        if (count($coinsRecords) == 10) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($coinsRecords as $coinsRecord) {
                    $data = [
                        'coinsRecord_id' => $coinsRecord->id,
                        'user_id' => $coinsRecord->user_id,
                        'coins' => $coinsRecord->incrCoins,
                    ];
                    echo 'coinsRecord_id: ' . $data['coinsRecord_id'] . ' | ' . 'user_id: ' . $data['user_id'] . ' | ' . 'coins: ' . $data['coins'] . PHP_EOL;
                    if (!in_array($data['user_id'], $user_ids)) {
                        throw new \Exception($coinsRecord->id . '的用户不在修复名单中');
                    }

                    $sql = "delete from coins_record where id = :coinsRecord_id and user_id = :user_id and isOffline = 0";
                    $res = Yii::$app->db->createCommand($sql, [
                        'coinsRecord_id' => $data['coinsRecord_id'],
                        'user_id' => $data['user_id'],
                    ])->execute();
                    if (!$res) {
                        throw new \Exception($coinsRecord->id . '删除旧数据失败');
                    }

                    $sql = "update `coins_record` set `finalCoins` = `finalCoins` - :coins WHERE id > :coinsRecord_id and user_id = :user_id  and isOffline = 0;";
                    Yii::$app->db->createCommand($sql, [
                        'coins' => $data['coins'],
                        'coinsRecord_id' => $data['coinsRecord_id'],
                        'user_id' => $data['user_id'],
                    ])->execute();

                    $sql = "update user set annualInvestment = annualInvestment - :coins  where id = :user_id";
                    $res = Yii::$app->db->createCommand($sql, [
                        'coins' => $data['coins'],
                        'user_id' => $data['user_id'],
                    ])->execute();
                    if (!$res) {
                        throw new \Exception($coinsRecord->id . '更新用户累计年华投资金额失败');
                    }
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                echo $e->getMessage() . PHP_EOL;
            }
        }
    }

    public function actionMallLogin()
    {
        $user = User::findOne(['mobile' => '18310722679']);
        $thirdPartyConnect = ThirdPartyConnect::findOne(['user_id' => $user->id]);
        if (is_null($thirdPartyConnect)) {
            $thirdPartyConnect = ThirdPartyConnect::initNew($user);
            $thirdPartyConnect->save();
        }

        $url = ThirdPartyConnect::buildCreditAutoLoginRequest(
            \Yii::$app->params['mall_settings']['app_key'],
            \Yii::$app->params['mall_settings']['app_secret'],
            empty($thirdPartyConnect) ? 'not_login' : $thirdPartyConnect->publicId,
            is_null($user) ? 0 : $user->points,
            urldecode('')
        );
        echo $url . PHP_EOL;
    }

    /**
     * 更新未计息标的的保全队列
     * php yii data/baoquan 1
     */
    public function actionBaoquan($run = false)
    {
        $orderTable = OnlineOrder::tableName();
        $loanTable = OnlineProduct::tableName();
        $orders = OnlineOrder::find()
            ->innerJoin(OnlineProduct::tableName(), "$orderTable.online_pid = $loanTable.id")
            ->where(['in', "$loanTable.status", [OnlineProduct::STATUS_NOW, OnlineProduct::STATUS_FULL, OnlineProduct::STATUS_FOUND]])
            ->andWhere(["$orderTable.status" => OnlineOrder::STATUS_SUCCESS])
            ->all();
        $this->stdout('总共找到　'. count($orders) . ' 条未计息的成功订单' . PHP_EOL);
        $successCount = $errorCount = 0;
        if (!$run) {
            exit(1);
        }
        foreach ($orders as $order) {
            //投标之后添加保全
            $job = new BaoQuanQueue(['itemId' => $order->id, 'status' => BaoQuanQueue::STATUS_SUSPEND, 'itemType' => BaoQuanQueue::TYPE_LOAN_ORDER]);
            if ($job->save()) {
                $this->stdout('订单'. $order->id.'　添加保全队列'.PHP_EOL);
                $successCount ++;
            } else {
                $this->stdout('订单'. $order->id.'　失败'.PHP_EOL);
                $errorCount ++;
            }
        }
        $this->stdout("成功数 {$successCount}, 失败数 {$errorCount}".PHP_EOL);
    }

}
