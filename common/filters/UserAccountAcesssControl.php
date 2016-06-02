<?php

namespace common\filters;

use Yii;
use yii\base\ActionFilter;
use common\models\app\AccessToken;

class UserAccountAcesssControl extends ActionFilter
{
    public function beforeAction($action)
    {
        if (!defined('IN_APP') && null !== Yii::$app->user->identity && 0 === Yii::$app->user->identity->status) {
            return Yii::$app->getResponse()->redirect('/site/usererror');
        }
        if (defined('IN_APP') && Yii::$app->request->get('token')) {
            $accessToken = AccessToken::isEffectiveToken(Yii::$app->request->get('token'));
            if (false !== $accessToken) {
                Yii::$app->user->setIdentity($accessToken->user);
                if (date('Ymd', strtotime('+30 day')) == date('Ymd', $accessToken->expireTime)) {
                    //同一天不用更新
                    $accessToken->expireTime = strtotime('+30 day');//延长有效期30天
                    $accessToken->save(false);
                }
            }
        }

        return true;
    }
}
