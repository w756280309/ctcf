<?php

namespace api\modules\v1\controllers\notify;

use common\models\bank\BankCardManager;
use common\models\bank\BankCardUpdate;
use common\models\TradeLog;
use yii\web\Controller;

class UpdatecardController extends Controller
{
    /**
     * 换卡申请页面前台回调函数.
     */
    public function actionFrontend()
    {
        $data = \Yii::$app->request->get();
        $channel = $this->getChannel($data);
        $isSucc = false;

        TradeLog::initLog(2, $data, $data['sign'])->save();    //记录交易日志信息

        if (\Yii::$container->get('ump')->verifySign($data)
            && 'mer_bind_card_apply_notify' === $data['service']
            && '0000' === $data['ret_code']) {
            $model = BankCardUpdate::findOne(['sn' => $data['order_id']]);
            if (BankCardUpdate::STATUS_PENDING === $model->status) {
                $model->status = BankCardUpdate::STATUS_ACCEPT;
                $model->save();
            }

            $isSucc = true;
        }

        $backUrl = $this->getBackUrl($channel, $isSucc);

        return $this->redirect($backUrl);
    }

    /**
     * 换卡申请页面后台回调函数.
     */
    public function actionBackend()
    {
        $data = \Yii::$app->request->get();
        $channel = $this->getChannel($data);

        $this->layout = false;
        $err = '0000';
        $errmsg = 'no error';

        TradeLog::initLog(2, $data, $data['sign'])->save();  //记录交易日志

        if (\Yii::$container->get('ump')->verifySign($data)) {
            if ('mer_bind_card_apply_notify' === $data['service']) {    //换卡申请后台通知
                $isSucc = false;
                if ('0000' === $data['ret_code']) {
                    $model = BankCardUpdate::findOne(['sn' => $data['order_id']]);
                    if (BankCardUpdate::STATUS_PENDING === $model->status) {
                        $model->status = BankCardUpdate::STATUS_ACCEPT;
                        $model->save();
                    }

                    $isSucc = true;
                }

                $backUrl = $this->getBackUrl($channel, $isSucc);

                return $this->redirect($backUrl);
            } elseif ('mer_bind_card_notify' === $data['service']) {    //换卡结果后台通知
                $model = BankCardUpdate::findOne(['sn' => $data['order_id']]);
                if (null === $model) {
                    $err = '00009999';
                    $errmsg = '记录未找到';
                } else {
                    if ('0000' === $data['ret_code']) {
                        $model->status = BankCardUpdate::STATUS_SUCCESS;    //更新换卡申请表记录状态
                        if (!$model->save()) {
                            $err = '00009999';
                            $errmsg = '数据库更新失败';
                        } else {
                            $bankCardMgr = new BankCardManager();
                            try {
                                $newCard = $bankCardMgr->confirmUpdate($model); // 返回新卡的记录
                            } catch (\Exception $e) {
                                $err = '00009999';
                                $errmsg = $e.getMessage();
                            }
                        }
                    } else {
                        $model->status = BankCardUpdate::STATUS_FAIL;
                        if (!$model->save()) {
                            $err = '00009999';
                            $errmsg = '数据库更新失败';
                        }
                    }
                }
            } else {
                $err = '00009999';
                $errmsg = '接口名称错误';
            }
        } else {
            $err = '00009999';
            $errmsg = '验证签名失败';
        }

        $content = \Yii::$container->get('ump')->buildQuery([
            'order_id' => $data['order_id'],
            'mer_date' => $data['mer_date'],
            'reg_code' => $err,
        ]);

        return $this->render('@borrower/modules/user/views/recharge/recharge_notify.php', ['content' => $content]);
    }

    private function getChannel(&$data)
    {
        if (array_key_exists('channel', $data) && 'pc' === $data['channel']) {
            unset($data['channel']);

            return 'pc';
        }

        if (array_key_exists('token', $data) && $data['token']) {
            unset($data['token']);

            return 'app';
        }

        return 'wap';
    }

    private function getBackUrl($channel, $isSucc)
    {
        if ('pc' === $channel) {
            $host = \Yii::$app->params['clientOption']['host']['frontend'];
            if ($isSucc) {
                return $host.'info/success?source=huanka&jumpUrl=/user/userbank/mybankcard';
            }

            return $host.'info/fail?source=huanka';
        }

        if ('app' === $channel) {
            $host = \Yii::$app->params['clientOption']['host']['app'];
        } else {
            $host = \Yii::$app->params['clientOption']['host']['wap'];
        }

        if ($isSucc) {
            return $host.'user/userbank/updatecardnotify?ret=success';
        }

        return $host.'user/userbank/updatecardnotify';
    }
}
