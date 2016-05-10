<?php

namespace common\models\affiliation;

use common\models\order\OnlineOrder as Order;
use common\models\user\User;

/**
 * 分销管理.
 *
 * @author zhanghongyu<zhanghongyu@wangcaigu.com>
 */
class AffiliationManager
{
    /**
     * @param string $code
     * @param obj    $obj  User || Order
     */
    public function log($code, $obj)
    {
        $campaign = AffiliateCampaign::findOne(['trackCode' => $code]);
        if (null === $campaign) {
            return false;
        }
        if ($obj instanceof User) {
            (new UserAffiliation([
                'user_id' => $obj->id,
                'trackCode' => $code,
                'affiliator_id' => $campaign->affiliator_id,
            ]))->save();
        } elseif ($obj instanceof Order) {
            (new OrderAffiliation([
                'order_id' => $obj->id,
                'trackCode' => $code,
                'affiliator_id' => $campaign->affiliator_id,
            ]))->save();
        }
    }
}
