<?php

namespace frontend\modules\user\controllers\qpay;

use common\models\bank\BankManager;
use common\models\user\RechargeRecord;
use common\models\user\UserFreepwdRecord;
use common\utils\TxUtils;
use frontend\controllers\BaseController;
use Yii;
use yii\base\Model;

class QrechargeController extends BaseController
{
    public function actionVerify()
    {
        $cpuser = $this->getAuthedUser();
        $ubank = $cpuser->qpay;
        if (empty($ubank)) {
            return $this->createErrorResponse('请先绑卡');
        }
        /**
         * 验证快捷支付（商业委托）是否开通
        */
        $depute = Yii::$app->request->post('depute');
        if('depute' === $depute){
            $userfree = UserFreepwdRecord::findOne(['uid'=> $cpuser->id, 'status' => UserFreepwdRecord::OPEN_FASTPAY_STATUS_PASS]);
            if(empty($userfree)){
                return ['next' => '/user/userbank/recharge-depute'];
            }
        }
        // 已验证的数据:无需验证
        $safe = [
            'uid' => $this->getAuthedUser()->id,
            'account_id' => $cpuser->lendAccount->id,
            'bindingSn' => $ubank->binding_sn,
            'bank_id' => strval($ubank->id),
            'pay_type' => RechargeRecord::PAY_TYPE_QUICK,
        ];

        $rec_model = new RechargeRecord([
            'sn' => TxUtils::generateSn('RC'),
            'uid' => $safe['uid'],
            'account_id' => $safe['account_id'],
            'bank_id' => $safe['bank_id'],
            'pay_bank_id' => $safe['bank_id'],
            'pay_type' => $safe['pay_type'],
            'clientIp' => ip2long(Yii::$app->request->userIP),
            'epayUserId' => $this->getAuthedUser()->epayUser->epayUserId,
        ]);
        if (
            $rec_model->load(Yii::$app->request->post())
            && $rec_model->validate()
        ) {
            try {
                BankManager::verifyQpayLimit($ubank, $rec_model->fund);
            } catch (\Exception $ex) {
                return $this->createErrorResponse($ex->getMessage());
            }
            if (!$rec_model->hasErrors()) {
                if (!$rec_model->save(false)) {
                    throw new \Exception('Insert recharge record err.');
                }
                if('depute' !== $depute){
                    $next = Yii::$container->get('ump')->rechargeViaQpay($rec_model, 'pc');
                }else {
                    $next = Yii::$container->get('ump')->doUserFastRecharge($rec_model, 'pc');
                }
                
                if ($next->isRedirection()) {
                    return ['next' => $next->getLocation()];
                } else {
                    return $this->createErrorResponse('充值申请跳转失败');
                }
            }
        }

        return $this->createErrorResponse($rec_model->getSingleError()['message']);
    }
}
