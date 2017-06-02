<?php

namespace common\models\promo;

use common\models\user\User;
use yii\db\ActiveRecord;

class Callout extends ActiveRecord
{
    public static function tableName()
    {
        return 'callout';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '召集用户',
            'endTime' => '召集截止时间',
            'responderCount' => '响应次数',
            'promo_id' => '参与活动ID',
            'createTime' => '创建时间',
        ];
    }

    public static function initNew(User $user, \DateTime $endTime, $promo_id)
    {
        return new self([
            'promo_id' => $promo_id,
            'endTime' => $endTime->format('Y-m-d H:i:s'),
            'responderCount' => 0,
            'user_id' => $user->id,
            'createTime' => date('Y-m-d H:i:s'),
        ]);
    }
}
