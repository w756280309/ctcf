<?php

namespace wap\modules\mall\controllers;

use app\controllers\BaseController;
use common\models\mall\ThirdPartyConnect;

class PortalController extends BaseController
{
    public function actionIndex()
    {
        if (!\Yii::$app->params['mall_enabled']) {
            throw $this->ex404();
        }
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
            \Yii::$app->params['mall_settings']['appKey'],
            \Yii::$app->params['mall_settings']['appSecret'],
            $thirdPartyConnect->publicId,
            10000
        );
        return $this->redirect($url);
    }
}