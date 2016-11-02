<?php

namespace app\modules\order\controllers;

use app\controllers\BaseController;
use common\controllers\ContractTrait;
use common\models\order\BaoQuanQueue;
use common\models\user\User;
use common\utils\StringUtils;
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
use Tx\TxClient;
use Yii;
use yii\helpers\Html;

class OrderController extends BaseController
{
    use ContractTrait;
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

        $coupon = UserCoupon::find()
            ->innerJoin($ct, "couponType_id = $ct.id")
            ->where(['isUsed' => 0, 'order_id' => null, 'coupon_type.isDisabled' => 0, 'user_id' => $this->getAuthedUser()->id])
            ->andFilterWhere(['>=', 'expiryDate', date('Y-m-d')]);

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
            throw $this->ex404();   //判断参数无效时,抛404异常
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
        //记录订单来源
        $investFrom = OnlineOrder::INVEST_FROM_WAP;
        if (defined('IN_APP') && IN_APP) {
            $investFrom = OnlineOrder::INVEST_FROM_APP;
        }
        if ($this->fromWx()) {
            $investFrom  = OnlineOrder::INVEST_FROM_WX;
        }
        return $orderManager->createOrder($sn, $money,  $this->getAuthedUser()->id, $coupon, $investFrom);
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
     * 查看用户合同
     * @param $asset_id
     * @return mixed
     */
    public function actionContract($asset_id, $key = 0)
    {
        $txClient = \Yii::$container->get('txClient');
        $asset = $txClient->get('assets/detail', ['id' => $asset_id, 'validate' => false]);
        if ($asset['user_id'] !== $this->getAuthedUser()->id) {
            throw new \Exception('不能查看他人的合同');
        }
        $contracts = $this->getUserContract($asset);
        $bqLoan = $contracts['bqLoan'];
        $contracts = array_merge($contracts['loanContract'], $contracts['creditContract']);
        $key = isset($contracts[$key]) ? $key : 0;
        if ($contracts[$key]['type'] === 'loan' && !empty($bqLoan)) {
            $bq = $bqLoan;
        } elseif (in_array($contracts[$key]['type'], ['credit_note', 'credit_order']) && !empty($contracts[$key]['bqCredit'])) {
            $bq = $contracts[$key]['bqCredit'];
        } else {
            $bq = [];
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderFile('@wap/modules/order/views/order/_contract.php', ['content' => $contracts[$key]['content'], 'bq' => $bq]);
        }

        return $this->render('contract', [
            'contracts' => $contracts,
            'fk' => $key,
            'content' => $contracts[$key]['content'],
            'asset_id' => $asset_id,
            'bq' => $bq,
        ]);
    }

    /**
     * 合同显示页面.
     */
    public function actionAgreement($id, $note_id = 0, $key = 0)
    {
        $contracts = $this->getContractTemplate($id, $note_id);
        $key = isset($contracts[$key]) ? $key : 0;
        $content = $contracts[$key]['content'];

        if (Yii::$app->request->isAjax) {
            return $this->renderFile('@wap/modules/order/views/order/_contract.php', ['content' => $content]);
        }

        return $this->render('contract', [
            'contracts' => $contracts,
            'content' => $content,
            'fk' => $key,
            'note_id' => $note_id,
            'id' => $id,
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
