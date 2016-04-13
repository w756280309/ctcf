<?php

namespace app\modules\order\controllers;

use Yii;
use app\controllers\BaseController;
use yii\web\Response;
use common\models\product\OnlineProduct;
use common\models\contract\ContractTemplate;
use common\models\order\OnlineOrder;
use common\service\PayService;
use common\core\UserAccountCore;
use common\core\OrderCore;

class OrderController extends BaseController
{
    /**
     * 认购页面.
     *
     * @param type $sn 标的编号
     *
     * @return page
     */
    public function actionIndex($sn)
    {
        $deal = OnlineProduct::findOne(['sn' => $sn]);
        if (empty($deal)) {
            throw new \yii\web\NotFoundHttpException('This production is not existed.');
        }
        $uacore = new UserAccountCore();
        $ua = $uacore->getUserAccount($this->getAuthedUser()->id);
        $param['order_balance'] = $deal->getLoanBalance(); //获取标的可投余额;
        $param['my_balance'] = $ua->available_balance; //用户账户余额;

        return $this->render('index', ['deal' => $deal, 'param' => $param]);
    }

    /**
     * 购买标的.
     *
     * @param type $sn
     *
     * @return type
     */
    public function actionDoorder($sn = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $money = \Yii::$app->request->post('money');
        $pay = new PayService(PayService::REQUEST_AJAX);
        $ret = $pay->checkAllowPay($this->getAuthedUser(), $sn, $money);
        if ($ret['code'] != PayService::ERROR_SUCCESS) {
            return $ret;
        }
        $ordercore = new OrderCore();

        return $ordercore->createOrder($sn, $money,  $this->getAuthedUser()->id);
    }

    public function actionOrdererror($osn = '')
    {
        $order = OnlineOrder::ensureOrder($osn);
        $deal = null;
        if (null  !== $order && 1 !== $order->status) {
            $deal = OnlineProduct::findOne($order->online_pid);
        }
        if (\Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['status' => $order->status];
        }

        return $this->render('error', ['order' => $order, 'deal' => $deal, 'ret' => (null  !== $order && 1 === $order->status) ? 'success' : 'fail']);
    }

    public function actionOrderwait($osn = '')
    {
        $order = OnlineOrder::ensureOrder($osn);
        if (OnlineOrder::STATUS_FALSE  !== $order->status) {
            return $this->redirect("/order/order/ordererror?osn=" . $order->sn);
        }
        return $this->render('wait', ['order' => $order]);
    }

    /**
     * 合同显示页面.
     *
     * @param type $sn
     * @param type $id
     * @param type $key
     *
     * @return type
     *
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionAgreement($id, $key = 0)
    {
        if (empty($id)) {
            throw new \yii\web\NotFoundHttpException('The argument err.');
        }

        $model = ContractTemplate::find()->where(['pid' => $id])->select('pid,name,content')->all();

        $deal_id = Yii::$app->request->get('deal_id');
        if (!empty($deal_id)) {
            $deal = OnlineOrder::findOne($deal_id);
            if (null === $deal) {
                $deal_id = null;   //对传入的参数做处理,当无效时,置为null,防止脚本等注入问题
            }
        } else {
            $deal = null;
        }

        $model[$key] = ContractTemplate::replaceTemplate($model[$key], $deal);

        return $this->render('agreement', ['model' => $model, 'key_f' => $key, 'content' => $model[$key]['content'], 'deal_id' => $deal_id]);
    }
}
