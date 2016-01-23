<?php

namespace app\modules\user\controllers\qpay;

use app\controllers\BaseController;
use common\lib\cfca\Cfca;
use common\models\user\User;
use common\models\user\UserBanks as QpayAcct;
use PayGate\Cfca\Account\IndividualAccount;
use PayGate\Cfca\Identity\IndividualIdentity;
use PayGate\Cfca\Message\Request2531;
use PayGate\Cfca\Message\Request2532;
use PayGate\Cfca\Message\Response as CfcaResponse;
use XmlUtils\XmlUtils;
use Yii;
use yii\base\Model;
use yii\web\Response;

class BindingController extends BaseController
{
    public function actionInit()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // 已验证的数据
        $safe = [
            'realName' => $this->user->real_name,
            'idNo' => $this->user->idcard,
        ];

        $acct_model = new QpayAcct();
        $acct_model->scenario = 'step_first';
        $acct_model->uid = $this->user->id;
        $acct_model->card_number = $safe['idNo'];
        $acct_model->account = $safe['realName'];
        $acct_model->account_type = QpayAcct::PERSONAL_ACCOUNT;
        if (
            $acct_model->load(Yii::$app->request->post())
            && $acct_model->validate()
        ) {
            $id = new IndividualIdentity(
                $acct_model->account,
                IndividualIdentity::ID_TYPE_RESIDENT,
                $acct_model->card_number,
                $acct_model->mobile
            );

            $acct = new IndividualAccount(
                $acct_model->bank_id,
                IndividualAccount::ACCT_TYPE_DEBIT,
                $acct_model->card_number
            );

            $req = new Request2531(
                Yii::$app->params['cfca']['institutionId'],
                $id,
                $acct
            );

            $cfca = new Cfca();
            $resp = $cfca->request($req);
            if (false === $resp) {
                return $this->createErrorResponse('服务器异常');
            } elseif ($resp instanceof CfcaResponse && !$resp->isSuccess()) {
                return $this->createErrorResponse($resp->getMessage());
            } else {
                // 调用
                Yii::$app->session->set('cfca_qpay_binding', [
                    'bindingSn' => $req->getBindingSn(),
                    'realName' => $id->getRealName(),
                    'idNo' => $id->getIdNo(),
                    'bankId' => $acct->getBankId(),
                    'acctNo' => $acct->getAcctNo(),
                    'mobile' => $id->getMobile(),
                    '_time' => time(),
                ]);

                return ['bindingSn' => $req->getBindingSn()];
            }
        }

        return $this->createErrorResponse($acct_model);
    }

    public function actionVerify()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // 已验证的数据
        $safe = [
            'realName' => $this->user->real_name,
            'idNo' => $this->user->idcard,
        ];

        $acct_model = new QpayAcct();
        $acct_model->scenario = 'step_first';
        $acct_model->uid = $this->user->id;
        $acct_model->card_number = $safe['idNo'];
        $acct_model->account = $safe['realName'];
        $acct_model->account_type = QpayAcct::PERSONAL_ACCOUNT;
        if (
            $acct_model->load(Yii::$app->request->post())
            && $acct_model->validate()
        ) {
            $pending = Yii::$app->session->get('cfca_qpay_binding');
            if (
                null === $pending
                || empty($acct_model->binding_sn)
            ) {
                return $this->createErrorResponse('请先请求短信验证码');
            }

            if (
                $acct_model->binding_sn !== $pending['bindingSn']
                || $acct_model->account !== $pending['realName']
                || $acct_model->bank_id !== $pending['bankId']
                || $acct_model->card_number !== $pending['acctNo']
                || $acct_model->mobile !== $pending['mobile']
            ) {
                return $this->createErrorResponse('绑卡信息已修改，请重新请求短信验证码');
            }

            $cfca = new Cfca();
            $resp = $cfca->request(new Request2532(
                Yii::$app->params['cfca']['institutionId'],
                $pending['bindingSn'],
                $acct_model->sms
            ));

            // XXX
            $xmlDoc = new \DOMDocument();
            $xmlDoc->loadXML($resp->getText());

            if (false === $resp) {
                return $this->createErrorResponse('短信发送失败');
            }

            if ('30' !== XmlUtils::getSingleValue($xmlDoc, '/Response/Body/Status')) {
                return $this->createErrorResponse($resp->getMessage());
            }

            if ($acct_model->save()) {
                \Yii::$app->session->remove('cfca_qpay_binding');//增加销毁
                return [
                    'next' => '/user/userbank/addbuspass',
                ];
            } else {
                return $this->createErrorResponse('绑卡失败');
            }
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
