<?php

namespace common\models\mall;

use Yii;

/**
 * 积分流水表
 *
 * This is the model class for table "point_record".
 *
 * @property integer $id
 * @property string $sn
 * @property integer $user_id
 * @property string $ref_type           导致积分变动的类型：购买标的、积分兑换……
 * @property integer $ref_id            导致积分变动的对应记录ID
 * @property integer $incr_points       增加积分
 * @property integer $decr_points       减少积分
 * @property integer $final_points      变动后剩余积分
 * @property string $recordTime         流水时间
 */
class PointRecord extends \yii\db\ActiveRecord
{

    const TYPE_LOAN_ORDER = 'loan_order';   //购买标的
    const TYPE_POINT_ORDER = 'point_order'; //积分兑换
    const TYPE_POINT_ORDER_FAIL = 'point_order_fail';//积分订单失败退款积分

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
            'incr_points' => 'Incr Points',
            'decr_points' => 'Decr Points',
            'final_points' => 'Final Points',
            'recordTime' => 'Record Time',
        ];
    }
}