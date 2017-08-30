<?php

namespace common\models\affiliation;

use common\models\code\GoodsType;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class Affiliator
 * @package common\models\affiliation
 *
 * @property integer $id
 * @property string  $name        分销商名称
 * @property string  $picPath     分销商图片（250x750）
 * @property integer $isRecommend 是否推荐（在前台显示-注册成功页）
 * @property boolean $isO2O       是否为O2O
 */
class Affiliator extends ActiveRecord
{
    public function getGoods()
    {
        return $this->hasMany(GoodsType::className(), ['affiliator_id' => 'id']);
    }

    /**
     * 获取分销商下的渠道码
     */
    public function getCampaigns()
    {
        return $this->hasMany(AffiliateCampaign::className(), ['affiliator_id' => 'id']);
    }

    /**
     * 判断当前某个渠道码是当前分销商所属的渠道码
     *
     * @param string $campaignSource 渠道码
     *
     * @return bool
     */
    public function isAffiliatorCampaign($campaignSource)
    {
        if (null === $campaignSource) {
            return false;
        }

        return null !== AffiliateCampaign::find()
                ->where(['trackCode' => $campaignSource])
                ->andWhere(['affiliator_id' => $this->id])
                ->one();
    }
}
