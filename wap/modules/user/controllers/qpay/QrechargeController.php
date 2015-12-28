<?php

namespace app\modules\user\controllers\qpay;

use app\controllers\BaseController;
use common\lib\cfca\Cfca;
use common\models\user\User;
use PayGate\Cfca\Message\Request1375;
use common\models\user\RechargeRecord;
//use PayGate\Cfca\Message\Request2532;
use PayGate\Cfca\Message\Response as CfcaResponse;
use XmlUtils\XmlUtils;
use Yii;
use yii\base\Model;
use yii\web\Response;

class QrechargeController extends BaseController
{
    public function actionInit()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $cpuser = $this->user;
        $ubank = $cpuser->bank;
        if($cpuser->status == User::STATUS_DELETED) {
            return $this->createErrorResponse('用户被禁止访问');
        }
        
        if(empty($ubank)){
            return $this->createErrorResponse('请先绑卡');
        }
        // 已验证的数据:无需验证
        $safe = [
            'uid' => $this->uid,
            'account_id' => $cpuser->accountInfo->id,
            'bindingSn' => $ubank->binding_sn,
            'bank_id' => strval($ubank->id)
        ];

        $rec_model = new RechargeRecord([
            'uid'=>$safe['uid'],
            'account_id'=>$safe['account_id'],
            'bank_id'=>$safe['bank_id'],
            'pay_bank_id'=>$safe['bank_id']
        ]);
        if (
            $rec_model->load(Yii::$app->request->post())
            && $rec_model->validate()
        ) {
            
            $req = new Request1375(
                Yii::$app->params['cfca']['institutionId'],
                $safe['bindingSn'],
                $rec_model->fund
            );
            $cfca = new Cfca();
            $resp = $cfca->request($req);
            if (false === $resp) {
                return $this->createErrorResponse('服务器异常');
            } elseif ($resp instanceof CfcaResponse && !$resp->isSuccess()) {
                return $this->createErrorResponse($resp->getMessage());
            } else {
                // 设置session。用来验证数据的不可修改
                Yii::$app->session->set('cfca_qpay_recharge', [
                        'recharge_sn' => $req->getRechargeSn(),
                        'recharge_fund' => $rec_model->fund,
                        '_time' => time(),
                    ]);
                return ['rechargeSn' => $req->getRechargeSn()];
            }
        }

        return $this->createErrorResponse($rec_model);
    }

    public function actionVerify()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

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
