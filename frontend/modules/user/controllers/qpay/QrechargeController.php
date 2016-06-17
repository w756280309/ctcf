<?php

namespace frontend\modules\user\controllers\qpay;

use common\utils\TxUtils;
use Yii;
use yii\base\Model;
use yii\web\Response;
use frontend\controllers\BaseController;
use common\models\user\RechargeRecord;
use common\models\bank\BankManager;

class QrechargeController extends BaseController
{
    public function actionVerify()
    {
        $cpuser = $this->getAuthedUser();
        $ubank = $cpuser->qpay;
        if (empty($ubank)) {
            return $this->createErrorResponse('请先绑卡');
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
                $next = Yii::$container->get('ump')->rechargeViaQpay($rec_model);
                if ($next->isRedirection()) {
                    return ['next' => $next->getLocation()];
                } else {
                    return $this->createErrorResponse('充值申请跳转失败');
                }
            }
        }

        return $this->createErrorResponse('充值申请失败:'.$rec_model->getSingleError()['message']);
    }

    private function createErrorResponse($modelOrMessage = null)
    {
        Yii::$app->response->statusCode = 400;
        $message = null;

        if (is_string($modelOrMessage)) {
            $message = $modelOrMessage;
        } elseif (
            $modelOrMessage instanceof Model
            && $modelOrMessage->hasErrors()
        ) {
            $message = current($modelOrMessage->getFirstErrors());
        }

        return [
            'message' => $message,
        ];
    }
}
