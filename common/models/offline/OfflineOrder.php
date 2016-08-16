<?php

namespace common\models\offline;

use yii\db\ActiveRecord;
use common\models\salebranch\SaleBranch;
use common\models\offline\OfflineLoan;

class OfflineOrder extends ActiveRecord
{
    public function rules()
    {
        return [];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'branch_id' => '网点ID',
            'loan_id' => '线下产品ID',
            'realName' => '姓名',
            'mobile' => '联系电话',
            'money' => '购买金额',
            'orderDate' => '订单日期',
            'created_at' => '创建时间',
        ];
    }

    public function getBranch()
    {
        return $this->hasOne(SaleBranch::className(), ['id' => 'branch_id']);
    }

    public function getLoan()
    {
        return $this->hasOne(OfflineLoan::className(), ['id' => 'loan_id']);
    }
}
