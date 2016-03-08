<?php

namespace app\modules\user\controllers\qpay;

use Yii;
use Exception;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use common\models\user\DrawRecord;
use common\models\draw\DrawManager;
use common\models\TradeLog;

/**
 * 提现申请回调控制器4.2.
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class DrawnotifyController extends Controller
{
    /**
     * 提现申请前台通知地址
     */
    public function actionFrontend()
    {
        $data = Yii::$app->request->get();
        $channel =  null;
        if (array_key_exists('channel', $data)) {
            $channel = $data['channel'];
            unset($data['channel']);
        }
        $ret = $this->processing($data);
        if ($ret instanceof DrawRecord) {
            if ('pc' === $channel) {
                return $this->redirect(\Yii::$app->params['ump']['notify']['draw_pc_ret_url'].'?flag=succ');
            }

            return $this->redirect('/user/userbank/drawres?ret=success');
        } else {
            if ('pc' === $channel) {
                return $this->redirect(\Yii::$app->params['ump']['notify']['draw_pc_ret_url'].'?flag=err');
            }

            return $this->redirect('/user/userbank/drawres');
        }
    }

    /**
     * 提现申请后台通知地址
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

    /**
     * @param array $data
     *
     * @return type
     *
     * @throws NotFoundHttpException
     * @throws Exception
     */
    private function processing(array $data = [])   //没有做防重复处理
    {
        TradeLog::initLog(2, $data, $data['sign'])->save();
        if (
            Yii::$container->get('ump')->verifySign($data)
            && '0000' === $data['ret_code']
            && 'withdraw_apply_notify' === $data['service']
        ) {
            $draw = DrawRecord::findOne(['sn' => $data['order_id']]);
            if (DrawRecord::STATUS_ZERO === (int) $draw->status) {
                return DrawManager::ackDraw($draw);
            } else {
                throw new Exception($data['order_id'].'状态异常');
            }
        } else {
            throw new Exception($data['order_id'].'处理失败');
        }
    }

    /**
     * @param array $data
     */
    public function apply()
    {
        $data = Yii::$app->request->get();
    }
}
