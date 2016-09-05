<?php

namespace common\models\affiliation;

use common\models\affiliation\Affiliator;
use yii\db\ActiveRecord;

/**
 * 分销用户.
 *
 * @author zhanghongyu<zhanghongyu@wangcaigu.com>
 */
class UserAffiliation extends ActiveRecord
{
    /**
     * 获取对应的分销商信息.
     */
    public function getAffiliator()
    {
        return $this->hasOne(Affiliator::className(), ['id' => 'affiliator_id']);
    }
}