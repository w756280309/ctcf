<?php

namespace app\modules\order\controllers;

use common\models\order\EbaoQuan;
use common\models\product\RateSteps;
use EBaoQuan\Client;
use Yii;
use app\controllers\BaseController;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Response;
use common\models\product\OnlineProduct;
use common\models\contract\ContractTemplate;
use common\models\order\OnlineOrder;
use common\service\PayService;
use common\models\order\OrderManager;

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
        if (empty($sn)) {
            throw new \yii\web\NotFoundHttpException();
        }

        $deal = OnlineProduct::findOne(['sn' => $sn]);
        if (null === $deal) {
            throw new \yii\web\NotFoundHttpException('This production is not existed.');
        }

        $ua = $this->getAuthedUser()->lendAccount;    //获取用户的账户信息
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
    public function actionDoorder($sn)
    {
        if (empty($sn)) {
            throw new \yii\web\NotFoundHttpException();   //判断参数无效时,抛404异常
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $money = \Yii::$app->request->post('money');
        $pay = new PayService(PayService::REQUEST_AJAX);
        $ret = $pay->checkAllowPay($this->getAuthedUser(), $sn, $money);
        if ($ret['code'] != PayService::ERROR_SUCCESS) {
            return $ret;
        }
        $orderManager = new OrderManager();

        return $orderManager->createOrder($sn, $money,  $this->getAuthedUser()->id);
    }

    /**
     * 认购标的结果页
     */
    public function actionOrdererror($osn)
    {
        if (empty($osn)) {
            throw new \yii\web\NotFoundHttpException();   //判断参数无效时,抛404异常
        }

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

    /**
     * 认购标的中间处理页
     */
    public function actionOrderwait($osn)
    {
        if (empty($osn)) {
            throw new \yii\web\NotFoundHttpException();   //判断参数无效时,抛404异常
        }

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
            throw new \yii\web\NotFoundHttpException();
        }

        $model = ContractTemplate::find()->where(['pid' => $id])->select('pid,name,content')->all();
        if (empty($model)) {
            throw new \yii\web\NotFoundHttpException();  //当对象为空时,抛出异常
        }

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

        //获取证书
        $baoQuan = EbaoQuan::find()->where(['type' => $key, 'orderId' => $deal_id, 'uid' => Yii::$app->user->identity->getId()])->one();
        $linkUrl = $downUrl = null;
        if (null !== $baoQuan) {
            $client = new Client();
            $downUrl = $client->contractFileDownload($baoQuan);
            //查看证书地址
            $linkUrl = $client->certificateLinkGet($baoQuan);
        }

        return $this->render('agreement', [
            'model' => $model,
            'key_f' => $key,
            'content' => $model[$key]['content'],
            'deal_id' => $deal_id,
            'linkUrl' => $linkUrl,
            'downUrl' => $downUrl,
        ]);
    }

    /*public function actionBaoQuan($deal_id, $type = 1)
    {
        $baoQuan = EbaoQuan::find()->where(['type' => $type, 'orderId' => $deal_id, 'uid' => Yii::$app->user->identity->getId()])->one();
        $data = [];
        if (null !== $baoQuan) {
            $data = ArrayHelper::toArray($baoQuan);
            $res = (new Client())->contractFileDownload($baoQuan);
            if ($res->success) {
                $data['downUrl'] = $res->downUrl;
            }
            //查看证书地址
            $res = (new Client())->certificateLinkGet($baoQuan);
            if ($res->success) {
                $data['link'] = $res->link;
            }
        }
        return $this->render('bao-quan', [
            'baoQuan' => $data,
        ]);
    }*/

    /**
     * 根据投资金额和产品利率阶梯获取订单的利率
     * @return array
     */
    public function actionRate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isPost) {
            $sn = Html::encode(Yii::$app->request->post('sn'));
            $amount = Html::encode(Yii::$app->request->post('amount'));
            $product = OnlineProduct::find()->where(['sn' => $sn])->one();
            if ($product && $amount) {
                if (1 === $product->isFlexRate && !empty($product->rateSteps)) {
                    $config = RateSteps::parse($product->rateSteps);
                    if (!empty($config)) {
                        $rate = RateSteps::getRateForAmount($config, $amount);
                        if (false !== $rate) {
                            return ['res' => true, 'rate' => $rate / 100];
                        }
                    }
                }
            }
            return ['res' => false, 'rate' => false];
        }
        return ['res' => false, 'rate' => false];
    }
}
