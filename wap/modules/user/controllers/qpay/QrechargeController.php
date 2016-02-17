<?php

namespace app\modules\user\controllers\qpay;

use app\controllers\BaseController;
use common\models\user\RechargeRecord;
use common\models\TradeLog;
use Yii;
use yii\base\Model;
use yii\web\Response;
use PayGate\Cfca\CfcaUtils;

class QrechargeController extends BaseController
{
    
    public function actionVerify()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $cpuser = $this->user;
        $ubank = $cpuser->qpay;
        if (empty($ubank)) {
            return $this->createErrorResponse('请先绑卡');
        }
        // 已验证的数据:无需验证
        $safe = [
            'uid' => $this->user->id,
            'account_id' => $cpuser->lendAccount->id,
            'bindingSn' => $ubank->binding_sn,
            'bank_id' => strval($ubank->id),
            'pay_type' => RechargeRecord::PAY_TYPE_QUICK,
        ];

        $rec_model = new RechargeRecord([
            'sn' => CfcaUtils::generateSn("RC"),
            'uid' => $safe['uid'],
            'account_id' => $safe['account_id'],
            'bank_id' => $safe['bank_id'],
            'pay_bank_id' => $safe['bank_id'],
            'pay_type' => $safe['pay_type'],
            'clientIp' => ip2long(Yii::$app->request->userIP),
            'epayUserId' => $this->user->epayUser->epayUserId,
        ]);
        if (
            $rec_model->load(Yii::$app->request->post())
            && $rec_model->validate()
        ) {
            //$rec_model->save();
            $rec_model->created_at = time();
            $next = Yii::$container->get('ump')->rechargeViaQpay($rec_model);
            if ($next->isRedirection()) {
                return ['next' => $next->getLocation()];
            } else {
                return $this->createErrorResponse('充值申请跳转失败');
            }            
        } else {
            return $this->createErrorResponse('充值申请失败');
        }
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
