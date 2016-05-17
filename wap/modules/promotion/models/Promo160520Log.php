<?php

namespace wap\modules\promotion\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * 160520活动.
 *
 * @author zhanghongyu<zhanghongyu@wangcaigu.com>
 */
class Promo160520Log extends ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['mobile', 'prizeId', 'count'], 'required'],
        ];
    }
}
