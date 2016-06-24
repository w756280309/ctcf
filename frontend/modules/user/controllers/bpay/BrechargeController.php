<?php

namespace frontend\modules\user\controllers\bpay;

use frontend\controllers\BaseController;
use Yii;
use common\models\user\RechargeRecord;
use common\models\TradeLog;

class BrechargeController extends BaseController
{
    /**
     * 融资用户充值前台通知接口
     */
    public function actionFrontendNotify()
    {
        $data = Yii::$app->request->get();
        TradeLog::initLog(2, $data, $data['sign'])->save();
        if (empty($data)) {
            throw new \Exception('The request info is null.');
        }

        $ump = Yii::$container->get('ump');

        if ($ump->verifySign($data)
            && '0000' === $data['ret_code']
            && 'recharge_notify' === $data['service']) {
            $recharge = RechargeRecord::findOne(['sn' => $data['order_id']]);

            if (!$recharge) {
                throw new \Exception('The recharge info is null.');
            }

            $accService = Yii::$container->get('account_service');

            if ($accService->confirmRecharge($recharge)) {
                return $this->redirect('/info/success?source=chongzhi&jumpUrl=/user/user/index');
            }
        }

        return $this->redirect('/info/fail?source=chongzhi&jumpUrl=/user/recharge/init');
    }

    /**
     * 融资用户充值后台通知接口
     */
    public function actionBackendNotify()
    {
        $data = Yii::$app->request->get();
        TradeLog::initLog(2, $data, $data['sign'])->save();
        $ump = Yii::$container->get('ump');
        $err = '0000';
        $errMess = 'No err';

        if ($ump->verifySign($data)
            && '0000' === $data['ret_code']
            && 'recharge_notify' === $data['service']) {
            $recharge = RechargeRecord::findOne(['sn' => $data['order_id']]);

            if (!recharge) {
                $err = '00009999';
                $errMess = '充值记录没有找到';
            } else {
                $accService = Yii::$container->get('account_service');

                if (!$accService->confirmRecharge($recharge)) {
                    $err = '00009999';
                    $errMess = '更新充值记录相关表数据错误';
                }
            }
        } else {
            $err = '00009999';
            $errMess = '请求数据错误';
        }

        Yii::trace($errMess.$data['service'].":".http_build_query($data), 'umplog');
        $content = $ump->buildQuery([
            'order_id' => $data['order_id'],
            'mer_date' => $data['mer_date'],
            'reg_code' => $err,
        ]);

        $this->layout = false;
        return $this->render('@borrower/modules/user/views/recharge/recharge_notify.php', ['content' => $content]);
    }
}
