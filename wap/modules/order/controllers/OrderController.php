<?php

namespace app\modules\order\controllers;

use app\controllers\BaseController;
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
        $contracts = $this->getUserContract($asset_id);
        $contracts = array_merge($contracts['loanContract'], $contracts['creditContract']);
        $key = isset($contracts[$key]) ? $key : 0;
        return $this->render('contract', [
            'contracts' => $contracts,
            'fk' => $key,
            'content' => $contracts[$key]['content'],
            'asset_id' => $asset_id,
        ]);
    }

    /**
     * 获取用户合同
     * @param $asset_id
     * @param string $userType  seller:获取卖方合同;buyer:买方合同
     */
    private function getUserContract($asset_id)
    {
        //获取用户资产信息
        $txClient = \Yii::$container->get('txClient');
        $asset = $txClient->get('assets/detail', ['id' => $asset_id, 'validate' => false]);
        if (empty($asset) || !isset($asset['loan_id']) || !isset($asset['order_id'])) {
            throw new \Exception('没有找到合适资产信息');
        }
        if ($asset['user_id'] !== $this->getAuthedUser()->id) {
            throw $this->ex404('不能查看其他人的合同');
        }
        $loan = OnlineProduct::findOne($asset['loan_id']);
        $loanOrder = OnlineOrder::findOne($asset['order_id']);
        if (empty($loan) || empty($loanOrder)) {
            throw new \Exception('没有找到合适资产信息');
        }

        //获取原标的协议
        $loanTemplates = ContractTemplate::findAll(['pid' => $asset['loan_id']]);
        foreach ($loanTemplates as $key => $loanTemplate) {
            $template = ContractTemplate::replaceTemplate($loanTemplate, $loanOrder);
            $loanTemplates[$key] = $template->getAttributes();
        }
        $loanContract = [];
        foreach ($loanTemplates as $key => $template) {
            if ($key === 0) {
                $title = '认购合同';
            } elseif ($key === 1) {
                $title = '风险提示书';
            } else {
                $title = Yii::$app->functions->cut_str($template['name'], 5, 0, '**');
            }
            $loanContract[$key]  = ['title' => $title, 'content' => $template['content']];
        }
        $creditContract = [];
        //获取转协议
        if ($asset['note_id'] && $asset['credit_order_id']) {
            //购买该转让生成的转让合同
            $creditTemplate = $this->loadCreditContractByAsset($asset, $txClient, $loan, $loanOrder);
            $creditContract[] = ['title' => '产品转让协议', 'content' => $creditTemplate];
        }
        //获取该资产被转让的记录
        $soldRes = $txClient->get('assets/sold-res', ['asset_id' => $asset['id']]);
        if (count($soldRes) > 0) {
            foreach ($soldRes as $noteId => $assetLists) {
                $noteTemplate = [];
                if (count($assetLists) > 0) {
                    foreach ($assetLists as $asset) {
                        $noteTemplate[] = $this->loadCreditContractByAsset($asset, $txClient, $loan, $loanOrder);
                    }
                }
                if (count($noteTemplate) > 0) {
                    $contentRes = implode(' <br><hr><br> ', $noteTemplate);
                    $creditContract[] = ['title' => '产品转让协议', 'content' => $contentRes];
                }
            }
        }
        return ['loanContract' => $loanContract, 'creditContract' => $creditContract];
    }

    //根据债权资产、用户、标的订单、标的等信息填充债权转让合同模板
    private function loadCreditContractByAsset($asset, TxClient $txClient, OnlineProduct $loan, OnlineOrder $loanOrder)
    {
        if ($asset['note_id'] && $asset['credit_order_id']) {
            $queue = BaoQuanQueue::find()->where(['itemType' => BaoQuanQueue::TYPE_CREDIT_ORDER, 'itemId' => $asset['credit_order_id']])->one();
            $creditOrder = $txClient->get('credit-order/detail', ['id' => $asset['credit_order_id']]);
            $creditNote = $txClient->get('credit-note/detail', ['id' => $asset['note_id']]);
            $newPlans = $txClient->get('order/repayment', ['id' => $asset['order_id'], 'amount' => $creditOrder['principal']]);
            if ($asset['asset_id']) {
                $prevAsset = $txClient->get('assets/detail', ['id' => $asset['asset_id']]);
                if (empty($prevAsset)) {
                    throw new \Exception('没有找到资产');
                }
                $user = User::findOne($prevAsset['user_id']);
            } else {
                $user = $loanOrder->user;
            }
            $buyer = User::findOne($creditOrder['user_id']);
            if (
                !empty($queue)
                && !empty($creditOrder)
                && !empty($creditNote)
                && !empty($newPlans)
                && count($newPlans) > 0
                && isset($creditOrder['user_id'])
                && !empty($buyer)
            ) {
                //生成标的利率
                $loanRate = OnlineProduct::calcBaseRate($loan->yield_rate, $loan->jiaxi);
                if ($loan->isFlexRate && $loan->rateSteps) {
                    $loanRate = $loanRate . '~' . StringUtils::amountFormat2(RateSteps::getTopRate(RateSteps::parse($loan->rateSteps)));
                }
                if ($loan->jiaxi) {
                    $loanRate = $loanRate . '+' . $loan->jiaxi . '%';
                }
                //生成标的还款方式
                $refund_methods = Yii::$app->params['refund_method'];
                if (isset($refund_methods[$loan->refund_method])) {
                    $refund_method = $refund_methods[$loan->refund_method];
                } else {
                    $refund_method = null;
                }
                $payedInterest = 0;//按照还款日理论已经支付的利息
                $remainingInterest = 0;//按照还款计划理论未还款利息
                foreach ($newPlans as $plan) {
                    if ($plan['date'] < $creditOrder['createTime']) {
                        $payedInterest = bcadd($payedInterest, $plan['interest'], 2);
                    } else {
                        $remainingInterest = bcadd($remainingInterest, $plan['interest'], 2);
                    }
                }

                $creditTemplate = $this->renderFile('@common/views/credit_contract_template.php', [
                    'contractNum' => $queue->getNum(),
                    'sellerName' => $user->real_name,
                    'sellerIdCard' => $user->idcard,
                    'buyerName' => $buyer->real_name,
                    'buyerIdCard' => $buyer->idcard,
                    'loanOrderCreateDate' => date('Y-m-d', $loanOrder->order_time),
                    'loanTitle' => $loan->title,
                    'loanOrderPrincipal' => $loanOrder->order_money,
                    'creditOrderPrincipal' => bcdiv($creditOrder['principal'], 100, 2),
                    'loanIssuer' => $loan->getIssuerName(),
                    'affiliator' => $loan->getAffiliatorName(),
                    'exceptRaisedAmount' => $loan->money,
                    'incrAmount' => $loan->start_money,
                    'interestDate' => date('Y-m-d', $loan->jixi_time),
                    'finishDate' => date('Y-m-d', $loan->finish_date),
                    'yieldRate' => $loanRate,
                    'refundMethod' =>$refund_method,
                    'sellerInterest' => bcadd($payedInterest, bcdiv($creditOrder['interest'], 100, 2), 2),
                    'buyerInterest' => bcsub($remainingInterest, bcdiv($creditOrder['interest'], 100, 2), 2),
                    'discountRate' => $creditNote['discountRate'],
                    'refundedInterest' => $payedInterest,
                    'creditOrderPayAmount' => bcdiv($creditOrder['amount'], 100, 2),
                    'feeRate' => 3,
                    'feeAmount' => bcdiv($creditOrder['fee'], 100, 2),
                ]);
            } else {
                $creditTemplate = '';
            }
        } else {
            $creditTemplate = '';
        }
        return $creditTemplate;
    }


    /**
     * 合同显示页面.
     */
    public function actionAgreement($id, $note_id=0, $key = 0)
    {
        $contracts = [];
        $model = ContractTemplate::findAll(['pid' => $id]);
        if (empty($model)) {
            throw $this->ex404();  //当对象为空时,抛出异常
        }
        foreach ($model as $k => $val) {
            if ($k === 0) {
                $title = '认购合同';
            } elseif ($k === 1) {
                $title = '风险提示书';
            } else {
                $title = Yii::$app->functions->cut_str($val['name'], 5, 0, '**');
            }
            $contracts[]  = ['title' => $title, 'content' => $val['content']];
        }
        if ($note_id > 0) {
            $content = $this->renderFile('@common/views/credit_contract_template.php', [
                'contractNum' => '',
                'sellerName' => '',
                'sellerIdCard' => '',
                'buyerName' => '',
                'buyerIdCard' => '',
                'loanOrderCreateDate' => '',
                'loanTitle' => '',
                'loanOrderPrincipal' => '',
                'creditOrderPrincipal' => '',
                'loanIssuer' => '',
                'affiliator' => '',
                'exceptRaisedAmount' => '',
                'incrAmount' => '',
                'interestDate' => '',
                'finishDate' => '',
                'yieldRate' => '',
                'refundMethod' => '',
                'sellerInterest' => '',
                'buyerInterest' => '',
                'discountRate' => '',
                'refundedInterest' => '',
                'creditOrderPayAmount' => '',
                'feeRate' => '',
                'feeAmount' => '',
            ]);
            $contracts[] = ['title' => '产品转让协议', 'content' => $content];
        }
        $key = isset($contracts[$key]) ? $key : 0;
        $content = $contracts[$key]['content'];
        return $this->render('agreement', [
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
