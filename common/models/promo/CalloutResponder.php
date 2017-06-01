<?php

namespace common\models\promo;

use yii\db\ActiveRecord;

class CalloutResponder extends ActiveRecord
{
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'openid' => '用户身份识别ID',
            'callout_id' => '召集ID',
            'ip' => 'IP地址',
            'createTime' => '创建时间',
        ];
    }
}
