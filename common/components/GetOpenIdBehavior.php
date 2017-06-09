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
    }
}
