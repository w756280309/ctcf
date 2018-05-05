<?php

namespace console\controllers;

use common\models\draw\DrawManager;
use common\models\epay\EpayUser;
use common\models\mall\ThirdPartyConnect;
use common\models\message\RepaymentMessage;
use common\models\order\OnlineFangkuan;
use common\models\order\OnlineOrder;
use common\models\order\OnlineRepaymentPlan;
use common\models\order\OnlineRepaymentRecord;
use common\models\payment\PaymentLog;
use common\models\payment\Repayment;
use common\models\product\OnlineProduct;
use common\models\tx\CreditOrder;
use common\models\user\RechargeRecord;
use common\models\user\DrawRecord;
use common\models\user\MoneyRecord;
use common\models\user\User;
use common\models\user\UserAccount;
use common\service\AccountService;
use common\service\LoanService;
use common\service\SmsService;
use common\utils\SecurityUtils;
use common\utils\TxUtils;
use Ding\DingNotify;
use EasyWeChat\Core\Exception;
use Lhjx\Noty\Noty;
use Yii;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * 工具脚本
 *
 * Class ToolController
 * @package console\controllers
 */
class ToolController extends Controller
{
    /**
     * 更新钉钉用户:工具脚本
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


    /**
     * 将转账方资金转给收款人并为收款人提现:工具脚本
     * 使用说明：
     * 1. php yii tool/transfer 查看转账方、温都账户、收款方金额, 不尽兴任何实际操作
     * 2. 转一笔0.01元 php yii tool/transfer 1
     * 3. 转指定金额 php yii tool/transfer 1 $amount
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
                'hzyx' => '7601209',//测试环境只有一个账号
            ];
        } else {
            $ePayUserIdList = [
                'njj' => '7302209',//正式环境（南京交）在联动ID
                'lhwt' => '7301209',//正式环境（立合旺通）在联动ID  转账流程已测试 正式转账已成功
                'jmc' => '7303209',//正式环境（居莫愁）在联动ID
                'hzyx' => '7305209', // 杭州越翔
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
                    $sql = "update user_account set available_balance = available_balance - :amount where uid = ( select appUserId from epayuser where epayUserId = :epayUserId )";
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
                    $ret = $ump->orgDraw($draw->getTxSn(), $toEPayUser->epayUserId, $amount, $time);
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

    //初始化用户信息加密key:工具脚本 php yii tool/init-key
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

    //生成兑吧免登url:工具脚本
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
     * 工具脚本：为标的重新计息 php yii tool/regenerate-repayment
     * 注：暂时不支持已经还过款的标的、暂时不支持已经转让的标的
     *
     * @param $loanId
     * @param bool $run
     */
    public function actionRegenerateRepayment($loanId, $run = false)
    {
        /**
         * @var OnlineProduct $loan
         */
        $loan = OnlineProduct::find()
            ->where(['id' => $loanId])
            ->andWhere(['in', 'status', [2,3,5,7]])
            ->andWhere('jixi_time is not null')
            ->andWhere(['is_jixi' => true])
            ->one();
        if (is_null($loan)) {
            throw new \Exception('没有找到符合条件标的');
        }

        $plan = OnlineRepaymentPlan::find()
            ->where(['online_pid' => $loan->id])
            ->andWhere(['in', 'status', [1, 2]])
            ->one();
        if (!is_null($plan)) {
            throw new \Exception('暂时不支持已经还过款的标的');
        }
        $plan = OnlineRepaymentPlan::find()
            ->where(['online_pid' => $loan->id])
            ->andWhere('asset_id is not null')
            ->one();
        if (!is_null($plan)) {
            throw new \Exception('暂时不支持已经转让的标的');
        }
        $this->stdout("标的状态正常，可以进行重新计息操作 \n");
        $plans = OnlineRepaymentPlan::find()->where(['online_pid' => $loan->id])->all();
        $repayments = Repayment::find()->where(['loan_id' => $loan->id])->all();
        $plansCount = count($plans);
        $repaymentsCount = count($repayments);
        $this->stdout("即将删除 online_repayment_plan 记录条数为 {$plansCount} 条， 即将删除 repayment 记录条数为 {$repaymentsCount} 条 \n");

        if ($run) {
            $deletePlanCount = 0;
            $deleteRepaymentCount = 0;
            /**
             * @var OnlineRepaymentPlan $plan
             * @var Repayment   $repayment
             */
            foreach ($plans as $plan) {
                file_put_contents('/tmp/online_product_'.$loan->id.'_plan_old.txt', json_encode($plan->attributes()) . "\n", FILE_APPEND);
                $plan->delete();
                $deletePlanCount++ ;
            }
            $this->stdout("成功删除 online_repayment_plan 条数 $deletePlanCount \n");
            foreach ($repayments as $repayment) {
                file_put_contents('/tmp/online_product_'.$loan->id.'_repayment_old.txt', json_encode($repayment->attributes()) . "\n", FILE_APPEND);
                $repayment->delete();
                $deleteRepaymentCount++;
            }
            $this->stdout("成功删除 repayment 条数 $deleteRepaymentCount \n");


            OnlineRepaymentPlan::saveRepayment($loan);

            $this->stdout("新还款数据更新成功 \n");

            $plans = OnlineRepaymentPlan::find()->where(['online_pid' => $loan->id])->all();
            $repayments = Repayment::find()->where(['loan_id' => $loan->id])->all();
            $plansCount = count($plans);
            $repaymentsCount = count($repayments);
            $this->stdout("更新后 online_repayment_plan 记录条数为 {$plansCount} 条，  repayment 记录条数为 {$repaymentsCount} 条 \n");
        }
    }

    /**
     * 脚本工具：修复标的在温都上标成功但是在联动未发标的数据 php yii tool/publish-loan
     */
    public function actionPublishLoan($loanId, $run = false)
    {
        $loan = OnlineProduct::find()
            ->where(['online_status' => 1, 'id' => $loanId])
            ->andWhere('publishTime is not null')
            ->one();
        if (empty($loan)) {
            throw new \Exception('标的未找到');
        }
        $resp = Yii::$container->get('ump')->getLoanInfo($loan->id);
        if (!$resp->isSuccessful()) {
            throw new \Exception('标的状态查询失败');
        }
        echo '标的在联动状态:' . $resp->get('project_state') . "\n";
        if ($resp->get('project_state') === '92' && $run) {
            $resp = Yii::$container->get('ump')->updateLoanState($loan->id, 0);
            if (!$resp->isSuccessful()) {
                throw new \Exception('联动状态修改失败');
            }
            echo '标的在联动状态:' . $resp->get('project_state') . "\n";
        }
    }

    /**
     * 工具脚本:统计平台一段时间的复投率，默认时间段为本月  php yii tool/platform-rate
     */
    public function actionPlatformRate($startDate = null, $endDate = null)
    {
        if (empty($startDate) || false === strtotime($startDate)) {
            $startDate = date('Y-m-01');
        }
        if (empty($endDate) || false === strtotime($endDate)) {
            $endDate = date('Y-m-t');
        }
        //提现数据
        $drawCount = 0;
        $drawAmount = 0;
        $drawData = Yii::$app->db->createCommand("SELECT COUNT( DISTINCT uid ) as drawUser, SUM( money ) as drawAmount 
FROM  `draw_record` 
WHERE STATUS =2
AND DATE( FROM_UNIXTIME( created_at ) ) 
BETWEEN  :startDate
AND  :endDate", [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->queryOne();
        $this->stdout("$startDate 到 $endDate 平台成功提现数据如下: \n");
        if (!empty($drawData)) {
            $drawCount = $drawData['drawUser'];
            $drawAmount = $drawData['drawAmount'];
        }
        $this->stdout("提现人数: $drawCount 人; 提现金额: ".number_format($drawAmount, 2)." 元; \n");

        //回款数据
        $refundData = Yii::$app->db->createCommand("SELECT uid, SUM( benxi ) AS amount
FROM online_repayment_plan
WHERE STATUS IN ( 1, 2 ) 
AND DATE(  `actualRefundTime` ) 
BETWEEN  :startDate
AND  :endDate
AND benxi >0
GROUP BY uid", [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->queryAll();
        $refundCount = count($refundData);
        if ($refundCount === 0) {
            $this->stdout("指定时间段内没有还款数据 \n");
            exit();
        }
        $refundAllUsers = array_column($refundData, 'uid');
        $refundAllAmount = ArrayHelper::index($refundData, 'uid');
        $refundUserToString = implode(',', $refundAllUsers);
        $refundAmount = array_sum(array_column($refundAllAmount, 'amount'));
        $reinvestAmount = 0;
        $increaseInvestAmount = 0;

        //既有回款又有投资，并且不是首投用户 投资数据
        $sql = "SELECT o.uid,sum(o.order_money) as amount
FROM online_order AS o
INNER JOIN user_info AS i ON o.uid = i.user_id
WHERE o.`status` =1
AND DATE( FROM_UNIXTIME( o.order_time ) ) 
BETWEEN  :startDate
AND  :endDate
AND o.uid
IN (" . $refundUserToString . ")
AND i.firstInvestDate != i.lastInvestDate
AND o.order_money > 0.1
group by o.uid
";
        $userInvestData = Yii::$app->db->createCommand($sql, [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->queryAll();
        $reinvestUserCount = count($userInvestData);

        //统计每个用户的 复投金额 和 新增金额
        foreach ($userInvestData as $item) {
            $userId = $item['uid'];
            $amount = $item['amount'];
            if (!isset($refundAllAmount[$userId])) {
                throw new \Exception("没有找到 $userId 的还款数据");
            }
            //回款金额
            $userRefundAmount = $refundAllAmount[$userId]['amount'];
            //复投金额
            if ($amount > $userRefundAmount) {
                $reinvestAmount = bcadd($reinvestAmount, $userRefundAmount, 2);
                $increaseInvestAmount = bcadd($increaseInvestAmount, bcsub($amount, $userRefundAmount, 2), 2);
            } else {
                $reinvestAmount = bcadd($reinvestAmount, $amount, 2);
            }
        }
        $rate = bcmul(bcdiv($reinvestAmount, $refundAmount, 4), 100, 2);

        $this->stdout("$startDate 到 $endDate 平台复投率统计数据如下: \n");
        $this->stdout("复投总额: " . number_format($reinvestAmount, 2) . "元 ; 复投人数: " . $reinvestUserCount . " \n");
        $this->stdout("回款总额: " . number_format($refundAmount, 2) . "元; 回款人数: " . $refundCount . "\n");
        $this->stdout("平台新增金额: " . number_format($increaseInvestAmount, 2) . "元; 平台复投率: " . $rate . "% \n");
    }

    /**
     * 工具脚本：根据副标题统计一系列标的在回款 $n 天后的复投情况 php yii tool/loan-rate
     */
    public function actionLoanRate($title, $n = 10)
    {

        $n = intval($n);
        if ($n <= 0) {
            $n = 10;
        }
        if (empty($title)) {
            throw new \Exception("副标题不能为空");
        }
        $loans = OnlineProduct::find()
            ->where(['like', 'internalTitle', $title])
            ->andWhere(['status' => OnlineProduct::STATUS_OVER])
            ->all();
        if (count($loans) > 500) {
            throw new \Exception('根据副标题找到标的超过 100 条');
        }
        $validateData = [];
        foreach ($loans as $key => $loan) {
            /**
             * @var Repayment $repayment
             * @var OnlineProduct $loan
             */
            $repayment = Repayment::find()->where(['loan_id' => $loan->id, 'isRefunded' => 1])->orderBy(['term' => SORT_DESC])->one();
            if (is_null($repayment)) {
                $this->stdout("没有找到  $loan->title 的还款数据 \n");
                exit();
            }
            $validateData[] = [
                'loan' => $loan,
                'repayment' => $repayment,
                'startTime' => strtotime($repayment->refundedAt),
                'endTime' => (new \DateTime($repayment->refundedAt))->add(new \DateInterval('P'.$n.'D'))->getTimestamp(),
            ];
        }

        $fp = fopen('/tmp/loan.csv', 'w');
        fputcsv($fp, [
            '标的副标题',
            '标的标题',
            '标的还款时间',
            '复投查询截止时间',
            '还款金额',
            '还款人数',
            '复投金额',
            '复投人数',
            '复投率',
        ]);
        $count = 0;
        foreach ($validateData as $item) {
            /**
             * @var OnlineProduct $loan
             * @var Repayment $repayment
             */
            $loan = $item['loan'];
            $repayment = $item['repayment'];
            $startTime = $item['startTime'];
            $endTime = $item['endTime'];

            //回款金额
            $refundAmount = $repayment->amount;

            $data = OnlineRepaymentPlan::find()
                ->select('uid')
                ->distinct()
                ->where(['online_pid' => $loan->id])
                ->andWhere(['status' => [1, 2]])
                ->asArray()
                ->all();
            $userIds = array_column($data, 'uid');
            //回款人数
            $refundUserCount = count($userIds);
            $orderData = OnlineOrder::find()
                ->select(['uid', 'order_money'])
                ->where(['in', 'uid', $userIds])
                ->andWhere(['status' => OnlineOrder::STATUS_SUCCESS])
                ->andWhere(['between', 'order_time', $startTime, $endTime])
                ->asArray()
                ->all();
            $orderUserIds = array_column($orderData, 'uid');
            $orderUserIds = empty($orderData) ? [] : array_unique($orderUserIds);
            $orderMoney = array_column($orderData, 'order_money');
            $orderMoney = empty($orderMoney) ? 0 : array_sum($orderMoney);
            $txOrder = CreditOrder::find()
                ->select(['user_id', 'amount'])
                ->where(['in', 'user_id', $userIds])
                ->andWhere(['status' => CreditOrder::STATUS_SUCCESS])
                ->andWhere(['between', 'createTime', date('Y-m-d H:i:s', $startTime), date('Y-m-d H:i:s', $endTime)])
                ->asArray()
                ->all();
            $txUserIds = array_column($txOrder, 'user_id');
            $txUserIds = empty($txUserIds) ? [] : array_unique($txUserIds);
            $txMoney = array_column($txOrder, 'amount');
            $txMoney = empty($txMoney) ? 0 : array_sum($txMoney);
            //复投数据
            $buyUserIds = array_merge($orderUserIds, $txUserIds);
            $buyUserIds = array_unique($buyUserIds);
            $buyUserCount = count($buyUserIds);
            $buyAmount = bcadd($orderMoney, $txMoney, 2);

            $rate = 0;
            if ($refundAmount > 0) {
                $rate = bcmul(bcdiv($buyAmount, $refundAmount, 4), 100, 2);
            }
            fputcsv($fp, [
                $loan->internalTitle,
                $loan->title,
                $repayment->refundedAt,
                date('Y-m-d H:i:s', $endTime),
                number_format($refundAmount, 2),
                $refundUserCount,
                number_format($buyAmount, 2),
                $buyUserCount,
                $rate . '%',
            ]);
            $count++;
        }
        $this->stdout("共处理 $count 个标的 \n");
        fclose($fp);
    }

    /**
     * 修复重复满标数据 php yii tool/duplicate-establish
     */
    public function actionDuplicateEstablish($loanId, $run = false)
    {
        $loan = OnlineProduct::find()->where(['id' => $loanId])->one();
        if (is_null($loan)) {
            throw new \Exception('没有找到数据');
        }
        $orders = OnlineOrder::find()->where([
            'online_pid' => $loanId,
            'status' => 1,
        ])->all();
        $count = count($orders);
        $dirtyCount = 0;
        $successCount = 0;
        $errorCount = 0;
        $this->stdout("共找到 $count 个订单 \n");
        if ($count <= 0) {
            throw new \Exception('没有找到数据');
        }
        foreach ($orders as $order) {
            /**
             * @var OnlineOrder $order
             */
            $moneyRecords = MoneyRecord::find()->where([
                'osn' => $order->sn,
                'type' => MoneyRecord::TYPE_FULL_TX,
            ])->orderBy(['id' => SORT_DESC])->all();
            $recordCount = count($moneyRecords);
            if ($recordCount === 1) {
                continue;
            }
            $this->stdout("订单 {$order->id} 用户 {$order->uid} 标的成立流程处理重复, 同一笔订单有 $recordCount 条满标流水 \n");
            if ($recordCount > 2) {
                throw new \Exception('暂时不支持超过２次重复执行情况');
            }
            $dirtyCount++;
            if ($run) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    /**
                     * @var MoneyRecord $moneyRecord
                     */
                    $moneyRecord = current($moneyRecords);
                    if (is_null($moneyRecord)) {
                        throw new \Exception("没有获取到订单 {$order->id} 的重复资金流水");
                    }
                    if (bccomp($moneyRecord->in_money, $order->order_money, 2) !== 0) {
                        throw new \Exception("资金流水记录的变动资金和订单金额不相等 \n");
                    }
                    //删除　money_record　
                    $moneyRecord->delete();
                    //更新　user_account 账户信息
                    $sql = "update user_account set freeze_balance = freeze_balance + :orderMoney, investment_balance = investment_balance - :orderMoney where uid = :userId";
                    Yii::$app->db->createCommand($sql, [
                        'orderMoney' => $order->order_money,
                        'userId' => $order->uid,
                    ])->execute();

                    $transaction->commit();
                    $successCount++;
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    $errorCount++;
                    $this->stdout("修改数据库失败，" . $e->getMessage() . "\n");
                }
            }
        }

        $this->stdout("需要修复数据个数 $dirtyCount, 成功修复订单个数 $successCount, 修复失败个数 $errorCount \n");
    }

    /**
     * 修复线上充值事故
     * 问题：全量更新status=1
     */
    public function actionRechargeEvent()
    {
        $r = RechargeRecord::tableName();
        $u = User::tableName();
        $e = EpayUser::tableName();
        $rechargeRecordQuery = (new Query())
            ->select("$r.*,$u.type uType,$e.epayUserId")
            ->from($r)
            ->innerJoin($u, "$u.id = $r.uid")
            ->innerJoin($e, "$e.appUserId = $u.id")
            ->where(["$r.status" => 1])
            ->andWhere(['<=', "$r.created_at", strtotime('2017-12-15 14:00:00')])
            ->orderBy(["$r.uid" => SORT_DESC]);

        $fileCache = Yii::$app->cache;
        $moneyRechargeSns = $fileCache->getOrSet('repairSns', function ($cache) {
            return MoneyRecord::find()
                ->select('osn')
                ->where(['in', 'type', [MoneyRecord::TYPE_RECHARGE_POS, MoneyRecord::TYPE_RECHARGE]])
                ->column();
        });

        $num = 0;
        $timesCount = 0;
        $balanceUids = [];
        $file = Yii::getAlias('@app/runtime/repairSns'.'.csv');
        file_put_contents($file, "用户ID\t本地用户余额\t联动用户余额\t充值流水sn\t本地充值订单状态\t联动充值订单状态0初始1成功2失败5交易关闭46不明".PHP_EOL);
        foreach ($rechargeRecordQuery->batch(200) as $rechargeRecords) {
            foreach ($rechargeRecords as $rechargeRecord) {
                $uType = (int) $rechargeRecord['uType'];
                $status = (int) $rechargeRecord['status'];
                $uid = $rechargeRecord['uid'];
                if (1 === $status && in_array($rechargeRecord['sn'], $moneyRechargeSns)) {
                    continue;
                }

                $balance = '**'; //占位符
                $umpBalance = '**'; //占位符
                if (!in_array($uid, $balanceUids)) {
                    //获取本地余额
                    if (1 === $uType) {
                        $userAccount = UserAccount::find()
                            ->where(['user_account.type' => UserAccount::TYPE_LEND])
                            ->andWhere(['uid' => $rechargeRecord['uid']])
                            ->one();
                    } else {
                        $userAccount = UserAccount::find()
                            ->where(['user_account.type' => UserAccount::TYPE_BORROW])
                            ->andWhere(['uid' => $rechargeRecord['uid']])
                            ->one();
                    }
                    $balance = null === $userAccount ? 0 : $userAccount['available_balance'] * 100;

                    //获取联动余额
                    $umpBalance = 0;
                    if (2 === $uType) {
                        $resp = \Yii::$container->get('ump')->getMerchantInfo($rechargeRecord['epayUserId']);
                        if ($resp->isSuccessful()) {
                            $umpBalance = $resp->get('balance');
                            $balances[$uid]['ump'] = $umpBalance;
                        }
                    } else {
                        $userUmpInfo = Yii::$container->get('ump')->getUserInfo($rechargeRecord['epayUserId']);
                        if ($userUmpInfo->isSuccessful()) {
                            $umpBalance = $userUmpInfo->get('balance');
                        }
                    }
                }
                $balanceUids[] = $uid;

                $umpStatus = null; //初始状态null
                $resp = Yii::$container->get('ump')->getRechargeInfo($rechargeRecord['sn'], $rechargeRecord['created_at']);
                if ($resp->isSuccessful()) {
                    $tranState = (int) $resp->get('tran_state');
                    if (2 === $tranState) {
                        $umpStatus = RechargeRecord::STATUS_YES;
                    } elseif (3 === $tranState) {
                        $umpStatus = RechargeRecord::STATUS_FAULT;
                    } else {
                        $umpStatus = $tranState;
                    }
                    if ($status === $umpStatus) {
                        continue;
                    }
                }
                $num++;
                $data = $rechargeRecord['uid']."\t".$balance."\t".$umpBalance."\t".$rechargeRecord['sn'] . "\t" . $status . "\t" . $umpStatus . PHP_EOL;
                file_put_contents($file, $data, FILE_APPEND);
            }
            $timesCount++;
            echo $timesCount.PHP_EOL;
        }

        $this->stdout('需要修复的充值记录条数有'.$num.'条');
    }

    /**
     * 向指定标的贴息
     *
     * 注意不要多次执行此代码
     *
     * @param int    $loanId 标的IID
     * @param string $amount 金额
     *
     * @throws \Exception
     */
    public function actionTransferToLoan($loanId, $amount)
    {
        if (is_null($loanId) || $amount <= 0) {
            throw new \Exception('参数错误');
        }

        $loan = OnlineProduct::findOne($loanId);
        if (null === $loan) {
            throw new \Exception('标的未找到');
        }
        if ($loan->status >= 4 && $loan->status < 7) {
            throw new \Exception('标的状态不对');
        }

        $paymentLog = new PaymentLog([
            'txSn' => TxUtils::generateSn('P'),
            'createdAt' => time(),
            'loan_id' => $loanId,
            'amount' => $amount,
        ]);

        $res = Yii::$container->get('ump')->merOrder($paymentLog);
        if (!$res->isSuccessful()) {
            throw new \Exception($res->get('ret_msg'));
        }

        $this->stdout('向标的ID'.$loanId.'贴息'.$amount.'元');
    }

    public function actionLhwtInvest($loanSn, $money)
    {
        $lhwtEpayUserId = Yii::$app->params['ump']['lhwt_merchant_id'];
        $loan = OnlineProduct::findOne(['sn' => $loanSn]);
        if (null === $loan) {
            throw new Exception('标的信息不存在');
        }
        if (2 !== $loan->status) {
            throw new \Exception('当前标的非募集中状态');
        }

        //请求联动client
        $ump = \Yii::$container->get('ump');

        //查询联动余额：
        $merchantInfo = $ump->getMerchantInfo($lhwtEpayUserId);
        $userBalance = bcdiv($merchantInfo->get('balance'), 100, 2);
        $userBalancestr = '当前联动投资者账户联动余额为：'.$userBalance.'元'.PHP_EOL;
        $this->stdout($userBalancestr);
        Yii::info($userBalancestr, 'recharge_log');

        //查询标的余额
        $loanInfo = $ump->getLoanInfo($loan->id);
        if ($loanInfo->isSuccessful()) {
            $balance = bcdiv($loanInfo->get('balance'), 100, 2);
        } else {
            $this->stdout($loanInfo->get('ret_msg'));
            $balance = 0;
        }
        $loanStr = '当前联动标的账户余额为：'.$balance.'元'.PHP_EOL;
        $this->stdout($loanStr);
        Yii::info($loanStr, 'recharge_log');

        //联动用户余额与投资金额，投资金额与标的剩余账户余额比较
        if (bccomp($userBalance, $money, 2) < 0) {
            Yii::info('用户联动当前余额小于投资余额'.PHP_EOL, 'recharge_log');
            throw new \Exception('用户联动当前余额小于投资余额');
        }

        //标的投资金额与剩余可投资金额作比较
        $restBalance = bcsub($loan->money, $balance, 2);
        $loanRestStr = '当前联动标的剩余账户余额为：'.$restBalance.'元'.PHP_EOL;
        Yii::info($loanRestStr, 'recharge_log'.PHP_EOL);
        $this->stdout($loanRestStr);
        if (bccomp($money, $restBalance, 2) > 0) {
            Yii::info('用户投资金额大于标的账户余额'.PHP_EOL, 'recharge_log');
            throw new \Exception('用户投资金额大于标的账户余额');
        }

        //虚拟订单对象
        $epayUser = EpayUser::find()
            ->where(['epayUserId' => $lhwtEpayUserId])
            ->one();
        $uid = $epayUser->appUserId;
        $order = new OnlineOrder([
            'online_pid' => $loan->id,
            'sn' => OnlineOrder::createSN(),
            'uid' => $uid,
            'yield_rate' => $loan->yield_rate,
            'created_at' => time(),
            'order_money' => $money,
            'order_time' => time(),
            'paymentAmount' => $money,
            'status' => 1,
        ]);

        //企业用户免密投资
        $ret = $ump->orderCompanyNopass($order, $lhwtEpayUserId);
        if ($ret->isSuccessful()) {
            $finishRate = bcdiv(bcadd($balance, $money, 2), $loan->money, 2);
            if (bccomp($finishRate, 1, 2) < 0) {
                $sql = "update online_product set funded_money=funded_money+:fundedMoney,finish_rate=:finishRate where id=:id";
                Yii::$app->db->createCommand($sql, [
                    'fundedMoney' => $money,
                    'finishRate' => $finishRate,
                    'id' => $loan->id,
                ])->execute();
            } else {
                $sql = "update online_product set funded_money=funded_money+:fundedMoney,full_time=:fullTime,finish_rate=:finishRate where id=:id";
                Yii::$app->db->createCommand($sql, [
                    'fundedMoney' => $money,
                    'fullTime' => time(),
                    'finishRate' => $finishRate,
                    'id' => $loan->id,
                ])->execute();
            }

            $order->save(false);
            $successStr = '【投资成功】订单编号：'.$order->sn.'，标的sn：'.$loanSn.'，投资者：立合旺通，投资时间:'.$order->created_at.'，投资金额：'.$money.PHP_EOL;
            Yii::info($successStr, 'recharge_log');
            $this->stdout($successStr);
        } else {
            $this->stdout($ret->get('ret_msg'));
            $failStr = '【投资失败】订单编号：'.$order->sn.'，标的sn：'.$loanSn.'，投资者：立合旺通，投资时间:'.$order->created_at.'，投资金额：'.$money.PHP_EOL;
            Yii::info($failStr, 'recharge_log');
            $this->stdout($failStr);
        }
    }

    public function actionOrgFee($startDate, $endDate)
    {
        $file = Yii::getAlias('@app/runtime/orgFee'.$startDate.'-'.$endDate.'csv');
        $ump = Yii::$container->get('ump');
        if (false === strtotime($startDate) || false === strtotime($endDate)) {
            throw new \Exception('日期格式不对');
        }
        $startDateTime = new \DateTime($startDate);
        $endDateTime = new \DateTime($endDate);

        $days = $endDateTime->diff($startDateTime)->days;
        $num = ceil($days / 30);

        for ($i = 1; $i <= $num; $i++) {
            //首次查询日期为开始日期，然后每次开始日期+30天，结束日期在开始日期的及基础上再加30天
            $dueStartDateTime = 1 === $i ? $startDateTime : $startDateTime->add((new \DateInterval('P31D')));
            $dueCloneDateTime = clone $dueStartDateTime;
            $dueEndDateTime = $dueCloneDateTime->add((new \DateInterval('P30D')));
            $dueEndDateTime = $dueEndDateTime >= $endDateTime ? $endDateTime : $dueEndDateTime;

            $dueStartDate = $dueStartDateTime->format('Ymd');
            $dueEndDate = $dueEndDateTime->format('Ymd');
            $this->stdout($dueStartDate.'-'.$dueEndDate.'开始查询'.PHP_EOL);
            $response = $ump->orgFee($dueStartDate, $dueEndDate, 1);
            if ($response->isSuccessful()) {
                $totalNum = $response->get('total_num');
                $pageNum = ceil($totalNum / 10);
                for ($j = 1; $j <= $pageNum; $j++) {
                    $responseByPage = $ump->orgFee($dueStartDate, $dueEndDate, $pageNum);
                    $transDetail = $responseByPage->get('trans_detail');
                    $transDetail = str_replace('|', PHP_EOL, $transDetail);
                    $transDetail = str_replace(',', "\t", $transDetail);
                    file_put_contents($file, $transDetail.PHP_EOL, FILE_APPEND);
                    sleep(1);
                }
                $this->stdout($dueStartDate.'-'.$dueEndDate.'执行查询完毕，共计'.$totalNum.'条'.PHP_EOL);
            }
        }
    }

    /**
     * 新手标不放款直接还款，利息补发仍从融资者账户转入标的账户
     *
     * @param int $loanId 新手标ID
     *
     * @throws \Exception
     * @return boolean
     */
    public function actionXsRepayment($loanId)
    {
        $loan = OnlineProduct::findOne($loanId);
        $qishu = 1;    //新手标只有1期

        //判断标的是否存在
        if (null === $loan) {
            throw new \Exception('标的不存在');
        }

        //判断标的是否为新手标
        if (!$loan->is_xs) {
            throw new \Exception('非新手标不允许直接还款');
        }

        //判断当前标的状态为已经计息但未放款阶段
        if (!($loan->is_jixi && !in_array($loan->status, [OnlineProduct::STATUS_HUAN, OnlineProduct::STATUS_OVER]))) {
            throw new \Exception('当前标的必须处于已经计息但未放款阶段');
        }

        //检查还款计划是否存在
        $plans = OnlineRepaymentPlan::find()
            ->where(['online_pid' => $loanId])
            ->andWhere(['status' => OnlineRepaymentPlan::STATUS_WEIHUAN])
            ->andWhere(['qishu' => $qishu])
            ->all();
        if (0 === count($plans)) {
            throw new \Exception('没有需要还款的项目');
        }

        //检查还款记录
        $repayment = Repayment::find()
            ->where(['loan_id' => $loanId, 'term' => $qishu])
            ->one();
        if (null === $repayment) {
            throw new \Exception('还款信息不存在');
        }

        /** 第一步：融资者账户补息到标的账户 */
        //检查融资者用户信息
        $borrower = $loan->borrower;
        if (null === $borrower) {
            throw new \Exception('融资者用户信息不存在');
        }

        //检查融资者用户账户信息
        $borrowerAccount = $borrower->borrowAccount;
        if (null === $borrowerAccount) {
            throw new \Exception('融资者用户账户信息不存在');
        }

        //检查融资者对应联动账户信息
        $borrowerEpayUser = $borrower->epayUser;
        if (null === $borrowerEpayUser) {
            throw new \Exception('融资者对应联动账户信息不存在');
        }

        //判断温都融资者账户余额是否足够
        $totalFund = $repayment->interest;
        if (bccomp($totalFund, $borrowerAccount->available_balance, 2) > 0) {
            throw new \Exception('融资者用户账户余额不足');
        }

        //当不允许访问联动时候，默认联动测处理成功
        $ump = Yii::$container->get('ump');
        if (Yii::$app->params['ump_uat']) {
            //修改联动标的为还款中状态
            $resp = Yii::$container->get('ump')->updateLoanState($loan->id, 2);
            if (!$resp->isSuccessful()) {
                throw new Exception('联动状态修改失败:' . $resp->get('ret_msg'));
            }

            //调用联动接口，查看联动标的状态
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

            $orgResp = $ump->getMerchantInfo($borrowerEpayUser->epayUserId);
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

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        //新手标不支持加息券，暂不考虑
        try {
            //更新还款记录状态
            $sql = "update repayment set isRepaid=:isRepaid,repaidAt=:repaidAt where id = :repaymentId and isRepaid=false";
            $updateRepayment = $db->createCommand($sql, [
                'isRepaid' => true,
                'repaidAt' => date('Y-m-d H:i:s'),
                'repaymentId' => $repayment->id,
            ])->execute();
            if (!$updateRepayment) {
                $transaction->rollBack();
                throw new \Exception('更新还款记录融资账户回款状态失败');
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
                'remark' => '还款记录ID：'.$repayment->id.',还款总计:'.$totalFund.'元',
            ]);
            if (!$borrowerMoneyRecord->save()) {
                throw new \Exception('还款资金流水记录失败');
            }

            //新手标皆为到期本息，故直接更新标的为已还清状态
            $updateLoanSql = "update online_product set status=:status,sort=:sort where id=:loanId and status not in (5, 6)";
            $updateLoan = $db->createCommand($updateLoanSql, [
                'status' => OnlineProduct::STATUS_OVER,
                'sort' => OnlineProduct::SORT_YHK,
                'loanId' => $loan->id,
            ])->execute();
            if (!$updateLoan) {
                $transaction->rollBack();
                throw new \Exception('修改标的状态从还款中到已还清错误');
            }

            if (Yii::$app->params['ump_uat']) {
                //联动一测融资用户还款利息到标的账户
                if ($totalFund > 0) {
                    $hk = $ump->huankuan(
                        TxUtils::generateSn('HK'),
                        $loan->id,
                        $borrowerEpayUser->epayUserId,
                        $totalFund);

                    if (!$hk->isSuccessful()) {
                        throw new \Exception($hk->get('ret_code').$hk->get('ret_msg'));
                    }
                }
            }
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw new \Exception('标的账户放款标的利息失败'. $ex->getMessage());
        }

        /** 第二步：标的账户返标的利息到投资人账户*/
        $this->loanRefundToLender($loan, $plans, $repayment->id);

        /** 第三步：更新用户资产回款状态及发送短信消息及微信推送 */
        //还款短信
        $userIds = array_unique(ArrayHelper::getColumn($plans, 'uid'));
        $this->sendRefundSms($loan, $qishu, $userIds);

        //更新资产回款状态（TX）
        $this->updateAssetRepaidStatus($loan);

        //微信推送给还款成功信息投资者
        $this->repaySuccessPush($plans, $repayment);
    }

    /**
     * 标的账户返款到投资人账户
     *
     * @param OnlineProduct $loan        标的
     * @param array         $plans       还款计划
     * @param int           $repaymentId 还款记录ID
     *
     * @return bool
     * @throws \Exception
     */
    private function loanRefundToLender($loan, $plans, $repaymentId)
    {
        //检查融资者返款到账户是否完成，若完成直接返回true，进行下一步
        $refundRepayment = Repayment::find()
            ->where(['id' => $repaymentId])
            ->one();
        if (true === (bool) $refundRepayment->isRefunded) {
            return true;
        }

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
                    $fkResp = $ump->fankuan($plan->sn, $lenderRepaymentRecord->refund_time, $plan->online_pid, $user->epayUser->epayUserId, $plan->benxi);
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
                throw new \Exception('更新还款记录失败');
            }
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
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
                    $repaymentRecord['real_name'],
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
     * 根据放款记录新建一笔提现
     * 两种情况：
     * 1）已经提现受理，但未生成提现，资金等流水记录，requireUmp传参数为0
     * 2）提现失败或者未受理，需要重现生成，requireUmp可不传
     *
     * @param string $sn 放款sn
     * @param integer $requireUmp 是否请求联动 0不请求1请求
     *
     * @throws \Exception
     */
    public function actionFkDraw($sn, $requireUmp = 1)
    {
        $requireUmp = boolval($requireUmp);
        $onlineFangkuan = OnlineFangkuan::find()
            ->where(['sn' => $sn])
            ->one();
        if (null === $onlineFangkuan) {
            throw new \Exception('融资用户账户信息不存在', '000001');
        }
        $account = UserAccount::findOne(['uid' => $onlineFangkuan->uid, 'type' => UserAccount::TYPE_BORROW]);
        if (!$account) {
            throw new \Exception('融资用户账户信息不存在', '000002');
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

            if ($requireUmp) {
                $ump = Yii::$container->get('ump');
                $resp = $ump->orgDrawApply($draw);
                if (!$resp->isSuccessful()) {
                    throw new \Exception($resp->get('ret_code').$resp->get('ret_msg'));
                }
            }

            DrawManager::ackDraw($draw);
            $transaction->commit();

            $this->stdout('提现受理中...');
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * 标的转账到融资用户虚拟户，可选择是否自动帮助提现
     *
     * @param string $sn 标的sn
     * @param string $uid 融资用户ID
     * @param string $money 转账金额
     * @param integer $isAutoDraw 是否自动提现 1是0否
     * @param integer $requireUmp 是否调用联动提现 1是0否
     *
     * @throws \Exception
     */
    public function actionLoanToMer($sn, $uid, $money, $isAutoDraw = 0, $requireUmp = 1)
    {
        $ump = Yii::$container->get('ump');

        //验证金额
        if (!is_numeric($money)) {
            throw new \Exception('金额错误');
        }

        //查询温都标的状态
        $loan = OnlineProduct::find()
            ->where(['sn' => $sn])
            ->one();
        if (null === $loan) {
            throw new \Exception('标的不存在');
        }
        if (!in_array($loan->status, ['2', '3', '5', '7'])) {
            throw new \Exception('标的状态必须为募集中~还款中');
        }

        //查询用户是否为融资用户
        $borrower = User::findOne($uid);
        if (null === $borrower || !$borrower->isOrgUser()) {
            throw new \Exception('用户非融资用户');
        }

        //调用联动接口，查看联动标的状态
        $loanResp = $ump->getLoanInfo($loan->id);
        if (!$loanResp->isSuccessful()) {
            throw new \Exception($loanResp->get('ret_msg'));
        }
        if ('02' === $loanResp->get('project_account_state')) {
            throw new \Exception('当前联动标的状态为冻结状态');
        }

        //将联动标的状态置为还款中
        LoanService::updateLoanState($loan, OnlineProduct::STATUS_HUAN);
        $epayUserId = $borrower->epayUser->epayUserId;
        $resp = $ump->loanTransferToMer1(TxUtils::generateSn('LTM'), date('Ymd'), $loan->id, $epayUserId, $money);
        if (!$resp->isSuccessful()) {
            throw new \Exception('联动一侧：'.$resp->get('ret_msg'));
        }

        //更新融资者账户余额
        $res = Yii::$app->db->createCommand("UPDATE `user_account` SET `account_balance` = `account_balance` + :money, `available_balance` = `available_balance` + :money, `drawable_balance` = `drawable_balance` + :money, `in_sum` = `in_sum` + :money WHERE `uid` = :uid and `type` = :userType", [
            'money' => $money,
            'uid' => $uid,
            'userType' => UserAccount::TYPE_BORROW,
        ])->execute();
        if (!$res) {
            throw new \Exception('更新融资账户异常', '000003');
        }

        $this->stdout('已将标的账户金额转账到融资者账户中');
        if ($isAutoDraw) {
            $account = UserAccount::findOne(['uid' => $borrower->id, 'type' => UserAccount::TYPE_BORROW]);
            if (!$account) {
                throw new \Exception('融资用户账户信息不存在', '000002');
            }

            $transaction = Yii::$app->db->beginTransaction();

            try {
                //融资方放款,不收取手续费
                $draw = DrawManager::initDraw($account, $money);
                if (!$draw->save()) {
                    throw new \Exception('提现申请失败', '000003');
                }

                $draw->orderSn = $sn;
                if (!$draw->save()) {
                    throw new \Exception('写入提现流水失败', '000003');
                }

                if ($requireUmp) {
                    $resp = $ump->orgDrawApply($draw);
                    if (!$resp->isSuccessful()) {
                        throw new \Exception($resp->get('ret_code').$resp->get('ret_msg'));
                    }
                }

                DrawManager::ackDraw($draw);
                $transaction->commit();

                $this->stdout('提现受理中...');
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
    }

    /**
     * 给用户发指定的现金
     *
     * @param integer $userId 用户ID
     * @param string $money 金额
     *
     * @return int
     * @throws \Exception
     */
    public function actionSendCash($userId, $money)
    {
        $user = User::findOne($userId);
        if (null === $user) {
            throw new \Exception('用户不存在');
        }
        AccountService::userTransfer($user, $money);

        $this->stdout('现金已发送');
        return self::EXIT_CODE_NORMAL;
    }
}
