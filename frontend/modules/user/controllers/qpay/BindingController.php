<?php

namespace frontend\modules\user\controllers\qpay;

use common\models\bank\BankManager;
use common\models\bank\QpayConfig;
use common\models\user\QpayBinding as QpayAcct;
use common\utils\TxUtils;
use frontend\controllers\BaseController;
use Yii;
use yii\base\Model;
use yii\filters\AccessControl;

class BindingController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 绑卡申请处理.
     */
    public function actionVerify()
    {
        // 已验证的数据
        $safe = [
            'realName' => $this->getAuthedUser()->real_name,
            'idNo' => $this->getAuthedUser()->idcard,
        ];

        $acct_model = new QpayAcct();
        $acct_model->uid = $this->getAuthedUser()->id;
        $acct_model->account = $safe['realName'];
        $acct_model->account_type = QpayAcct::PERSONAL_ACCOUNT;

        if ($acct_model->load(Yii::$app->request->post()) && $acct_model->validate()) {
            try {
                //对于绑卡时候如果没有找到要过滤掉异常
                $bind = QpayAcct::findOne(['card_number' => $acct_model->card_number, 'status' => QpayAcct::STATUS_SUCCESS]);
                if ($bind) {
                    return $this->createErrorResponse('卡号已被占用，请换一张卡片重试');
                }

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
            $acct_model->save();
            $next = Yii::$container->get('ump')->enableQpay($acct_model);//获取跳转页面
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

        return ['message' => $message];
    }
}
