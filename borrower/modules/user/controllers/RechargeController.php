<?php

namespace app\modules\user\controllers;

use Yii;
use borrower\controllers\BaseController;
use common\models\user\UserAccount;
use common\models\user\RechargeRecord;
use common\models\epay\EpayUser;
use common\models\bank\EbankConfig;
use common\models\bank\BankManager;

class RechargeController extends BaseController
{

    public $layout = '@app/views/layouts/main';

    /**
     * 充值页面显示.
     */
    public function actionInit()
    {
        $bank = BankManager::getEbank('business');

        $user_account = UserAccount::findOne(['uid' => $this->user->id, 'type' => UserAccount::TYPE_BORROW]);
        $recharge = new RechargeRecord();

        return $this->render('recharge', ['recharge' => $recharge, 'user_account' => $user_account, 'bank' => $bank]);
    }

    /**
     * 充值申请.
     */
    public function actionApply()
    {
        $bank_id = Yii::$app->request->post('bankid');
        $pay_type = Yii::$app->request->post('pay_type');

        if (empty($bank_id) || empty($pay_type)) {
            return $this->redirect('/user/recharge/recharge-err');
        }

        $user_account = UserAccount::findOne(['uid' => $this->user->id, 'type' => UserAccount::TYPE_BORROW]);
        $recharge = new RechargeRecord([
            'sn' => RechargeRecord::createSN(),
            'pay_type' => $pay_type,
            'pay_id' => 0,
            'account_id' => $user_account->id,
            'uid' => $this->user->id,
            'bank_id' => $bank_id, //网银充值,不要求绑卡,存储的是银行的统一ID
            'pay_bank_id' => $bank_id, //网银充值,不要求绑卡,存储的是银行的统一ID
            'status' => RechargeRecord::STATUS_NO,
        ]);

        if ($recharge->load(Yii::$app->request->post())) {
            //录入recharge_record记录
            if (!$recharge->save()) {
                return $this->redirect('/user/recharge/recharge-err');
            }

            $ump = Yii::$container->get('ump');

            $epayUser = EpayUser::findOne(['appUserId' => $this->user->id]);
            if (!$epayUser) {
                throw new \Exception('EpayUser record is null.');
            }
            $bank = EbankConfig::findOne(['bankId' => $bank_id]);

            $ump->OrgRechargeApply(
                $recharge, 'B2BBANK', $epayUser->epayUserId, $bank->bank->gateId
            );
        } else {
            return $this->redirect('/user/recharge/recharge-err');
        }
    }

    /**
     * 充值失败页面.
     */
    public function actionRechargeErr()
    {
        return $this->render('recharge_err');
    }

}
