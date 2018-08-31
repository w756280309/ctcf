<?php

namespace backend\modules\toolbox\controllers;

use backend\controllers\BaseController;
use common\helpers\TransferHelper;
use common\models\transfer\MoneyTransfer;
use common\models\user\User;
use common\utils\TxUtils;
use YIi;

class TransferController extends BaseController
{
    /**
     * 资金转移首页
     * todo 暂时支持商户与平台以及商户与商户之间的转账
     */
    public function actionIndex()
    {
        //企业融资会员信息（用于商户间转账）
        $orgCompanyUsers = $this->orgUserInfo([1]);
        $platformUser = [
            '0' => '--选择--',
            '-1' => '平台现金账户',
        ];
        $selectedUsers = $platformUser + $orgCompanyUsers;

        return $this->render('index', [
            'selectedUsers' => $selectedUsers,
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
}
