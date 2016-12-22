<?php

namespace wap\modules\mall\controllers;

use app\controllers\BaseController;
use common\models\mall\ThirdPartyConnect;

class PortalController extends BaseController
{
    public function actionIndex()
    {
        $user = $this->getAuthedUser();
        if (empty($user)) {
            throw $this->ex404();
        }
        $thirdPartyConnect = ThirdPartyConnect::findOne(['user_id' => $user->getId()]);
        if (empty($thirdPartyConnect)) {
            $thirdPartyConnect = ThirdPartyConnect::initnew($user);
            $thirdPartyConnect->save();
        }
        $url = ThirdPartyConnect::buildCreditAutoLoginRequest(
            \Yii::$app->params['mall_settings']['app_key'],
            \Yii::$app->params['mall_settings']['app_secret'],
            $thirdPartyConnect->publicId,
            $user->points
        );
        return $this->redirect($url);
    }
}