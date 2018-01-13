<?php

namespace common\models\promo;

use common\models\user\User;
use common\utils\SecurityUtils;
use common\utils\StringUtils;
use yii\helpers\ArrayHelper;

class Promo180119 extends BasePromo
{
    private $showAwardTime = '2018-01-19 17:00:00';

    /**
     * 获得中奖的手机号并对手机号进行混淆处理
     */
    public function getAwardMobileList()
    {
        $awardMobileList = [];
        $currentTime = new \DateTime();

        $showAwardDateTime = new \DateTime($this->showAwardTime);
        $isShowAwardList = $currentTime >= $showAwardDateTime;
        if ($isShowAwardList) {
            $awardUserIds = Award::find()
                ->select('user_id')
                ->where(['promo_id' => $this->promo->id])
                ->column();

            if (!empty($awardUserIds)) {
                $awardMobileList = User::find()
                    ->select('safeMobile')
                    ->where(['in', 'id', $awardUserIds])
                    ->column();
                foreach ($awardMobileList as $k => $awardMobile) {
                    $awardMobileList[$k] = StringUtils::obfsLandlineNumber(SecurityUtils::decrypt($awardMobile));
                }
            }
        }

        return $awardMobileList;
    }

    /**
     * 根据上证指数获得最接近的用户注册信息
     */
    public function getUserInfoByCompositeIndex($index, $limitCount = 5)
    {
        $startTime = $this->promo->startTime;
        $userMobiles = [];

        //获得指定时间段的用户信息，并添加用于排序的字段
        $userMobilesInfo = User::find()
            ->select('id,safeMobile')
            ->where(['type' => User::USER_TYPE_PERSONAL])
            ->andWhere(['idcard_status' => User::IDCARD_STATUS_PASS])
            ->andFilterWhere(['>=', 'created_at', strtotime($startTime)])
            ->andFilterWhere(['<=', 'created_at', strtotime($this->promo->endTime)])
            ->asArray()
            ->all();
        foreach ($userMobilesInfo as $k => $mobileInfo) {
            $userMobiles[$k]['mobile'] = SecurityUtils::decrypt($mobileInfo['safeMobile']);
            $userMobiles[$k]['sort'] = abs(bcsub(substr($userMobiles[$k]['mobile'], -4), $index, 0));
            $userMobiles[$k]['id'] = $mobileInfo['id'];
        }

        //根据sort字段排序
        ArrayHelper::multisort($userMobiles, 'sort', SORT_ASC, SORT_NUMERIC);

        //获取指定的几个用户信息
        if (!empty($userMobiles)) {
            $userMobiles = array_slice($userMobiles, 0, $limitCount);
        }

        return $userMobiles;
    }
}
