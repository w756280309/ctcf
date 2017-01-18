<?php

namespace common\models\mall;

use common\models\offline\OfflineUser;
use common\utils\TxUtils;
use Yii;
use yii\db\ActiveRecord;

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
    const TYPE_POINT_ORDER_FAIL = 'point_order_fail';//积分订单失败退款积分
    const TYPE_OFFLINE_BUY_ORDER = 'offline_loan_order'; //线下购买标的
    const TYPE_OFFLINE_POINT_ORDER = 'offline_point_order'; //线下积分兑换
    const TYPE_OFFLINE_ORDER_DELETE = 'offline_order_delete'; //线下订单删除扣减积分
    const TYPE_POINT_FA_FANG = 'point_fa_fang'; //积分发放
    const TYPE_FIRST_LOAN_ORDER_POINTS_1 = 'first_order_1';//首次投资送积分,活动 FirstOrderPoints

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
            [['remark'], 'string', 'max' => 6],
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
        ];
        return isset($name[$type]) ? $name[$type] : '';
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
            'recordTime' => date('Y-m-d H:i:s'),
            'isOffline' => true,
            'offGoodsName' => isset($order->offGoodsName) ? $order->offGoodsName : $order->loan->title,
            'userLevel' => $user->level,
        ]);
    }
}
