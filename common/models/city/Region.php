<?php

namespace common\models\city;

use Yii;

/**
 * This is the model class for table "region".
 *
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property integer $province_id
 * @property integer $city_id
 * @property integer $show_order
 */
class Region extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'region';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'name', 'province_id', 'city_id', 'show_order'], 'required'],
            [['province_id', 'city_id', 'show_order'], 'integer'],
            [['code'], 'string', 'max' => 64],
            [['name'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '区域ID',
            'code' => '代码',
            'name' => '名称',
            'province_id' => '所属省ID（0不存在）',
            'city_id' => '所属市ID（0不存在）',
            'show_order' => '显示顺序',
        ];
    }
}
