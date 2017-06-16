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

        $code = Yii::$app->request->get('code');
        $state = Yii::$app->request->get('state');

        //将open_id存储在session的resourceOwnerId字段
        //获取方式为Yii::$app->session->get('resourceOwnerId');
        $wxClient = Yii::$container->get('wxClient');
        if (!Yii::$app->session->has('resourceOwnerId')) {
            if ($code && $state) {
                try {
                    $response = $wxClient->getGrant($code);
                    Yii::$app->session->set('resourceOwnerId', $response['resource_owner_id']);
                } catch (\Exception $ex) {
                    throw $ex;
                }
            } else {
                $url = $wxClient->getAuthorizationUrl(Yii::$app->request->absoluteUrl, 'snsapi_userinfo', time());
                return Yii::$app->controller->redirect($url);
            }
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
