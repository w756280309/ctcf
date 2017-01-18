<?php
namespace common\models\promo;

use common\exception\NotActivePromoException;
use common\models\mall\PointRecord;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\user\User;
use common\utils\TxUtils;
use wap\modules\promotion\models\RankingPromo;

/**
 * 首次投资送积分活动
 */
class FirstOrderPoints
{
    public $promo;

    private $orderMoneyLimit = 50000;//超过5万送3500积分

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
    }

    public function doAfterLoanJixi(OnlineProduct $loan)
    {
        $loan->refresh();
        if ($loan->is_jixi) {
            $orders = OnlineOrder::find()->where(['online_pid' => $loan->id, 'status' => OnlineOrder::STATUS_SUCCESS])->all();
            foreach ($orders as $order) {
                try {
                    $this->addUserPointsWithLoanOrder($order);
                } catch (NotActivePromoException $ex) {
                }
            }
        }
    }

    private function addUserPointsWithLoanOrder(OnlineOrder $order)
    {
        $user = $order->user;
        if ($order->status === OnlineOrder::STATUS_SUCCESS && $this->promo->isActive($user, $order->order_time)) {
            //活动前没有投资
            $orderCount = OnlineOrder::find()->where(['uid' => $user->id, 'status' => OnlineOrder::STATUS_SUCCESS])->andWhere(['<', 'order_time', strtotime($this->promo->startTime)])->count();
            if ($orderCount > 0) {
                return;
            }
            //活动期间首次投资
            $firstOrder = OnlineOrder::find()->where(['uid' => $user->id, 'status' => OnlineOrder::STATUS_SUCCESS])->andWhere(['>=', 'order_time', strtotime($this->promo->startTime)])->andWhere(['<=', 'order_time', strtotime($this->promo->endTime)])->orderBy(['order_time' => SORT_ASC])->one();
            if (empty($firstOrder) || $firstOrder->id !== $order->id) {
                return;
            }
            $this->addUserPoints($order);
        }
    }

    //判断用户是否已经发过首投积分
    public function hasAwarded(User $user)
    {
        $record = PointRecord::findOne([
            'user_id' => $user->id,
            'ref_type' => PointRecord::TYPE_FIRST_LOAN_ORDER_POINTS_1,//首次投资送积分
        ]);
        return !is_null($record);
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