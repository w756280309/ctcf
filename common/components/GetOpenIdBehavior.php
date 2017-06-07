<?php

namespace common\components;

use common\controllers\HelpersTrait;
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

    public function beforeAction()
    {
        $isWx = $this->fromWx();
        if (!$isWx) {
            return false;
        }

        //将open_id存储在session的resourceOwnerId里
        //获取方式为Yii::$app->session->get('resourceOwnerId');
        $wxClient = Yii::$container->get('wxClient');
        if (!Yii::$app->session->has('resourceOwnerId')) {
            $url = $wxClient->getAuthorizationUrl(Yii::$app->request->absoluteUrl, 'snsapi_userinfo', time());
            $code = Yii::$app->request->get('code');
            $state = (int) Yii::$app->request->get('state');
            if ($code && null !== $state) {
                try {
                    $response = $wxClient->getGrant($code);
                    Yii::$app->session->set('resourceOwnerId', $response['resource_owner_id']);
                } catch (\Exception $ex) {
                    throw $ex;
                }
            } else {
                //todo 防止重复重定向进入死循环
                return Yii::$app->controller->redirect($url);
            }
        }
    }
}
