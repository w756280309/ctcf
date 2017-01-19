<?php

namespace wap\modules\mall\controllers;

use common\controllers\HelpersTrait;
use common\models\mall\ThirdPartyConnect;
use yii\web\Controller;

class PortalController extends Controller
{
    use HelpersTrait;

    //积分商城首页
    public function actionIndex()
    {
        $url = ThirdPartyConnect::generateLoginUrl();
        return $this->redirect($url);
    }

    /**
     * 进入积分商城,可以游客进入
     * @param string $dbredirect 兑吧商城内部地址（兑吧默认参数名）
     */
    public function actionGuest($dbredirect = '')
    {
        $allowGuest = true;
        if (defined('IN_APP') && IN_APP) {
            //APP不能以游客身份进入积分商城，跳转到登录页(暂时)
            $allowGuest = false;
        }
        $url = ThirdPartyConnect::generateLoginUrl($dbredirect, $allowGuest);
        return $this->redirect($url);
    }

    //转跳到兑吧的兑换记录
    public function actionRecord()
    {
        $dbredirect = rtrim(\Yii::$app->params['mall_settings']['url'], '/') . '/crecord/record';
        $url = ThirdPartyConnect::generateLoginUrl($dbredirect);
        return $this->redirect($url);
    }
}