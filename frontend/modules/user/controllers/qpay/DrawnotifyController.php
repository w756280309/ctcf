<?php

namespace frontend\modules\user\controllers\qpay;

use frontend\controllers\BaseController;
use Yii;
use Exception;
use common\models\TradeLog;
use common\models\user\DrawRecord;
use common\models\draw\DrawManager;
use common\models\user\UserAccount;

/**
 * 借款人提现回调
 */
class DrawnotifyController extends BaseController
{
    /**
     * 前台通知地址
     */
    public function actionFrontend()
    {
        $data = Yii::$app->request->get();
        $channel = null;
        $app_token = "";
        $website = "wap";
        if (array_key_exists('channel', $data)) {
            $channel = $data['channel'];
            unset($data['channel']);
        }
        if (array_key_exists('token', $data)) {
            $app_token = "&token=" . $data['token'];
            $website = "wap";
            unset($data['token']);
        }
        $ret = $this->processing($data);

        if ($ret instanceof DrawRecord) {
            if (Yii::$app->session->has('tx_ur')) {
                $url = Yii::$app->session->get('tx_url');
                Yii::$app->session->remove('tx_url');
            } else {
                $url = '/user/user/index';
            }
            if ('pc' === $channel) {
                return $this->redirect(Yii::$app->params['clientOption']['host']['frontend'].'info/success?source=tixian&jumpUrl=' . $url);
            }

            return $this->redirect(Yii::$app->params['clientOption']['host'][$website].'user/userbank/drawres?ret=success' . $app_token);
        } else {
            if ('pc' === $channel) {
                return $this->redirect(Yii::$app->params['clientOption']['host']['frontend'].'info/fail?source=tixian&jumpUrl');
            }

            return $this->redirect(Yii::$app->params['clientOption']['host'][$website].'user/userbank/drawres' . $app_token);
        }
    }

    /**
     * 后台通知地址
     */
    public function actionBackend()
    {
        $this->layout = false;
        $err = '00009999';
        $errmsg = 'no error';
        $data = Yii::$app->request->get();

        $ret = $this->processing($data);
        if ($ret instanceof DrawRecord) {
            $err = '0000';
        } else {
            $errmsg = '非DrawRecord对象;';
        }

        $content = Yii::$container->get('ump')->buildQuery([
            'order_id' => $data['order_id'],
            'mer_date' => $data['mer_date'],
            'reg_code' => $err,
        ]);

        return $this->render('@borrower/modules/user/views/recharge/recharge_notify.php', ['content' => $content]);
    }

    private  function processing($data)
    {
        TradeLog::initLog(2, $data, $data['sign'])->save();
        if (array_key_exists('token', $data)) {
            unset($data['token']);
        }
        if (
            Yii::$container->get('ump')->verifySign($data)
            && '0000' === $data['ret_code']
            && 'withdraw_apply_notify' === $data['service']
        ) {
            $draw = DrawRecord::findOne(['sn' => $data['order_id']]);
            if (DrawRecord::STATUS_ZERO === (int) $draw->status) {
                return DrawManager::ackDraw($draw, UserAccount::TYPE_BORROW);
            } else {
                throw new Exception($data['order_id'].'状态异常');
            }
        } else {
            throw new Exception($data['order_id'].'处理失败');
        }

    }
}