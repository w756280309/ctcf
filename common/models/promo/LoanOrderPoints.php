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

    /**
     * 标的确认计息后统一调用逻辑.
     */
    public function doAfterLoanJixi(OnlineProduct $loan)
    {
        $loan->refresh();
        if ($loan->is_jixi && !$loan->is_xs && !$loan->isLicai) {
            $orders = OnlineOrder::find()->where(['online_pid' => $loan->id, 'status' => OnlineOrder::STATUS_SUCCESS])->all();
            foreach ($orders as $order) {
                try {
                    $this->addUserPointsWithLoanOrder($order, $loan);
                } catch (NotActivePromoException $ex) {
                }
            }
        }
    }

    /**
     * 根据标的订单为用户添加积分.
     */
    private function addUserPointsWithLoanOrder(OnlineOrder $order, OnlineProduct $loan)
    {
        $user = $order->user;
        if ($order->status === OnlineOrder::STATUS_SUCCESS && $this->promo->isActive($user, $order->order_time)) {
            $record = PointRecord::find()->where([
                'user_id' => $user->id,
                'ref_type' => PointRecord::TYPE_LOAN_ORDER,
                'ref_id' => $order->id
            ])->one();
            if (empty($record)) {
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