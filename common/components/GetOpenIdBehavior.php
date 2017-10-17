<?php

namespace common\components;

use common\controllers\HelpersTrait;
use common\models\thirdparty\SocialConnect;
use common\models\user\User;
use Yii;
use yii\base\Behavior;
use yii\web\Controller;

class GetOpenIdBehavior extends Behavior
{
    use HelpersTrait;

    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction',
        ];
    }

    /**
     * 微信绑定后,在微信端可以自动登录:
     *
     * 主要判断:
     * 1. 首先是在微信端打开；
     * 2. 当前没有登录；
     * 3. 非Ajax请求；
     * 4. 当前微信已绑定了温都金服账号；
     *
     * 满足以上条件,即可自动登录温都金服账号;
     */
    public function beforeAction()
    {
        $isWx = $this->fromWx();
        if (!$isWx || Yii::$app->request->isAjax) {
            return false;
        }

        //将open_id存储在session的resourceOwnerId字段
        //获取方式为Yii::$app->session->get('resourceOwnerId');
        if (!Yii::$app->session->has('resourceOwnerId') || !Yii::$app->session->has('resourceOwnerNickName')) {
            $wxClient = Yii::$container->get('wxClient');
            $getGrantState = bin2hex(random_bytes(8));
            Yii::$app->session->set('getGrantState', $getGrantState);
            $callbackUrl = Yii::$app->request->hostInfo . '/weixin/callback?redirect=' . urlencode(Yii::$app->request->absoluteUrl);
            $url = $wxClient->getAuthorizationUrl($callbackUrl, 'snsapi_userinfo', $getGrantState);
            return Yii::$app->controller->redirect($url);
        }

        $resourceOwnerId = Yii::$app->session->get('resourceOwnerId');

        if ($resourceOwnerId && Yii::$app->user->isGuest) {
            $social = SocialConnect::findOne([
                'resourceOwner_id' => $resourceOwnerId,
                'provider_type' => SocialConnect::PROVIDER_TYPE_WECHAT,
            ]);

            if (is_null($social)) {
                return false;
            }

            $user = User::findOne($social->user_id);

            if (is_null($user)) {
                return false;
            }

            Yii::$app->user->login($user);    //微信绑定,自动登录
        }
    }
}
