<?php

namespace common\models\affiliation;

use yii\db\ActiveRecord;

/**
 * 分销商.
 *
 * @author zhanghongyu<zhanghongyu@wangcaigu.com>
 */
class AffiliateCampaign extends ActiveRecord
{
    public function getAffiliator()
    {
        return $this->hasOne(Affiliator::className(), ['id' => 'affiliator_id']);
    }
}
