<?php
/**
 * Created by ShiYang.
 * Date: 17-1-9
 * Time: 上午11:06
 */

namespace common\action\user;


use common\models\bank\BankCardUpdate;
use common\models\bank\BankManager;
use common\models\bank\QpayConfig;
use common\utils\TxUtils;
use Yii;
use yii\base\Action;

//换卡表单提交页面
class BankUpdateVerifyAction extends Action
{
    public function run()
    {
        $user = $this->controller->getAuthedUser();
        $bank = $user->qpay;

        $model = new BankCardUpdate([
            'sn' => TxUtils::generateSn('BU'),
            'oldSn' => $bank->binding_sn,
            'uid' => $user->id,
            'epayUserId' => $user->epayUser->epayUserId,
            'cardHolder' => $user->real_name,
            'status' => BankCardUpdate::STATUS_PENDING,
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($bank->card_number === $model->cardNo) {
                return $this->controller->createErrorResponse('您已绑定该银行卡');
            }

            //对于换卡时候如果没有找到要过滤掉异常
            try {
                $bin = BankManager::getBankFromCardNo($model->cardNo);
                if (!BankManager::isDebitCard($bin)) {
                    return $this->controller->createErrorResponse('该操作只支持借记卡');
                }
                if ((int)$bin->bankId !== (int)$model->bankId) {
                    return $this->controller->createErrorResponse('请选择银行卡对应的银行');
                }
            } catch (\Exception $ex) {
            }

            $qpay = QpayConfig::findOne($model->bankId);
            if (null === $qpay || 0 === (int)$qpay->allowBind) {
                return $this->controller->createErrorResponse('抱歉，不支持当前选择的银行');
            }

            if ($model->save(false)) {
                $next = \Yii::$container->get('ump')->changeQpay($model, CLIENT_TYPE, Yii::$app->request->get('token'));
                return ['next' => $next];
            }
        }

        return $this->controller->createErrorResponse($model);
    }
}