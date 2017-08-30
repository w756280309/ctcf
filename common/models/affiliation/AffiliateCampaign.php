<?php

namespace common\models\affiliation;

use yii\db\ActiveRecord;

/**
 * Class AffiliateCampaign
 * @package common\models\affiliation
 *
 * @property integer $id
 * @property string  $trackCode     渠道码
 * @property string  $affiliator_id 分销商ID
 */
class AffiliateCampaign extends ActiveRecord
{
    public function getAffiliator()
    {
        return $this->hasOne(Affiliator::className(), ['id' => 'affiliator_id']);
    }
}
