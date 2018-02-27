<?php

namespace common\models\offline;

use common\models\mall\PointRecord;
use common\models\order\OnlineOrder;
use common\models\user\User;
use common\service\AccountService;
use common\utils\SecurityUtils;
use common\utils\TxUtils;
use Wcg\Xii\Crm\Model\Account;
use Yii;

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
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $points = self::getOrderPoints($order, $type);
                //更新账户积分
                self::doUpdatePoints($order, $points, $type);

                if ($type != PointRecord::TYPE_OFFLINE_POINT_ORDER) {
                    //是否首投&存在邀请人
                    $isFirst = self::isFirst($order);
                    /**
                     * 首投奖励
                     * 大于等于50000，奖励3500积分
                     * 其余奖励1400积分
                     */
                    if ($isFirst == 0 && $type == PointRecord::TYPE_OFFLINE_BUY_ORDER) {
                        self::sendFirstAward($order, PointRecord::TYPE_FIRST_LOAN_ORDER_POINTS_1);
                    }

                    //邀请人
                    $acount = Account::findOne($user->crmAccount_id);
                    if (!is_null($acount)) {
                        $inviter = OfflineUser::findOne(['crmAccount_id' => $acount->identity->inviter]);
                    }

                    if ($isFirst < 3 && !is_null($inviter)) { //前三次返现
                        if ($inviter->onlineUserId && $type == PointRecord::TYPE_OFFLINE_BUY_ORDER) {
                            self::fanxian($inviter, $order->money);
                        } elseif ($isFirst == 0 && is_null($inviter->onlineUserId)) {   //首次发几份
                            $invite_type = $points > 0 ? PointRecord::TYPE_OFFLINE_INVITE_REWARD : PointRecord::TYPE_OFFLINE_INVITE_RESET;
                            self::sendPointsInviter($inviter, $order->money, $order->id, $invite_type);
                        }
                    }
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                \Yii::info('积分更新失败，id[' . $order->id . ']，原因：' . $e->getMessage());
            }
        }
    }
    /**
     * 更新线下账户积分
     * @param $order
     * @param $type
     * @param OfflineUser $user
     * @param $points
     * @throws \Exception
     */
    private function updateOfflinePoints($order, $type, OfflineUser $user, $points)
    {
        $res = \Yii::$app->db->createCommand("UPDATE `offline_user` SET `points` = `points` + :points WHERE `id` = :userId", ['points' => $points, 'userId' => $user->id])->execute();
        if (!$res) {
            throw new \Exception('线下账户积分更新失败');
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
            throw new \Exception('线下账户积分流水更新失败');
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
        } elseif ($type == PointRecord::TYPE_OFFLINE_ORDER_DELETE) {
            $model = PointRecord::findOne([
                'ref_type' => PointRecord::TYPE_OFFLINE_BUY_ORDER,
                'ref_id' => $order->id,
            ]);
            if (!is_null($model)) {
                $points = $model->incr_points;
            }
        } else {
            $points = max(1, ceil(bcdiv(bcmul($order->annualInvestment, 6, 14), 1000, 2)));
            //不同等级奖励
            $points = ceil($points * self::multiple($order->user));
            //线下积分翻倍活动
            $promo = Yii::$app->params['offline_points'];
            if ($promo['start'] && $order->orderDate >= $promo['start'] && $promo['number'] > 1) {
                if ($promo['end']) {
                    if ($order->orderDate <= $promo['end']) {
                        $points *= $promo['number'];
                    }
                } else {
                    $points *= $promo['number'];
                }
            }
        }
        if (in_array($type, PointRecord::getDecrType())) {
            $points = 0 - $points;
        }
        return (int) $points;
    }
    /**
     * 线下用户首投，如果有邀请人的话，给邀请人发积分(邀请人纯线下)
     * 邀请人积分 = 投资人投资金额 × 0.6%
     * 线下订单单位是（万元），所以积分 = ×60
     */
    private function sendPointsInviter(OfflineUser $user, $money, $order_id, $type)
    {
        $points = $money * 60;
        if (in_array($type, PointRecord::getDecrType())) {
            $points = bcsub(0, $points);
        }
        //更新积分
        $res = \Yii::$app->db->createCommand("UPDATE `offline_user` SET `points` = `points` + :points WHERE `id` = :userId", ['points' => $points, 'userId' => $user->id])->execute();
        if (!$res) {
            throw new \Exception('积分更新失败');
        }
        $user->refresh();
        //流水
        $model = new PointRecord([
            'sn' => TxUtils::generateSn('OFF'),
            'user_id' => $user->id,
            'ref_type' => $type,
            'ref_id' => $order_id,
            'final_points' => $user->points,
            'remark' => PointRecord::getTypeName($type),
            'isOffline' => true,
            'recordTime' => date('Y-m-d H:i:s'),
        ]);
        if ($points > 0) {
            $model->incr_points = $points;
        } else {
            $model->decr_points = abs($points);
        }
        if (!$model->save(false)) {
            throw new \Exception('积分更新失败');
        }
        //线上账户
        if ($user->onlineUserId) {
            $online_user = User::findOne($user->onlineUserId);
            if ($online_user) {
                self::updateOnlinePoint($online_user, $points, $order_id, $type);
            }
        }
    }

    //更新线上用户积分
    private function updateOnlinePoint(User $user, $points, $orderId, $type)
    {
        $res = \Yii::$app->db->createCommand("UPDATE `user` SET `points` = `points` + :points WHERE `id` = :userId", ['points' => $points, 'userId' => $user->id])->execute();
        $user->refresh();
        if ($res) {
            $model = new PointRecord([
                'sn' => TxUtils::generateSn('OFF'),
                'user_id' => $user->id,
                'ref_type' => $type,
                'ref_id' => $orderId,
                'final_points' => $user->points,
                'remark' => PointRecord::getTypeName($type),
                'isOffline' => false,
                'recordTime' => date('Y-m-d H:i:s'),
            ]);

            if ($points > 0) {
                $model->incr_points = $points;
            } else {
                $model->decr_points = abs($points);
            }
            if (!$model->save(false)) {
                throw new \Exception('线上账户积分明细添加失败');
            }
        } else {
            throw new \Exception('线上账户积分更新失败');
        }
    }

    //根据用户等级获得相应的积分倍数
    Public static function multiple(OfflineUser $user)
    {
        switch ($user->level) {
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
        return $multiple;
    }

    //此身份证号的用户投资次数
    public static function isFirst(OfflineOrder $order)
    {
        $user = $order->user;
        //线下账户是否有过投资
        $off_count_orders = OfflineOrder::find()->where([
            'idCard' => $user->idCard,
            'isDeleted' => false])
            ->andWhere(['<', 'created_at', $order->created_at])
            ->count();
        //线上账户投资
        $on_count_orders = OnlineOrder::find()
            ->innerJoin('user', 'user.id = online_order.uid')
            ->where([
                'user.safeIdCard' => SecurityUtils::encrypt($user->idCard),
                'online_order.status' => 1,])
            ->andWhere(['<', 'online_order.created_at', $order->created_at])
            ->count();
        return bcadd($off_count_orders, $on_count_orders, 0);

    }
    //被邀请人前三次投资，给邀请人返现
    private function fanxian(OfflineUser $user, $money)
    {
        if ($user->onlineUserId) {
            $online_user = User::findOne($user->onlineUserId);
            if ($online_user && $online_user->annualInvestment) {
                //返现
                AccountService::userTransfer($online_user, bcmul($money, 10));
            }
        }
    }

    //首投奖励
    private function sendFirstAward(OfflineOrder $order, $type)
    {
        if (self::isFirst($order) == 0) {
            if (bcmul($order->money, 10000) >= 50000) {
                $points = 3500;
            } else {
                $points = 1400;
            }
            self::doUpdatePoints($order, $points, $type);
        }
    }

    //更新账户积分
    private function doUpdatePoints($order, $points, $type)
    {
        $user = $order->user;
        //更新线下
        $res = \Yii::$app->db->createCommand(
            "UPDATE `offline_user` SET `points` = `points` + :points WHERE `id` = :userId",
            ['points' => $points, 'userId' => $user->id]
        )->execute();
        if (!$res) {
            throw new \Exception('线下账户积分更新失败');
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
            throw new \Exception('线下账户积分流水更新失败');
        }
        //更新线上
        $onlineUser = $user->online;
        if (!is_null($onlineUser)) {
            $res = \Yii::$app->db->createCommand(
                "UPDATE `user` SET `points` = `points` + :points WHERE `id` = :userId",
                ['points' => $points, 'userId' => $onlineUser->id]
            )->execute();
            if (!$res) {
                throw new \Exception('线上账户积分更新失败');
            }
            $onlineUser->refresh();
            $record = new PointRecord([
                'sn' => TxUtils::generateSn('OFF'),
                'user_id' => $onlineUser->id,
                'ref_type' => $type,
                'ref_id' => $order->id,
                'final_points' => $onlineUser->points,
                'remark' => PointRecord::getTypeName($type),
                'recordTime' => date('Y-m-d H:i:s'),
                'isOffline' => false,
                'offGoodsName' => isset($order->offGoodsName) ? $order->offGoodsName : $order->loan->title,
                'userLevel' => $onlineUser->level,
            ]);
            if ($points > 0) {
                $record->incr_points = $points;
            } else {
                $record->decr_points = abs($points);
            }
            $res = $record->save();
            if (!$res) {
                throw new \Exception('线上账户积分流水更新失败');
            }
        }
    }
}