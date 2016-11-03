<?php

namespace common\models\offline;

use yii\db\ActiveRecord;
use common\models\affiliation\Affiliator;
use common\models\offline\OfflineLoan;

class OfflineOrder extends ActiveRecord
{
    public function rules()
    {
        return [
            [['affiliator_id', 'loan_id', 'realName', 'mobile', 'money', 'orderDate', 'created_at'], 'required'],
            [['affiliator_id', 'loan_id', 'created_at'], 'integer'],
            ['realName', 'string', 'max' => 50],
            ['mobile', 'string', 'max' => 20],
            ['money', 'number'],
            ['orderDate', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'affiliator_id' => '分销商ID',
            'loan_id' => '线下产品ID',
            'realName' => '姓名',
            'mobile' => '联系电话',
            'money' => '购买金额',
            'orderDate' => '订单日期',
            'created_at' => '创建时间',
            'isDeleted' => '是否删除',
        ];
    }

    public function getAffliator()
    {
        return $this->hasOne(Affiliator::className(), ['id' => 'affiliator_id']);
    }

    public function getLoan()
    {
        return $this->hasOne(OfflineLoan::className(), ['id' => 'loan_id']);
    }
}
