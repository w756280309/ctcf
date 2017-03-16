<?php

namespace common\models\affiliation;

use common\models\code\GoodsType;
use yii\db\ActiveRecord;

/**
 * 分销商.
 *
 * @author zhanghongyu<zhanghongyu@wangcaigu.com>
 */
class Affiliator extends ActiveRecord
{
    public function getGoods()
    {
        return $this->hasMany(GoodsType::className(), ['affiliator_id' => 'id']);
    }
}
