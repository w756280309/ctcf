<?php

namespace common\models\offline;

use yii\db\ActiveRecord;
use common\models\offline\OfflineOrder;

class OfflineLoan extends ActiveRecord
{
    public function rules()
    {
        return [
            ['title', 'required'],
            ['title', 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '产品名称',
        ];
    }

    public function getOrder()
    {
        return $this->hasMany(OfflineOrder::className(), ['loan_id' => 'id']);
    }
}
