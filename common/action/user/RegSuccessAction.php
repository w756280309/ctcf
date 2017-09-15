<?php

namespace common\action\user;

use common\models\affiliation\Affiliator;
use common\models\affiliation\UserAffiliation;
use yii\base\Action;
use yii\helpers\ArrayHelper;

class RegSuccessAction extends Action
{
    public function run()
    {
        $user = $this->controller->getAuthedUser();

        //如果是游客，跳转到首页
        if (null === $user) {
            return $this->controller->redirect('/?_mark='.time());
        }

        $affArr = [];
        $userAffiliation = UserAffiliation::findOne(['user_id' => $user->id]);
        //判断是否来自宁波渠道
        $fromNb = Affiliator::isFromNb(\Yii::$app->request);

        //没有分销商时候可以选推荐媒体
        if (null !== $userAffiliation && !$fromNb) {
            $recommendAff = Affiliator::findAll(['isRecommend' => true]);
            $affArr = ArrayHelper::map($recommendAff, 'id', 'name');
            $affArr = ArrayHelper::merge([0 => '官方'], $affArr);
        }

        if ($fromNb) {
            $affiliatorIds = Affiliator::getAllIdByCode('nbxdjb');
            if (!empty($affiliatorIds)) {
                $recommendAff = Affiliator::find()->where(['id' => $affiliatorIds])->orderBy(['id' => SORT_ASC])->all();
                $affArr = ArrayHelper::map($recommendAff, 'id', 'name');
            }
        }

        return $this->controller->render('registerSucc', [
            'affArr' => $affArr,
            'fromNb' => $fromNb,
            'affiliatorId' => !is_null($userAffiliation) ? $userAffiliation->affiliator_id : 0,
        ]);
    }
}