<?php

namespace common\models\booking;

use Yii;

/**
 * This is the model class for table "booking_product".
 *
 * @property integer $id
 * @property string $name
 * @property integer $is_disabled
 * @property integer $start_time
 * @property integer $end_time
 * @property integer $min_fund
 * @property integer $total_fund
 * @property integer $created_at
 * @property integer $updated_at
 */
class BookingProduct extends \yii\db\ActiveRecord
{    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'booking_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'start_time', 'end_time', 'created_at', 'updated_at'], 'required'],
            [['id', 'is_disabled', 'start_time', 'end_time', 'min_fund', 'total_fund', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
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
