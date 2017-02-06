<?php
/**
 * Created by ShiYang.
 * User: yang
 * Date: 17-1-6
 * Time: 下午2:39
 */

namespace common\action\user;


use common\models\bank\BankManager;
use common\models\bank\QpayConfig;
use common\models\user\QpayBinding;
use common\utils\TxUtils;
use yii\base\Action;
use Yii;

//绑卡表单提交公共action
class BankVerifyAction extends Action
{
    public function run()
    {
        $user = $this->controller->getAuthedUser();
        // 已验证的数据
        $safe = [
            'realName' => $user->real_name,
            'idNo' => $user->idcard,
        ];

        $acct_model = new QpayBinding();
        $acct_model->uid = $user->id;
        $acct_model->account = $safe['realName'];
        $acct_model->account_type = QpayBinding::PERSONAL_ACCOUNT;

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
            $acct_model->epayUserId = $user->epayUser->epayUserId;
            $acct_model->save();
            $next = \Yii::$container->get('ump')->enableQpay($acct_model, CLIENT_TYPE);//获取跳转页面
            return [
                'next' => $next,
            ];
        }

        return $this->createErrorResponse($acct_model);
    }
}