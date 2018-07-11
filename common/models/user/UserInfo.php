<?php

namespace common\models\user;

use common\models\offline\OfflineOrder;
use common\models\order\OnlineOrder;
use common\models\product\RepaymentHelper;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_info".
 *
 * @property integer $id
 * @property integer $user_id             用户ID
 * @property integer $isInvested          是否投资过
 * @property integer $investCount         成功投资次数
 * @property string  $investTotal         累计投资金额
 * @property string  $firstInvestDate     第一次投资时间
 * @property string  $lastInvestDate      最后一次投资时间
 * @property string  $firstInvestAmount   第一次投资金额
 * @property string  $lastInvestAmount    最后一次投资金额
 * @property string  $averageInvestAmount 平均投资金额
 * @property boolean $isAffiliator        是否为被邀请人
 */
class UserInfo extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'isInvested', 'investCount'], 'integer'],
            ['isAffiliator', 'boolean'],
            [['investTotal', 'firstInvestAmount', 'lastInvestAmount', 'averageInvestAmount'], 'number'],
            [['firstInvestDate', 'lastInvestDate'], 'safe'],
            [['user_id'], 'required'],
            [['user_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'isInvested' => '是否投资过',
            'investCount' => '成功投资次数',
            'investTotal' => '累计投资金额',
            'firstInvestDate' => '第一次投资时间',
            'lastInvestDate' => '最后一次投资时间',
            'firstInvestAmount' => '第一次投资金额',
            'lastInvestAmount' => '最后一次投资金额',
            'averageInvestAmount' => '平均投资金额',
            'isAffiliator' => '是否为被邀请人',
        ];
    }

    //初始化用户信息
    public static function initUserInfo()
    {
        $users = User::find()->where(['status' => 1])->asArray()->all();
        if ($users) {
            foreach ($users as $user) {
                self::updateUserInfoOfUser($user);
            }
        }
    }

    //初始化制定用户的信息
    public static function updateUserInfoOfUser($user)
    {
        $info = UserInfo::find()->where(['user_id' => $user['id']])->one();
        if (null === $info) {
            $info = new UserInfo();
            $info->user_id = $user['id'];
            $info->isInvested = 0;
        }
        //更新用户 是否投资过、成功投资次数、成功投资总额、平均投资金额
        $data = OnlineOrder::find()
            ->select(['SUM(order_money) as s', 'AVG(order_money) as a', 'COUNT(order_money) as c'])
            ->where(['uid' => $user['id'], 'status' => 1])
            ->asArray()
            ->one();
        if ($data) {
            if (intval($data['c']) > 0) {
                $info->isInvested = 1;
            }
            $info->investCount = $data['c'];
            $info->averageInvestAmount = $data['a'];
            $info->investTotal = $data['s'];
        }
        //获取用户首次投资信息
        $firstData = OnlineOrder::find()
            ->select(['order_time', 'order_money'])
            ->where(['uid' => $user['id'], 'status' => 1])
            ->orderBy(['order_time' => SORT_ASC])
            ->limit(1)
            ->asArray()
            ->one();
        if ($firstData) {
            $info->firstInvestAmount = $firstData['order_money'];
            $info->firstInvestDate = date('Y-m-d', $firstData['order_time']);
        } else {
            $info->firstInvestAmount = 0;
            $info->firstInvestDate = null;
        }
        //获取用户最后一次投资信息
        $lastData = OnlineOrder::find()
            ->select(['order_time', 'order_money'])
            ->where(['uid' => $user['id'], 'status' => 1])
            ->orderBy(['order_time' => SORT_DESC])
            ->limit(1)
            ->asArray()
            ->one();
        if ($lastData) {
            $info->lastInvestAmount = $lastData['order_money'];
            $info->lastInvestDate = date('Y-m-d', $lastData['order_time']);
        } else {
            $info->lastInvestAmount = 0;
            $info->lastInvestDate = null;
        }

        $res = $info->save();
        if (!$res) {
            throw new \Exception(json_encode($info->getErrors()));
        }
    }

    //投资成功之后更新用户信息
    public static function dealWidthOrder(OnlineOrder $order)
    {
        if ($order->status === 1) {
            $info = UserInfo::find()->where(['user_id' => $order->uid])->one();
            if (null === $info) {
                $info = new UserInfo();
                $info->user_id = $order->uid;
            }
            if (!$info->isInvested) {
                $info->isInvested = 1;
            }
            if (!$info->firstInvestAmount) {
                $info->firstInvestAmount = $order->order_money;
            }
            if (!$info->firstInvestDate) {
                $info->firstInvestDate = date('Y-m-d', $order->order_time);
            }
            $info->investCount = $info->investCount + 1;
            $info->investTotal = $info->investTotal + $order->order_money;
            $info->averageInvestAmount = $info->investTotal / $info->investCount;
            $info->lastInvestAmount = $order->order_money;
            $info->lastInvestDate = date('Y-m-d', $order->order_time);
            $info->save();
        }
    }

    /**
     * 计算某个人一段时间内的累计年化金额
     *
     * @param int    $userOrId  user表ID
     * @param string $startDate 开始日期
     * @param string $endDate   结束日期
     *
     * @return int
     */
    public static function calcAnnualInvest($userOrId, $startDate, $endDate)
    {
        $db = \Yii::$app->db;
        $sql = "select sum(truncate((if(p.refund_method > 1, o.order_money*p.expires/12, o.order_money*p.expires/365)), 2)) as annual from online_order o inner join online_product p on o.online_pid = p.id inner join user u on u.id = o.uid where date(from_unixtime(o.order_time)) >= :startDate and date(from_unixtime(o.order_time)) <= :endDate and o.status = 1 and o.uid = :userId and p.refund_method != 10";
        $annualInvest = (int) $db->createCommand($sql, [
            'userId' => $userOrId,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->queryScalar();
        $debxAnnualInvest = (int) self::getDebxOnlineAnnualInvest($userOrId, $startDate, $endDate);
        $allAnnualInvest = $annualInvest + $debxAnnualInvest;

        return $allAnnualInvest;
    }

    /**
     * 计算某个人一段时间内的累计投资金额
     *
     * @param $userId
     * @param $startTime
     * @param $endTime
     *
     * @return int
     */
    public static function calcInvest($userId, $startTime, $endTime)
    {
        $db = \Yii::$app->db;
        $sql = "select sum(order_money) from online_order where uid = :userId and status = 1 and from_unixtime(order_time) >= :startTime and from_unixtime(order_time) <= :endTime";

        return (int) $db->createCommand($sql, [
            'userId' => $userId,
            'startTime' => $startTime,
            'endTime' => $endTime,
        ])->queryScalar();
    }

    /**
     * 计算某个人一段时间内的线下累计年化金额
     * 注意：对于订单的审核时间也做了限制
     *
     * @param int    $userOrId  user表ID
     * @param string $startDate 开始日期
     * @param string $endDate   结束日期
     *
     * @return int
     */
    public static function calcOfflineAnnualInvest($userOrId, $startDate, $endDate)
    {
        $db = \Yii::$app->db;
        $sql = "select 
sum(truncate((if(p.repaymentMethod > 1, o.money*p.expires*10000/12, o.money*p.expires*10000/365)), 2)) as annual
from offline_order o 
inner join offline_loan p 
    on o.loan_id = p.id 
inner join offline_user u 
    on u.id = o.user_id 
where date(o.orderDate) >= :startDate 
    and date(o.orderDate) <= :endDate 
    and o.isDeleted = 0 
    and p.repaymentMethod != 10 
    and date(from_unixtime(o.created_at)) >= :startDate 
    and date(from_unixtime(o.created_at)) <= :endDate 
    and u.onlineUserId = :userId";

        $annualInvest = (int) $db->createCommand($sql, [
            'userId' => $userOrId,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->queryScalar();
        $debxAnnualInvest = (int) self::getDebxOfflineAnnualInvest($userOrId, $startDate, $endDate);
        $allAnnualInvest = $annualInvest + $debxAnnualInvest;

        return $allAnnualInvest;
    }

    /**
     * 计算某个人一段时间内的线下累计投资金额
     * 注意：对于订单的审核时间也做了限制
     *
     * @param int    $userId    User表ID
     * @param string $startTime 开始时间
     * @param string $endTime   结束时间
     *
     * @return int
     */
    public static function calcOfflineInvest($userId, $startTime, $endTime)
    {
        $db = \Yii::$app->db;
        $sql = "select 
sum(money*10000) 
from offline_order o 
inner join offline_user u 
    on o.user_id = u.id 
where o.user_id = :userId 
    and o.isDeleted = false 
    and o.orderDate >= :startTime 
    and o.orderDate <= :endTime 
    and from_unixtime(o.created_at) >= :startTime 
    and from_unixtime(o.created_at) <= :endTime";

        return (int) $db->createCommand($sql, [
            'userId' => $userId,
            'startTime' => $startTime,
            'endTime' => $endTime,
        ])->queryScalar();
    }

    /**
     * 累计投资额（包含转让）
     *
     * @return string
     */
    public function getTotalInvestMoney()
    {
        return bcadd($this->investTotal, $this->creditInvestTotal, 2);
    }

    /**
     * 统计线上某个人某段时间内所有等额本息投标记录,计算年化投资金额
     * @param int $userId
     * @param string $startDate
     * @param string $endDate
     * @return float|int
     */
    public static function getDebxOnlineAnnualInvest($userId, $startDate, $endDate)
    {
        $onlineOrders = OnlineOrder::getDebxUserOnlineOrdersArray($userId, $startDate, $endDate);
        $allAnnualInvestAmount = 0;
        foreach ($onlineOrders as $order) {
            $startDate = (new \DateTime())->setTimestamp($order['order_time'])->add(new \DateInterval('P1D'))->format('Y-m-d');
            $duration = intval($order['expires']);
            $apr = $order['apr'];
            $amount = $order['money'];
            $annualInvestAmount = RepaymentHelper::calcDebxAnnualInvest($startDate, $duration, $apr, $amount);
            $allAnnualInvestAmount += $annualInvestAmount;
        }

        return $allAnnualInvestAmount;
    }

    /**
     * 统计线下某个人某段时间内所有等额本息投标记录,计算累计年化投资金额
     * @param int $userId
     * @param string $startDate
     * @param string $endDate
     * @return float|int
     */
    public static function getDebxOfflineAnnualInvest($userId, $startDate, $endDate)
    {
        $offlineOrders = OfflineOrder::getDebxUserOfflineOrdersArray($userId, $startDate, $endDate);
        $allAnnualInvestAmount = 0;
        foreach ($offlineOrders as $order) {
            $startDate = (new \DateTime($order['orderDate']))->add(new \DateInterval('P1D'))->format('Y-m-d');
            $duration = intval($order['expires']);
            $apr = $order['apr'];
            $amount = $order['money'];
            $annualInvestAmount = RepaymentHelper::calcDebxAnnualInvest($startDate, $duration, $apr, $amount);
            $allAnnualInvestAmount += $annualInvestAmount;
        }

        return $allAnnualInvestAmount;
    }
}
