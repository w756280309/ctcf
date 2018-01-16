<?php

namespace common\models\mall;

use common\models\order\OnlineOrder;
use common\models\offline\OfflineUser;
use common\models\user\User;
use common\utils\TxUtils;
use yii\db\ActiveRecord;
use Yii;

/**
 * 积分流水表
 *
 * This is the model class for table "point_record".
 *
 * @property integer $id
 * @property string  $sn
 * @property integer $user_id
 * @property string  $ref_type          导致积分变动的类型：购买标的、积分兑换……
 * @property integer $ref_id            导致积分变动的对应记录ID
 * @property integer $incr_points       增加积分
 * @property integer $decr_points       减少积分
 * @property integer $final_points      变动后剩余积分
 * @property string  $recordTime        流水时间
 * @property integer $userLevel         用户等级
 * @proprety boolean $isOffline         是否线下
 * @proprety string  $offGoodsName      线下商品名称
 * @property string  $remark            发放积分的描述
 */
class PointRecord extends ActiveRecord
{

    const TYPE_LOAN_ORDER = 'loan_order';   //购买标的
    const TYPE_POINT_ORDER = 'point_order'; //积分兑换
    const TYPE_ONLINEINVITE_REWARD = 'online_invite_reward';    //线上邀请奖励
    const TYPE_POINT_ORDER_FAIL = 'point_order_fail';//积分订单失败退款积分
    const TYPE_OFFLINE_BUY_ORDER = 'offline_loan_order'; //线下购买标的
    const TYPE_OFFLINE_POINT_ORDER = 'offline_point_order'; //线下积分兑换
    const TYPE_OFFLINE_ORDER_DELETE = 'offline_order_delete'; //线下订单删除扣减积分
    const TYPE_POINT_FA_FANG = 'point_fa_fang'; //积分发放
    const TYPE_FIRST_LOAN_ORDER_POINTS_1 = 'first_order_1';//首次投资送积分,活动 FirstOrderPoints
    const TYPE_OFFLINE_ORDER_POINT_CANCEL = 'offline_order_point_cancel';//线下投资订单积分撤销
    const TYPE_MALL_INCREASE = 'mall_increase';//积分商城获得
    const TYPE_BACKEND_BATCH = 'point_batch';//后台批量发放
    const TYPE_PROMO = 'promo'; //活动获得
    const TYPE_CHECK_IN = 'check_in';//签到获取
    const TYPE_WECHAT_CONNECT = 'wechat_connect'; //微信绑定奖励
    const REF_TYPE_CHECK_IN_RETENTION = 'check_in_retention'; //签到回归奖励
    const TYPE_OFFLINE_INVITE_REWARD = 'offline_invite_reward'; //线下邀请奖励
    const TYPE_OFFLINE_INVITE_RESET = 'offline_invite_reset';   //线下邀请投资撤回
    const TYPE_ACCOUNT_MERGE = 'account_merge';    //账号合并

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'point_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'ref_id', 'incr_points', 'decr_points', 'final_points'], 'integer'],
            [['recordTime'], 'safe'],
            [['sn', 'ref_type'], 'string', 'max' => 32],
            [['remark'], 'string', 'max' => 20],
            [['incr_points'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sn' => 'Sn',
            'user_id' => 'User ID',
            'ref_type' => 'Ref Type',
            'ref_id' => 'Ref ID',
            'incr_points' => '积分数额',
            'decr_points' => 'Decr Points',
            'final_points' => 'Final Points',
            'recordTime' => 'Record Time',
            'isOffline' => 'isOffline',
            'offGoodsName' => 'offGoodsName',
            'remark' => '发放积分描述',
        ];
    }

    /**
     * 获得积分流水类型名称
     *
     * @return string
     */
    public static function getTypeName($type)
    {
        $name = [
            self::TYPE_LOAN_ORDER => '购买标的',
            self::TYPE_POINT_ORDER => '积分兑换',
            self::TYPE_FIRST_LOAN_ORDER_POINTS_1 => '首投奖励',
            self::TYPE_POINT_ORDER_FAIL => '积分订单失败退款',
            self::TYPE_OFFLINE_BUY_ORDER => '线下购买标的',
            self::TYPE_OFFLINE_POINT_ORDER => '线下积分兑换',
            self::TYPE_OFFLINE_ORDER_DELETE => '线下订单删除扣减积分',
            self::TYPE_POINT_FA_FANG => '积分发放',
            self::TYPE_OFFLINE_ORDER_POINT_CANCEL => '16年投资撤销积分',
            self::TYPE_MALL_INCREASE => '积分商城获得',
            self::TYPE_BACKEND_BATCH => '后台批量发放',
            self::TYPE_PROMO => '活动获得',
            self::TYPE_CHECK_IN => '签到获得',
            self::TYPE_WECHAT_CONNECT => '绑定账户奖励',
            self::REF_TYPE_CHECK_IN_RETENTION => '签到回归奖励',
            self::TYPE_OFFLINE_INVITE_REWARD => '线下邀请奖励',
            self::TYPE_OFFLINE_INVITE_RESET => '线下邀请人取消投资',
            self::TYPE_ACCOUNT_MERGE => '账号合并',
            self::TYPE_ONLINEINVITE_REWARD => '邀请奖励',
        ];
        return isset($name[$type]) ? $name[$type] : '';
    }

    /**
     * 获取积分数值，使用金额显示样式二:
     * 增加积分显示 + 数值
     * 减少积分显示 - 数值
     * @return int
     */
    public function getDelta()
    {
        return (int)($this->incr_points > 0 ? $this->incr_points : -1 * $this->decr_points);
    }

    /**
     * 获得所有扣除积分的操作类型
     *
     * @return array
     */
    public static function getDecrType()
    {
        return [
            self::TYPE_POINT_ORDER,
            self::TYPE_OFFLINE_POINT_ORDER,
            self::TYPE_OFFLINE_ORDER_DELETE,
            self::TYPE_OFFLINE_INVITE_RESET,
        ];
    }

    /**
     * 根据订单对象初始化线下积分订单
     */
    public static function initOfflineRecord($order, $type)
    {
        $user = $order instanceof PointOrder ? OfflineUser::findOne($order->user_id) : $order->user;
        return new self([
            'sn' => TxUtils::generateSn('OFF'),
            'user_id' => $order->user_id,
            'ref_type' => $type,
            'ref_id' => $order->id,
            'final_points' => $user->points,
            'remark' => self::getTypeName($type),
            'recordTime' => date('Y-m-d H:i:s'),
            'isOffline' => true,
            'offGoodsName' => isset($order->offGoodsName) ? $order->offGoodsName : $order->loan->title,
            'userLevel' => $user->level,
        ]);
    }

    /**
     * 购买标的或首投送积分时,返回相关订单信息.
     */
    public function fetchOrder()
    {
        $order = null;

        if (in_array($this->ref_type, [self::TYPE_LOAN_ORDER, self::TYPE_FIRST_LOAN_ORDER_POINTS_1])) {
            $o = OnlineOrder::tableName();

            $order = OnlineOrder::find()
                ->joinWith('loan')
                ->where(["$o.id" => $this->ref_id])
                ->one();
        }

        return $order;
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
//根据用户信息和相应的积分减掉用户的积分以及在用户积分流水表（point_record）中添加记录
    public static function subtractUserPoints(User $user, $points)
    {
            if ($user->points < $points) {
                throw new \Exception('积分不足',8);
            }
            $res = Yii::$app->db->createCommand(
                "UPDATE `user` SET `points` = `points` - :points WHERE `id` = :userId", ['points' => $points, 'userId' => $user->id])
                ->execute();
            if (!$res) {
                throw new \Exception('系统繁忙',9);
            }
            $user->refresh();
            if ($user->points < 0) {
                throw new \Exception('积分不足',8);
            }
            $finalPoints = $user->points;
            $record = new PointRecord([
                'sn' => TxUtils::generateSn('PR'),
                'user_id' => $user->id,
                'ref_type' => PointRecord::TYPE_POINT_ORDER,
                'ref_id' => '',
                'decr_points' => $points,
                'final_points' => $finalPoints,
                'recordTime' => date('Y-m-d H:i:s'),
            ]);
            $res = $record->save();
            if (!$res) {
                throw new \Exception('系统繁忙',9);
            }
    }
}
