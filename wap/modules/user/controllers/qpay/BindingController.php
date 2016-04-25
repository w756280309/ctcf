<?php

namespace app\modules\user\controllers\qpay;

use app\controllers\BaseController;
use common\models\user\QpayBinding as QpayAcct;
use common\utils\TxUtils;
use Yii;
use yii\base\Model;
use yii\web\Response;
use common\models\bank\BankManager;
use common\models\bank\QpayConfig;

class BindingController extends BaseController
{
    public function actionVerify()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // 已验证的数据
        $safe = [
            'realName' => $this->getAuthedUser()->real_name,
            'idNo' => $this->getAuthedUser()->idcard,
        ];

        $acct_model = new QpayAcct();
        $acct_model->uid = $this->getAuthedUser()->id;
        $acct_model->account = $safe['realName'];
        $acct_model->account_type = QpayAcct::PERSONAL_ACCOUNT;

        if (
            $acct_model->load(Yii::$app->request->post())
            && $acct_model->validate()
        ) {
            try {
                //对于绑卡时候如果没有找到要过滤掉异常
                $bin = BankManager::getBankFromCardNo($acct_model->card_number);
                if (!BankManager::isDebitCard($bin)) {
                    return $this->createErrorResponse('该操作只支持借记卡');
                }
                if ((int) $bin->bankId !== (int) $acct_model->bank_id) {
                    return $this->createErrorResponse('请选择银行卡对应的银行');
                }
            } catch (\Exception $ex) {
            }

            $qpay = QpayConfig::findOne($acct_model->bank_id);
            if (null === $qpay || 1 === (int) $qpay->isDisabled) {
                return $this->createErrorResponse('抱歉不支持当前选择的银行');
            }
            $acct_model->binding_sn = TxUtils::generateSn('B');
            $acct_model->epayUserId = $this->getAuthedUser()->epayUser->epayUserId;
            //QpayAcct::deleteAll(['uid' => $acct_model->uid, 'status' => 0]); //将之前的绑卡未处理的删掉
            $acct_model->save();
            $next = \Yii::$container->get('ump')->enableQpay($acct_model);//获取跳转页面
            return [
                'next' => $next,
            ];
        }

        return $this->createErrorResponse($acct_model);
    }

    public function actionUmpmianmi()
    {
        return $this->redirect(Yii::$container->get('ump')->openmianmi($this->getAuthedUser()->epayUser->epayUserId));
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
