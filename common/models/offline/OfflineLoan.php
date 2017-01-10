<?php

namespace common\models\offline;

use yii\db\ActiveRecord;
use common\models\offline\OfflineOrder;

class OfflineLoan extends ActiveRecord
{
    public function rules()
    {
        return [
            [['title', 'expires', 'unit'], 'required'],
            ['title', 'string', 'max' => 255],
            ['unit', 'string', 'max' => 20],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '产品名称',
            'expires' => '产品期限',
            'unit' => '期限单位',
        ];
    }

    public function getOrder()
    {
        return $this->hasMany(OfflineOrder::className(), ['loan_id' => 'id']);
    }
}
