<?php

namespace common\models\offline;

use common\models\mall\PointRecord;
use common\models\order\OnlineOrder;
use common\models\user\User;
use common\service\AccountService;
use common\utils\SecurityUtils;
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
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $points = self::getOrderPoints($order, $type);

                //更新线下账户积分
                self::updateOfflinePoints($order, $type, $user, $points);

                //更新线上积分
                if ($user->onlineUserId) {
                    $online_user = User::findOne($order->user->onlineUserId);
                    if ($online_user) {
                        self::updateOnlinePoint($online_user, $points, $order->id, $type);
                    }
                }

                if ($type != PointRecord::TYPE_OFFLINE_POINT_ORDER) {
                    //是否首投&存在邀请人
                    $isFirst = self::isFirst($order);

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
        } else {
            $points = max(1, ceil(bcdiv(bcmul($order->annualInvestment, 6, 14), 1000, 2)));

            //是否存在线上账户
            if ($order->user->onlineUserId) {
                $online_user = User::findOne($order->user->onlineUserId);
                if ($online_user) {
                    $points = ceil($points * self::multiple($online_user));
                }
            }
            if ($type != PointRecord::TYPE_OFFLINE_POINT_ORDER) {
                //新用户首投赠送1400积分
                $isFirst = self::isFirst($order);
                if ($isFirst == 0) {
                    $points = bcadd($points, 1400, 2);
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
    private function updateOnlinePoint(User $user, $points, $orderid, $type)
    {
        $res = \Yii::$app->db->createCommand("UPDATE `user` SET `points` = `points` + :points WHERE `id` = :userId", ['points' => $points, 'userId' => $user->id])->execute();
        $user->refresh();
        if ($res) {
            $model = new PointRecord([
                'sn' => TxUtils::generateSn('OFF'),
                'user_id' => $user->id,
                'ref_type' => $type,
                'ref_id' => $orderid,
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
    Public static function multiple(User $user)
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
        $off_count_orders = 0;
        $on_count_orders = 0;
        //线下账户是否有过投资
        $off_count_orders = OfflineOrder::find()->where([
            'idCard' => $user->idCard,
            'isDeleted' => false])
            ->andWhere(['<', 'created_at', $order->created_at])
            ->count();
        //线上账户投资
        $online_users = User::find()
            ->select('id')
            ->where(['safeIdCard' => SecurityUtils::encrypt($user->idCard)])
            ->asArray()
            ->all();
        $arr = array_column($online_users, 'id');
        if (!empty($arr)) {
            $on_count_orders = OnlineOrder::find()->where(['in', 'uid', $arr])->andWhere(['status' => 1])->count();
        }
        return bcadd($off_count_orders, $on_count_orders);

    }
    //被邀请人前三次投资，给邀请人返现
    public static function fanxian(OfflineUser $user, $money)
    {
        if ($user->onlineUserId) {
            $online_user = User::findOne($user->onlineUserId);
            if ($online_user && $online_user->annualInvestment) {
                //返现
                AccountService::userTransfer($online_user, bcmul($money, 10));
            }
        }
    }
}