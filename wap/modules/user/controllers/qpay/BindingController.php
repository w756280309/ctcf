<?php

namespace app\modules\user\controllers\qpay;

use app\controllers\BaseController;
use common\models\user\QpayBinding as QpayAcct;
use Yii;
use yii\base\Model;
use yii\web\Response;
use PayGate\Cfca\CfcaUtils;

class BindingController extends BaseController
{

    public function actionVerify()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // 已验证的数据
        $safe = [
            'realName' => $this->user->real_name,
            'idNo' => $this->user->idcard,
        ];

        $acct_model = new QpayAcct();
        $acct_model->uid = $this->user->id;
        $acct_model->card_number = $safe['idNo'];
        $acct_model->account = $safe['realName'];
        $acct_model->account_type = QpayAcct::PERSONAL_ACCOUNT;
        if (
            $acct_model->load(Yii::$app->request->post())
            && $acct_model->validate()
        ) {
            $acct_model->binding_sn = CfcaUtils::generateSn('B');
            $acct_model->epayUserId = $this->user->epayUser->epayUserId;
            QpayAcct::deleteAll(['uid' => $acct_model->uid, 'status' => 0]); //将之前的绑卡未处理的删掉
            $acct_model->save();
            $next = \Yii::$container->get('ump')->enableQpay($acct_model);//获取跳转页面
            return [
                'next' => $next,
            ]; 
        }

        return $this->createErrorResponse($acct_model);
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
