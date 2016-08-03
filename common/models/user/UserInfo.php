<?php

namespace common\models\user;

use common\models\order\OnlineOrder;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_info".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $isInvested
 * @property integer $investCount
 * @property string $investTotal
 * @property string $firstInvestDate
 * @property string $lastInvestDate
 * @property string $firstInvestAmount
 * @property string $lastInvestAmount
 * @property string $averageInvestAmount
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
        ];
    }

    //初始化用户信息
    public static function initUserInfo()
    {
        $users = User::find()->where(['status' => 1])->asArray()->all();
        if ($users) {
            foreach ($users as $user) {
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
                }

                $res = $info->save();
                if (!$res) {
                    throw new \Exception(json_encode($info->getErrors()));
                }
            }
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
}
