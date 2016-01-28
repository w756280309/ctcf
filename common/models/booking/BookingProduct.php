<?php

namespace common\models\booking;

/**
 * 预约产品
 *
 * @property int $id
 * @property string $name
 * @property int $is_disabled
 * @property int $start_time
 * @property int $end_time
 * @property int $min_fund
 * @property int $total_fund
 * @property int $created_at
 * @property int $updated_at
 */
class BookingProduct extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'booking_product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'name', 'start_time', 'end_time', 'created_at', 'updated_at'], 'required'],
            [['id', 'is_disabled', 'start_time', 'end_time', 'min_fund', 'total_fund', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '项目名称',
            'is_disabled' => '是否禁用',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'min_fund' => '起投金额',
            'total_fund' => '总额',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
