<?php

namespace frontend\modules\deal\controllers;

use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\product\RateSteps;
use frontend\controllers\BaseController;
use Yii;
use yii\data\Pagination;
use yii\helpers\Html;

class DealController extends BaseController
{

    public function actionDetail($sn)
    {
        $deal = $this->findOr404(OnlineProduct::className(), ['online_status' => OnlineProduct::STATUS_ONLINE, 'del_status' => OnlineProduct::STATUS_USE, 'sn' => $sn]);
        //未登录或者登录了，但不是定向用户的情况下，报404
        if (1 === $deal->isPrivate) {
            if (Yii::$app->user->isGuest) {
                $this->ex404('未登录用户不能查看定向标');
            } else {
                $user_ids = explode(',', $deal->allowedUids);
                if (!in_array(Yii::$app->user->identity->getId(), $user_ids)) {
                    $this->ex404('不能查看他人的定向标');
                }
            }
        }
        //获取可用代金券
        if(!Yii::$app->user->isGuest){
            $ct = CouponType::tableName();
            $data = UserCoupon::find()
                ->innerJoinWith('couponType')
                ->where(['isUsed' => 0, 'order_id' => null, "$ct.isDisabled" => 0])
                ->andWhere(['<=', "$ct.useStartDate", date('Y-m-d')])
                ->andWhere(['>=', "$ct.useEndDate", date('Y-m-d')])
                ->andWhere(['user_id' => $this->getAuthedUser()->id])
                ->orderBy("$ct.useEndDate desc, $ct.amount desc, $ct.minInvest asc")
                ->all();
        } else {
            $data = [];
        }
        return $this->render('detail', [
            'deal' => $deal,
            'data' => $data
        ]);
    }

    public function actionOrderList($pid)
    {
        $this->findOr404(OnlineProduct::className(), ['id' => $pid]);
        $query = OnlineOrder::find()->where(['online_pid' => $pid, 'status' => 1])->select('mobile,order_time,order_money')->orderBy("id desc");
        $pages = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => 10
        ]);
        $data = $query->offset($pages->offset)->limit($pages->limit)->all();
        return $this->renderFile('@frontend/modules/deal/views/deal/_order_list.php', [
            'data' => $data,
            'pages' => $pages,
        ]);
    }

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