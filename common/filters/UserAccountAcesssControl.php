<?php

namespace common\filters;

use common\models\app\AccessToken;
use Yii;
use yii\base\ActionFilter;

class UserAccountAcesssControl extends ActionFilter
{
    /**
     * 1. APP端每天最多延长一次token的有效期30天;
     * 2. 如果不是APP且用户已登录,但是用户状态为已锁定,则跳转到锁定用户页面;
     */
    public function beforeAction($action)
    {
        if (defined('IN_APP')) {
            $token = Yii::$app->request->get('token');

            if ($token) {
                $accessToken = AccessToken::isEffectiveToken($token);
                if (false !== $accessToken) {
                    Yii::$app->user->setIdentity($accessToken->user);
                    if (date('Y-m-d') !== substr($accessToken->updateTime, 0, 10)) {
                        $accessToken->expireTime = strtotime('+30 day');//延长有效期30天
                        $accessToken->updateTime = date('Y-m-d H:i:s');
                        $accessToken->save(false);
                    }
                }
            }
        } else {
            $user = Yii::$app->user->identity;

            if (null !== $user && 0 === $user->status) {
                Yii::$app->user->logout();

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->statusCode = 400;

                    echo json_encode([
                        'code' => 1,
                        'message' => '当前用户已锁定',
                        'tourl' => '/site/usererror',
                    ]);

                    return false;
                }

                return Yii::$app->getResponse()->redirect('/site/usererror')->send();
            }
        }

        return true;
    }
}
