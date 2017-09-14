<?php

namespace common\models\affiliation;

use common\models\code\GoodsType;
use yii\db\ActiveRecord;

/**
 * Class Affiliator
 * @package common\models\affiliation
 *
 * @property integer $id
 * @property string  $name        分销商名称
 * @property string  $picPath     分销商图片（250x750）
 * @property integer $isRecommend 是否推荐（在前台显示-注册成功页）
 * @property boolean $isO2O       是否为O2O
 * @property int     $parentId    父级分销商ID
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

    //根据一个分销商的渠道码,获取该分销商及其子分销商的渠道码, 缓存1天
    public static function getAllCodesByCode($trackCode)
    {
        if (empty($trackCode)) {
            return [];
        }
        $affiliateCampaign = AffiliateCampaign::find()->where(['trackCode' => $trackCode])->one();
        if (is_null($affiliateCampaign)) {
            return [];
        }
        $affiliator = $affiliateCampaign->affiliator;
        if (is_null($affiliator)) {
            return [];
        }
        //todo 添加缓存
        return $affiliator->getAllCodes();
    }

    public function getAllCodes()
    {
        $affiliatorIds = [$this->id];
        $childIds = Affiliator::find()->select(['id'])->where(['parentId' => $this->id])->column();
        if (!empty($childIds)) {
            $affiliatorIds = array_merge($childIds, $affiliatorIds);
        }
        $codes = AffiliateCampaign::find()
            ->select('trackCode')
            ->where(['affiliator_id' => $affiliatorIds])
            ->orderBy(['affiliator_id' => SORT_ASC])
            ->column();
        return $codes;
    }
}
