<?php
namespace common\models\promo;

use common\exception\NotActivePromoException;
use common\models\mall\PointRecord;
use common\models\offline\OfflineOrder;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\user\IdCardIdentity;
use common\models\user\User;
use common\utils\SecurityUtils;
use common\utils\TxUtils;
use wap\modules\promotion\models\RankingPromo;

/**
 * 首次投资送积分活动: key => first_order_point
 */
class FirstOrderPoints
{
    public $promo;

    private $orderMoneyLimit = 50000;//超过5万送3500积分

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
    }

    public function doAfterSuccessLoanOrder(OnlineOrder $order){
        if ($this->canSendPoint($order)) {
            $this->addUserPoints($order);
        }
    }

    /**
     * 判断是否可以给用户发首投积分
     * 注意：O2O活动首投不送积分
     * @param OnlineOrder $order
     * @return bool
     */
    public function canSendPoint(OnlineOrder $order)
    {
        try {
            $user = $order->user;
            if (
                $order->status === OnlineOrder::STATUS_SUCCESS
                && $this->promo->isActive($user, $order->order_time)
            ) {
                //活动前没有投资
                $oldOrder = OnlineOrder::find()->where(['uid' => $user->id, 'status' => OnlineOrder::STATUS_SUCCESS])->andWhere(['<', 'order_time', strtotime($this->promo->startTime)])->one();
                if (!is_null($oldOrder)) {
                    return false;
                }

                //活动期间首次投资
                $query = OnlineOrder::find()->where(['uid' => $user->id, 'status' => OnlineOrder::STATUS_SUCCESS])->andWhere(['>=', 'order_time', strtotime($this->promo->startTime)]);
                if (!empty($this->promo->endTime)) {
                    $query = $query->andWhere(['<=', 'order_time', strtotime($this->promo->endTime)]);
                }
                $firstOrder = $query->orderBy(['order_time' => SORT_ASC])->one();
                if (is_null($firstOrder) || $firstOrder->id !== $order->id) {
                    return false;
                }
                //当前投资为第几次成功交易
                $idCardIdentity = new IdCardIdentity([
                    'idCard' => SecurityUtils::decrypt($user->safeIdCard),
                    ]);
                //不是第一次投资
                if ($idCardIdentity->getInvestNumber($order->order_time) > 1) {
                    return false;
                }
                //活动期间首次投资而且是O2O渠道注册用户
                if ($user->isO2oRegister()) {
                    return false;
                }

                if ($this->hasAwarded($user)) {
                    return false;
                }
                return true;
            }
        } catch (\Exception $ex) {
        }
        return false;
    }

    //判断用户是否已经发过首投积分
    public function hasAwarded(User $user)
    {
        $idCardIdentity = new IdCardIdentity([
            'idCard' => SecurityUtils::decrypt($user->safeIdCard),
            ]);
        return $idCardIdentity->isGetFirstAward();
    }

    //根据用户首次投资订单给用户送积分
    public function addUserPoints(OnlineOrder $firstOrder)
    {
        $money = $firstOrder->order_money;
        if ($money >= $this->orderMoneyLimit) {
            $points = 3500;
        } else {
            $points = 1400;
        }
        $user = $firstOrder->user;
        if ($this->hasAwarded($user)) {
            return false;
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            \Yii::$app->db->createCommand("UPDATE  `user` SET  `points` =  `points` + :points WHERE id = :userId", ['points' => $points, 'userId' => $firstOrder->uid])->execute();
            $user->refresh();
            $record = new PointRecord([
                'sn' => TxUtils::generateSn('PR'),
                'user_id' => $firstOrder->uid,
                'ref_type' => PointRecord::TYPE_FIRST_LOAN_ORDER_POINTS_1,//首次投资送积分
                'ref_id' => $firstOrder->id,
                'incr_points' => $points,
                'final_points' => $user->points,
                'recordTime' => date('Y-m-d H:i:s'),
                'userLevel' => $user->getLevel(),
            ]);
            $record->save();
            $transaction->commit();
            return true;
        } catch (\Exception $ex) {
            $transaction->rollBack();
            return false;
        }
    }
}
