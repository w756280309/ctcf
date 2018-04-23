<?php

namespace api\modules\njq\controllers;

use common\models\user\User;
use yii\web\Controller;

class UserController extends Controller
{
    /**
     * 限制IP访问（目前为南金中心服务器）
     *
     * 获得用户的hash密码
     *
     * @params string $uid 用户mobile
     *
     * return null|string
     */
    public function actionGetPass($uid)
    {
        $passwordHash = null;
        $user = User::findByMobile($uid)->one();
        if (null !== $user) {
            $passwordHash = $user->password_hash;
        }

        return $passwordHash;
    }
}
