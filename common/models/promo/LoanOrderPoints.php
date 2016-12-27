<?php

namespace common\models\promo;

use common\exception\NotActivePromoException;
use common\models\mall\PointRecord;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\utils\TxUtils;
use wap\modules\promotion\models\RankingPromo;

/**
 * 购买标的送积分活动
 */
class LoanOrderPoints
{
    public $promo;

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
    }

    //标的确认计息后统一调用逻辑
    public function doAfterLoanJixi(OnlineProduct $loan)
    {
        //todo 理财计划目前是使用 allowUseCoupon 判断，但是需要调整
        $loan->refresh();
        if ($loan->is_jixi && !$loan->is_xs && $loan->allowUseCoupon) {
            $orders = OnlineOrder::find()->where(['online_pid' => $loan->id, 'status' => OnlineOrder::STATUS_SUCCESS])->all();
            foreach ($orders as $order) {
                try {
                    $this->addUserPointsWithLoanOrder($order, $loan);
                } catch (NotActivePromoException $ex) {
                }
            }
        }
    }

    //根据标的订单为用户添加积分
    private function addUserPointsWithLoanOrder(OnlineOrder $order, OnlineProduct $loan)
    {
        $user = $order->user;
        if ($order->status === OnlineOrder::STATUS_SUCCESS && $this->promo->isActive($user)) {
            $record = PointRecord::find()->where([
                'user_id' => $user->id,
                'ref_type' => PointRecord::TYPE_LOAN_ORDER,
                'ref_id' => $order->id
            ])->one();
            if (empty($record)) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $points = $this->getPointsWithOrder($order, $loan);
                    $res = \Yii::$app->db->createCommand("UPDATE `user` SET `points` = `points` + :points WHERE `id` = :userId", ['points' => $points, 'userId' => $user->id])->execute();
                    if (!$res) {
                        throw new \Exception('数据保存失败1');
                    }
                    $user->refresh();
                    $finalPoints = $user->points;
                    $record = new PointRecord([
                        'sn' => TxUtils::generateSn('PR'),
                        'user_id' => $user->id,
                        'ref_type' => PointRecord::TYPE_LOAN_ORDER,
                        'ref_id' => $order->id,
                        'incr_points' => $points,
                        'final_points' => $finalPoints,
                        'recordTime' => date('Y-m-d H:i:s'),
                    ]);
                    $res = $record->save();
                    if (!$res) {
                        throw new \Exception('数据保存失败2');
                    }
                    $transaction->commit();
                } catch (\Exception $ex) {
                    $transaction->rollBack();
                }
            }
        }
    }

    //根据订单计算用户所得积分
    private function getPointsWithOrder(OnlineOrder $order, OnlineProduct $loan)
    {
        if ($loan->isAmortized()) {
            $money = $loan->expires / 12 * $order->order_money;
        } else {
            $money = $loan->expires / 365 * $order->order_money;
        }
        return ceil($money * 6 / 1000);
    }
}