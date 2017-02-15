<?php

namespace common\models\promo;

use common\exception\NotActivePromoException;
use common\models\mall\PointRecord;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\utils\TxUtils;
use wap\modules\promotion\models\RankingPromo;

/**
 * 购买标的送积分活动:loan_order_points
 */
class LoanOrderPoints
{
    public $promo;

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
    }

    public function doAfterSuccessLoanOrder(OnlineOrder $order){
        if ($this->canSendPoint($order)) {
            $this->addUserPointsWithLoanOrder($order);
        }
    }

    public function canSendPoint(OnlineOrder $order)
    {
        $user = $order->user;
        try {
            if (
                $order->status === OnlineOrder::STATUS_SUCCESS
                && $this->promo->isActive($user, $order->order_time)
            ) {
                $record = PointRecord::find()->where([
                    'user_id' => $user->id,
                    'ref_type' => PointRecord::TYPE_LOAN_ORDER,
                    'ref_id' => $order->id
                ])->one();
                if (is_null($record)) {
                    return true;
                }
            }
        } catch (\Exception $ex) {

        }
        return false;
    }

    /**
     * 根据标的订单为用户添加积分.
     */
    public function addUserPointsWithLoanOrder(OnlineOrder $order)
    {
        $user = $order->user;
        $loan = $order->loan;
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $points = $this->getPointsWithOrder($order, $loan);
            $level = $order->user->level;
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
                'userLevel' => $level,
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

    /**
     * 根据订单计算用户所得积分
     * @param OnlineOrder $order
     * @param OnlineProduct $loan
     * return int
     */
    private function getPointsWithOrder(OnlineOrder $order, OnlineProduct $loan)
    {
        switch ($order->user->level) {
            case 0:
                $multiple = 1;
                break;
            case 1:
                $multiple = 1.02;
                break;
            case 2:
                $multiple = 1.04;
                break;
            case 3:
                $multiple = 1.06;
                break;
            case 4:
                $multiple = 1.08;
                break;
            case 5:
                $multiple = 1.1;
                break;
            case 6:
                $multiple = 1.12;
                break;
            case 7:
                $multiple = 1.15;
                break;
            default :
                $multiple = 1;
        }

        $points = ceil(bcdiv(bcmul(bcmul($order->annualInvestment, 6, 14), $multiple, 14), 1000, 2));
        return max(1, $points) * $loan->pointsMultiple;
    }
}