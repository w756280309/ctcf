<?php

namespace common\models\offline;

use common\models\mall\PointRecord;
use common\utils\TxUtils;
use Wcg\Xii\Crm\Model\Account;

class OfflinePointManager
{
    /**
     * 根据订单对象更新对应用户的积分和积分流水
     *
     * @param  object $order 订单
     * @param  string $type  积分类型
     *
     * @throws \Exception
     */
    public function updatePoints($order, $type)
    {
        $user = $order->user;
        $record = PointRecord::find()->where([
            'user_id' => $user->id,
            'ref_type' => $type,
            'ref_id' => $order->id,
            'isOffline' => true,
        ])->one();
        if (empty($record)) {
            $points = $this->getOrderPoints($order, $type);
            $res = \Yii::$app->db->createCommand("UPDATE `offline_user` SET `points` = `points` + :points WHERE `id` = :userId", ['points' => $points, 'userId' => $user->id])->execute();
            if (!$res) {
                throw new \Exception('积分更新失败');
            }
            $user->refresh();
            $record = PointRecord::initOfflineRecord($order, $type);
            if ($points > 0) {
                $record->incr_points = $points;
            } else {
                $record->decr_points = abs($points);
            }
            $res = $record->save();
            if (!$res) {
                throw new \Exception('积分流水更新失败');
            }
            //是否首投&存在邀请人
            $models = OfflineOrder::find()->where(['user_id' => $order->user_id])->all();
            if (count($models) == 1) {  //新用户首投
                //判断是否存在邀请人
                $acount = Account::findOne($order->user->crmAccount_id);
                if (!is_null($acount)) {
                    if ($acount->identity->inviter) {

                        //判断邀请人是否是线下用户
                        $inviter = OfflineUser::findOne(['crmAccount_id' => $acount->identity->inviter]);
                        if (!is_null($inviter)) {
                            self::sendPointsInviter($inviter, $order->money, $order->id);
                        }
                    }
                }
            }
        }
    }

    /**
     * 根据订单对象和对应的积分流水类型获得该笔订单的积分
     *
     * @param  object $order 订单
     * @param  string $type  积分流水类型
     *
     * @return int    订单积分
     */
    private function getOrderPoints($order, $type)
    {
        if ($type === PointRecord::TYPE_OFFLINE_POINT_ORDER) {
            $points = $order->points;
        } else {
            $models = OfflineOrder::find()->where(['user_id' => $order->user_id])->all();
            if (count($models) == 1) {  //新用户首投给3500积分
                $points = 1400;
            } else {
                $points = max(1, ceil(bcdiv(bcmul($order->annualInvestment, 6, 14), 1000, 2)));
            }
        }
        if (in_array($type, PointRecord::getDecrType())) {
            $points = 0 - $points;
        }

        return (int) $points;
    }
    /**
     * 线下用户首投，如果有邀请人的话，给邀请人发积分
     * 邀请人积分 = 投资人投资金额 × 0.6%
     * 线下订单单位是（万元），所以积分 = ×60
     */
    private function sendPointsInviter(OfflineUser $user, $money, $order_id)
    {
        $points = $money * 60;
        //更新积分
        $res = \Yii::$app->db->createCommand("UPDATE `offline_user` SET `points` = `points` + :points WHERE `id` = :userId", ['points' => $points, 'userId' => $user->id])->execute();
        if (!$res) {
            throw new \Exception('积分更新失败');
        }
        //流水
        $model = new PointRecord([
            'sn' => TxUtils::generateSn('OFF'),
            'user_id' => $user->id,
            'ref_type' => 'inviting_awards',
            'ref_id' => $order_id,
            'incr_points' => $points,
            'final_points' => bcadd($user->points, $points),
            'isOffline' => true,
            'remark' => '邀请奖励',
        ]);
        if (!$model->save(false)) {
            throw new \Exception('积分更新失败');
        }
    }
}
