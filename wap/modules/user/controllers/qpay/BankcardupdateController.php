<?php
namespace app\modules\user\controllers\qpay;

use app\controllers\BaseController;
use common\models\bank\BankCardUpdate;
use common\models\bank\BankManager;
use common\models\bank\QpayConfig;
use common\utils\TxUtils;
use Yii;

class BankcardupdateController extends BaseController
{
    /**
     * 处理换卡申请请求
     */
    public function actionInit()
    {
        $user = $this->getAuthedUser();
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
                return $this->createErrorResponse('您已绑定该银行卡');
            }

            //对于换卡时候如果没有找到要过滤掉异常
            try {
                $bin = BankManager::getBankFromCardNo($model->cardNo);
                if (!BankManager::isDebitCard($bin)) {
                    return $this->createErrorResponse('该操作只支持借记卡');
                }
                if ((int) $bin->bankId !== (int) $model->bankId) {
                    return $this->createErrorResponse('请选择银行卡对应的银行');
                }
            } catch (\Exception $ex) {}

            $qpay = QpayConfig::findOne($model->bankId);
            if (null === $qpay || 1 === (int) $qpay->isDisabled) {
                return $this->createErrorResponse('抱歉，不支持当前选择的银行');
            }

            if ($model->save(false)) {
                $next = \Yii::$container->get('ump')->changeQpay($model);
                return ['next' => $next];
            }
        }

        return $this->createErrorResponse($model);
    }

    /**
     * 错误信息返回函数
     */
    private function createErrorResponse($modelOrMessage)
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
