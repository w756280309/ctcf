<?php

namespace app\modules\order\controllers;

use app\controllers\BaseController;
use EBaoQuan\Client;
use common\models\coupon\CouponType;
use common\models\contract\ContractTemplate;
use common\models\coupon\UserCoupon;
use common\models\order\OnlineOrder;
use common\models\order\OrderManager;
use common\models\order\EbaoQuan;
use common\models\product\OnlineProduct;
use common\models\product\RateSteps;
use common\service\PayService;
use Yii;
use yii\helpers\Html;

class OrderController extends BaseController
{
    /**
     * 认购页面.
     */
    public function actionIndex()
    {
        $request = array_replace([
                'sn' => null,
                'money' => null,
                'couponId' => null,
            ], Yii::$app->request->get());

        if (empty($request['sn']) || !preg_match('/^[A-Za-z0-9]+$/', $request['sn'])) {
            throw $this->ex404();
        }

        if (!empty($request['money']) && !preg_match('/^[0-9|.]+$/', $request['money'])) {
            throw $this->ex404();
        }

        if (!empty($request['couponId']) && !preg_match('/^[0-9]+$/', $request['couponId'])) {
            throw $this->ex404();
        }

        $deal = $this->findOr404(OnlineProduct::class, ['sn' => $request['sn']]);

        $user = $this->getAuthedUser();
        $ua = $user->lendAccount;    //获取用户的账户信息
        $param['order_balance'] = $deal->getLoanBalance(); //获取标的可投余额;
        $param['my_balance'] = $ua->available_balance; //用户账户余额;

        $ct = CouponType::tableName();
        $uc = UserCoupon::tableName();

        $coupon = (new \yii\db\Query())    //获取有效的代金券信息
            ->select("$ct.*, $uc.user_id, $uc.order_id, $uc.isUsed, $uc.id as uid, $uc.expiryDate expiryDate")
            ->from($ct)
            ->innerJoin($uc, "$ct.id = $uc.couponType_id")
            ->where(['isUsed' => 0, 'order_id' => null, 'isDisabled' => 0])
            ->andFilterWhere(['>=', 'expiryDate', date('Y-m-d')])
            ->andWhere(['user_id' => $user->id]);

        if (!empty($request['couponId'])) {
            $coupon->andWhere(["$uc.id" => $request['couponId']]);
        }

        return $this->render('index', [
                'deal' => $deal,
                'param' => $param,
                'coupon' => $coupon->one(),
                'money' => $request['money'],
                'couponId' => $request['couponId'],
            ]);
    }

    /**
     * 购买标的.
     */
    public function actionDoorder($sn)
    {
        if (empty($sn)) {
            throw new \yii\web\NotFoundHttpException();   //判断参数无效时,抛404异常
        }

        $money = \Yii::$app->request->post('money');
        $coupon_id = \Yii::$app->request->post('couponId');
        $coupon = null;
        if ($coupon_id) {
            $coupon = UserCoupon::findOne($coupon_id);
            if (null === $coupon) {
                return ['code' => 1,  'message' => '无效的代金券'];
            }
        }

        $pay = new PayService(PayService::REQUEST_AJAX);
        $ret = $pay->checkAllowPay($this->getAuthedUser(), $sn, $money, $coupon);
        if ($ret['code'] != PayService::ERROR_SUCCESS) {
            return $ret;
        }
        $orderManager = new OrderManager();

        return $orderManager->createOrder($sn, $money,  $this->getAuthedUser()->id, $coupon);
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
        // 统计转化，取消直接跳转
        /*if (OnlineOrder::STATUS_FALSE  !== $order->status) {
            return $this->redirect("/order/order/ordererror?osn=" . $order->sn);
        }*/
        return $this->render('wait', ['order' => $order]);
    }

    /**
     * 合同显示页面.
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
        $baoQuan = EbaoQuan::find()->where(['orderId' => $deal_id, 'uid' => Yii::$app->user->identity->getId()])->one();
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
