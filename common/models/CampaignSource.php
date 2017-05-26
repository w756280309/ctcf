<?php

namespace common\models;

use common\models\order\OnlineOrder;
use common\models\user\User;

class CampaignSource
{
    /**
     * 统计指定渠道注册人数
     * @param $campaignSource 注册渠道码
     * @return int
     */
    public static function getRegistration($campaignSource)
    {
        return User::find(['campaign_source' => $campaignSource])->count();
    }
    /**
     * 统计指定渠道订单人数
     * @param $campaignSource 注册渠道码
     * @return int
     */
    public static function getInvestors($campaignSource)
    {
        return OnlineOrder::find(['campaign_source' => $campaignSource])->count();
    }
    /**
     * 统计指定渠道订单金额
     * @param $campaignSource 注册渠道码
     * @return string
     */
    public static function getInvestment($campaignSource)
    {
        return OnlineOrder::find(['campaign_source' => $campaignSource])->sum('order_money');
    }
}