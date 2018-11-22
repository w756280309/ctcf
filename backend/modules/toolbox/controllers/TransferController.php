<?php

namespace backend\modules\toolbox\controllers;

use backend\controllers\BaseController;
use common\helpers\TransferHelper;
use common\models\transfer\MoneyTransfer;
use common\models\user\User;
use common\utils\TxUtils;
use YIi;
use common\lib\bchelp\BcRound;
use common\models\draw\DrawManager;
use common\models\user\DrawRecord;
use common\models\user\MoneyRecord;
use common\models\user\UserAccount;

class TransferController extends BaseController
{
    /**
     * 资金转移首页
     * todo 暂时支持商户与平台以及商户与商户之间的转账
     */
    public function actionIndex()
    {
        //企业融资会员信息（用于商户间转账）
        $orgCompanyUsers = $this->orgUserInfo([1, 3]);
        $platformUser = [
            '0' => '--选择--',
            '-1' => '平台现金账户',
        ];
        $withdrawal = [
            '0' => '否',
            '1' => '是',
        ];
        $selectedUsers = $platformUser + $orgCompanyUsers;

        return $this->render('index', [
            'selectedUsers' => $selectedUsers,
            'withdrawal' => $withdrawal,
        ]);
    }

    /**
     * 商户转账（包含平台现金账户）
     *
     * 平台 TO 企业融资者
     * 企业融资者 TO 平台
     * 企业融资者 TO 企业融资者
     */
    public function actionFirst()
    {
        $request = Yii::$app->request;
        $payerId = intval($request->post('payerId'));
        $receiverId = intval($request->post('receiverId'));
        $money = floatval($request->post('money'));

        if (bccomp($money, 1, 2) > 0) {
            $user = Yii::$app->user->getIdentity();
            if (null === $user || !$user->isSuper()) {
                return [
                    'code' => 'fail',
                    'message' => '转账金额大于1元，需要超级管理员才能操作',
                    'data' => [],
                ];
            }
        }

        //构建money_transfer业务流水对象
        $payerType = -1 === $payerId ? 'platform' : 'borrower';
        $receiverType = -1 === $receiverId ? 'platform' : 'borrower';
        $transfer = MoneyTransfer::initNew($payerId, $payerType, $receiverId, $receiverType, $money);

        //执行商户转账
        $transferHelper = new TransferHelper();
        $transferHelper->perform($transfer);

        return [
            'code' => $transfer->status,
            'message' => $transfer->retMsg,
            'data' => $transfer->toArray(),
        ];
    }

    /**
     * 获取商户在联动的余额及在平台的金额
     *
     * @param string $userId 商户平台用户ID
     *
     * @return array
     */
    public function actionGetBalance($userId)
    {
        $data = [
            'ump' => '0',
            'plat' => '0',
        ];
        $userId = intval($userId);
        $user = User::findOne($userId);

        //判断参数userId
        if ($userId < -1) {
            return $data;
        }

        //判断第三方商户ID
        $epayUserId = $this->getEpayUserId($userId);
        if (null === $epayUserId) {
            return $data;
        }

        $ump = Yii::$container->get('ump');
        $merchant = $ump->getMerchantInfo($epayUserId);
        $data['ump'] = bcdiv($merchant->get('balance'), 100, 2);
        if (-1 === $userId) {
            $data['plat'] = $data['ump'];
        } else {
            $borrowAccount = $user->borrowAccount;
            if (null !== $borrowAccount) {
                $data['plat'] = $borrowAccount->available_balance;
            }
        }

        return $data;
    }

    private function getEpayUserId($userId)
    {
        $epayUserId = null;
        if (-1 === $userId) {
            $epayUserId = Yii::$app->params['ump']['merchant_id'];
        } else {
            $user = User::findOne($userId);
            if (null !== $user) {
                $epayUserId = $user->getEpayUserId();
            }
        }

        return $epayUserId;
    }

    public function actionWithdrawal()
    {
        $request = Yii::$app->request;
        $uid = intval($request->post('receiverId'));
        $money = floatval($request->post('money'));
        $requireUmp = 1;
        $ump = Yii::$container->get('ump');
        $account = UserAccount::findOne(['uid' => $uid, 'type' => UserAccount::TYPE_BORROW]);
        if (!$account) {
            throw new \Exception('商户账户信息不存在', '000002');
        }

        //用款方放款,不收取手续费
        $draw = DrawManager::initDraw($account, $money);
        if (!$draw->save()) {
            throw new \Exception('提现申请失败', '000003');
        }

        $draw->orderSn = TxUtils::generateSn('FR');
        if (!$draw->save()) {
            throw new \Exception('写入提现流水失败', '000003');
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            if ($requireUmp) {
                $resp = $ump->orgDrawApply($draw);
                if (!$resp->isSuccessful()) {
                    throw new \Exception($resp->get('ret_code').$resp->get('ret_msg'));
                }
            }

            //处理提现记录
            $sql = "update draw_record set `status` = :drawStatus where `sn` = :drawSn and `status` = 0";
            $affectedRows = $db->createCommand($sql, [
                'drawStatus' => DrawRecord::STATUS_EXAMINED,
                'drawSn' => $draw->sn,
            ])->execute();
            if (0 === $affectedRows) {
                throw new \Exception('【提现受理】提现记录：更新受理状态失败');
            }

            /* 获得当前提现用户账户信息 */
            $draw->refresh();
            $user = $draw->user;
            $fee = $draw->fee;

            //记录money_record及更新user_account，分2块，提现金额、提现手续费
            /* 提现金额 */
            $bc = new BcRound();
            bcscale(14);
            $moneyRecord = new MoneyRecord();
            $moneyRecord->sn = MoneyRecord::createSN();
            $moneyRecord->type = MoneyRecord::TYPE_DRAW;
            $moneyRecord->osn = $draw->sn;
            $moneyRecord->account_id = $account->id;
            $moneyRecord->uid = $user->id;
            $moneyRecord->balance = $bc->bcround(bcsub($account->available_balance, $draw->money), 2);
            $moneyRecord->out_money = $draw->money;
            if (!$moneyRecord->save()) {
                throw new \Exception('【提现受理】资金流水记录：提现金额失败');
            }
            $sql = "update user_account set available_balance = available_balance - :amount, out_sum = out_sum - :amount where id = :accountId";
            $res = $db->createCommand($sql, [
                'amount' => $draw->money,
                'accountId' => $account->id,
            ])->execute();
            if (0 === $res) {
                throw new \Exception('【提现受理】账户余额更新：提现金额失败');
            }

            /* 提现手续费 */
            if ($fee > 0) {
                $account->refresh();
                $feeRecord = new MoneyRecord();
                $feeRecord->osn = $draw->sn;
                $feeRecord->account_id = $account->id;
                $feeRecord->uid = $user->id;
                $feeRecord->sn = MoneyRecord::createSN();
                $feeRecord->type = MoneyRecord::TYPE_DRAW_FEE;
                $feeRecord->balance = $bc->bcround(bcsub($account->available_balance, $draw->fee), 2);
                $feeRecord->out_money = $fee;
                if (!$feeRecord->save()) {
                    throw new \Exception('【提现受理】资金流水记录：提现手续费失败');
                }
                $sql = "update user_account set available_balance = available_balance - :fee, out_sum = out_sum - :fee where id = :accountId";
                $res = $db->createCommand($sql, [
                    'fee' => $draw->fee,
                    'accountId' => $account->id,
                ])->execute();
                if (0 === $res) {
                    throw new \Exception('【提现受理】账户余额更新：提现金额失败');
                }
            }

            //事务提交
            $transaction->commit();
            return [
                'code' => 'success',
                'message' => '提现成功！提现正在处理中。',
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'code' => 'fail',
                'message' => '提现失败！',
            ];
        }

    }
}
