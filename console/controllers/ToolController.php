<?php

namespace console\controllers;

use common\models\draw\DrawManager;
use common\models\epay\EpayUser;
use common\models\mall\ThirdPartyConnect;
use common\models\order\OnlineRepaymentPlan;
use common\models\payment\Repayment;
use common\models\product\OnlineProduct;
use common\models\user\DrawRecord;
use common\models\user\User;
use common\models\user\UserAccount;
use common\utils\TxUtils;
use Ding\DingNotify;
use Yii;
use yii\console\Controller;

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
        if(is_null($loan)) {
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
}