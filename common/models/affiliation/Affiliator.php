<?php

namespace common\models\affiliation;

use common\models\code\GoodsType;
use yii\db\ActiveRecord;
use yii\web\Request;

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
     * @return array
     */
    public function getCampaigns()
    {
        return $this->hasMany(AffiliateCampaign::className(), ['affiliator_id' => 'id']);
    }
    /**
     * 获取分销商下的渠道码
     * @return object
     */
    public function getCampaign()
    {
        return $this->hasOne(AffiliateCampaign::className(), ['affiliator_id' => 'id']);
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

    /**
     * 判断是否来自宁波及其子分销商(首页和签到页面会根据渠道码判断), 分销商渠道码不会经常变动, 缓存一天
     */
    public static function isFromNb(Request $request)
    {
        $fromNb = false;//来自宁波分销商
        if (!empty($request->get('hmsr'))) {
            $source = $request->get('hmsr');
        }
        if (!empty($request->get('utm_source'))) {
            $source = $request->get('utm_source');
        }
        if (!empty($request->get('trk_s'))) {
            $source = $request->get('trk_s');
        }
        if (empty($source) && $request->getCookies()->get('campaign_source')) {
            $source = $request->getCookies()->get('campaign_source');
        }
        if (\Yii::$app->cache->get('nb_affiliator_codes')) {
            $nbCodes = \Yii::$app->cache->get('nb_affiliator_codes');
        } else {
            $nbCodes = self::getAllCodesByCode('nbxdjb');
            \Yii::$app->cache->set('nb_affiliator_codes', $nbCodes, 60 * 60 * 24);
        }

        if (!empty($source) && in_array($source, $nbCodes)) {
            $fromNb = true;
        }
        return $fromNb;
    }

    //根据一个分销商的渠道码,获取该分销商及其子分销商的渠道码
    public static function getAllCodesByCode($trackCode)
    {
        $affiliatorIds = self::getAllIdByCode($trackCode);
        if (empty($affiliatorIds)) {
            return [];
        }
        $codes = AffiliateCampaign::find()
            ->select('trackCode')
            ->where(['affiliator_id' => $affiliatorIds])
            ->orderBy(['affiliator_id' => SORT_ASC])
            ->column();
        return $codes;
    }

    /**
     * 根据分销商渠道码获取该分销商ID和其所有子分销商ID
     */
    public static function getAllIdByCode($trackCode)
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
        $affiliatorIds = [$affiliator->id];
        $childIds = Affiliator::find()->select(['id'])->where(['parentId' => $affiliator->id])->column();
        if (!empty($childIds)) {
            $affiliatorIds = array_merge($childIds, $affiliatorIds);
        }

        return $affiliatorIds;
    }

    /**
     * 按 key => value  返回所有分销商
     * ['id' => ''name]
     */
    public static function allAffiliators()
    {
        $array = self::find()
            ->select(['id', 'name'])
            ->where(['isDel' => false])
            ->asArray()
            ->all();
        $affiliators = [];
        foreach ($array as $v) {
            $affiliators[$v['id']] = $v['name'];
        }
        return $affiliators;
    }
}
