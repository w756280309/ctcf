<?php

namespace common\action\user;

use common\models\affiliation\Affiliator;
use common\models\affiliation\AffiliateCampaign;
use common\models\affiliation\UserAffiliation;
use Yii;
use yii\base\Action;

class AddAffiliatorAction extends Action
{
    public function run()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        $userId = $this->controller->getAuthedUser()->id;
        $id = (int) Yii::$app->request->get('id');

        $affiliator = Affiliator::findOne($id);
        $userAff = UserAffiliation::findOne(['user_id' => $userId]);
        $realUserAff = null !== $userAff ? $userAff : new UserAffiliation();

        if ($id <= 0 && null !== $realUserAff) {
            return (bool) $realUserAff->findOne(['user_id' => $userId])->delete();
        }

        if (null === $affiliator) {
            return false;
        }

        $realUserAff->trackCode = AffiliateCampaign::find()->select('trackCode')->where(['affiliator_id' => $id])->scalar();
        $realUserAff->affiliator_id = $id;
        $realUserAff->user_id = $userId;

        return $realUserAff->save();
    }
}