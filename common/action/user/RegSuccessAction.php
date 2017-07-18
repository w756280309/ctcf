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

        $affiliationId = 0;
        $userAffiliation = UserAffiliation::findOne(['user_id' => $user->id]);

        if (null !== $userAffiliation) {
            $affiliationId = $userAffiliation->affiliator_id;
        }

        $recommendAff = Affiliator::findAll(['isRecommend' => true]);
        $affArr = ArrayHelper::map($recommendAff, 'id', 'name');
        $affArr = ArrayHelper::merge([0 => '官方'], $affArr);

        return $this->controller->render('registerSucc', [
            'affiliationId' => $affiliationId,
            'affArr' => $affArr,
            'hasAffiliation' => !is_null($userAffiliation),
        ]);
    }
}