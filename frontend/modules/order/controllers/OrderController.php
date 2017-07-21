<?php

namespace frontend\modules\order\controllers;

use common\controllers\ContractTrait;
use common\models\coupon\UserCoupon;
use common\models\order\OnlineOrder;
use common\models\order\OrderManager;
use common\models\product\OnlineProduct;
use common\service\PayService;
use frontend\controllers\BaseController;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class OrderController extends BaseController
{
    use ContractTrait;

    public function behaviors()
    {
        return [
            'access' => [    //登录控制,如果没有登录,则跳转到登录页面
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 生成订单
     */
    public function actionDoorder($sn)
    {
        if (empty($sn) || (null === ($deal = OnlineProduct::findOne(['sn' => $sn])))) {
            throw $this->ex404();   //判断参数无效时,抛404异常
        }
        $user = $this->getAuthedUser();
        $money = \Yii::$app->request->post('money');
        $detailData = Yii::$app->session->get('detail_data');
        $userCouponIds = isset($detailData[$sn]['couponId']) ? $detailData[$sn]['couponId'] : [];

        //检验代金券的使用
        $couponMoney = 0; //记录可用的代金券金额
        $checkMoney = $money; //校验输入的金额
        $existUnUseCoupon = false; //是否存在不可用代金券
        $lastErrMsg = ''; //最后一个不可用代金券的错误提示信息
        if (is_array($userCouponIds) && !empty($userCouponIds)) {
            $userCouponIds = array_filter($userCouponIds);
            $u = UserCoupon::tableName();
            $userCoupons = UserCoupon::find()
                ->where(['in', "$u.id", $userCouponIds])
                ->all();
            foreach ($userCoupons as $key => $userCoupon) {
                try {
                    UserCoupon::checkAllowUse($userCoupon, $checkMoney, $user, $deal);
                } catch (\Exception $ex) {
                    $lastErrMsg = $ex->getMessage();
                    $existUnUseCoupon = true;
                    unset($userCoupons[$key]);
                    continue;
                }
                $couponType = $userCoupon->couponType;
                $couponMoney = bcadd($couponMoney, $couponType->amount, 2);
                $checkMoney = bcsub($checkMoney, $couponType->minInvest, 2);
            }
            $userCouponIds = ArrayHelper::getColumn($userCoupons, 'id');
        }
        //将所有可用的代金券写入session，并判断是否存在不可用的代金券，返回报错信息
        $detailData[$sn]['couponId'] = $userCouponIds;
        Yii::$app->session->set('detail_data', $detailData);
        if ($existUnUseCoupon) {
            return [
                'code' => 1,
                'message' => $lastErrMsg,
            ];
        }

        $pay = new PayService(PayService::REQUEST_AJAX);
        $ret = $pay->checkAllowPay($user, $sn, $money, $couponMoney, 'pc');
        if ($ret['code'] != PayService::ERROR_SUCCESS) {
            return $ret;
        }
        //下订单之前删除保存在session中的购买数据
        if (Yii::$app->session->has('detail_data')) {
            $detailData = Yii::$app->session->get('detail_data');
            if (isset($detailData[$sn])) {
                unset($detailData[$sn]);
            }
            Yii::$app->session->set('detail_data', $detailData);
        }

        $orderManager = new OrderManager();
        //记录订单来源
        $investFrom = OnlineOrder::INVEST_FROM_PC;
        if ($this->fromWx()) {
            $investFrom = OnlineOrder::INVEST_FROM_WX;
        }
        return $orderManager->createOrder($sn, $money, $userCouponIds, $user->id, $investFrom);
    }

    /**
     * 认购标的中间处理页.
     */
    public function actionWait($osn)
    {
        if (empty($osn)) {
            throw $this->ex404();   //判断参数无效时,抛404异常
        }

        $order = OnlineOrder::ensureOrder($osn);

        return $this->render('wait', [
            'order' => $order,
        ]);
    }

    /**
     * 查看用户合同
     * @param $asset_id
     * @return mixed
     */
    public function actionContract($asset_id)
    {
        $txClient = \Yii::$container->get('txClient');
        $asset = $txClient->get('assets/detail', ['id' => $asset_id, 'validate' => false]);
        if ($asset['user_id'] !== $this->getAuthedUser()->id) {
            throw new \Exception('不能查看他人的合同');
        }
        $contracts = $this->getUserContract($asset);
        $bqLoan = $contracts['bqLoan'];
        return $this->render('contract', [
            'loanContracts' => $contracts['loanContract'],
            'creditContracts' => $contracts['creditContract'],
            'bqLoan' => $bqLoan,
        ]);
    }

    /**
     * 合同页面(原始合同)
     */
    public function actionAgreement($pid, $note_id = 0)
    {
        $contracts = $this->getContractTemplate($pid, $note_id);
        return $this->render('agreement', [
            'contracts' => $contracts,
        ]);
    }

    /**
     * 认购标的结果页
     */
    public function actionResult($osn)
    {
        $order = OnlineOrder::ensureOrder($osn);

        if (\Yii::$app->request->isAjax) {
            return ['status' => $order->status];
        } else {
            if (1 === $order->status) {
                return $this->redirect('/info/success?source=touzi&jumpUrl=/user/user/myorder?type=2');
            } else {
                return $this->redirect('/info/fail?source=touzi');
            }
        }
    }
}
