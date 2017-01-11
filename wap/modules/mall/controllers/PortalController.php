<?php

namespace wap\modules\mall\controllers;

use common\controllers\HelpersTrait;
use common\models\mall\ThirdPartyConnect;
use yii\web\Controller;

class PortalController extends Controller
{
    use HelpersTrait;

    /**
     * 进入积分商城,可以游客进入
     * @param string $dbredirect    兑吧商城内部地址（兑吧默认参数名）
     */
    public function actionIndex($dbredirect = '')
    {
        $this->login($dbredirect, true);
    }

    //转跳到兑吧的兑换记录
    public function actionRecord()
    {
        $this->login(rtrim(\Yii::$app->params['mall_settings']['url'], '/') . '/crecord/record');
    }

    /**
     * 兑吧免登
     * @param string $dbredirect 兑吧商城内部地址（兑吧默认参数名）
     * @param bool $allowGuest 是否允许游客访问
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    private function login($dbredirect = '', $allowGuest = false)
    {
        $user = $this->getAuthedUser();
        if (!empty($user)) {
            $thirdPartyConnect = ThirdPartyConnect::findOne(['user_id' => $user->getId()]);
            if (empty($thirdPartyConnect)) {
                $thirdPartyConnect = ThirdPartyConnect::initnew($user);
                $thirdPartyConnect->save();
            }
        } else {
            if (!$allowGuest) {
                return $this->redirect('/site/login');
            }
        }

        $url = ThirdPartyConnect::buildCreditAutoLoginRequest(
            \Yii::$app->params['mall_settings']['app_key'],
            \Yii::$app->params['mall_settings']['app_secret'],
            empty($thirdPartyConnect) ? 'not_login' : $thirdPartyConnect->publicId,
            empty($user) ? 0 : $user->points,
            $dbredirect
        );
        return $this->redirect($url);
    }
}