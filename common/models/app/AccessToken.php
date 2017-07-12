<?php

namespace common\models\app;

use common\models\user\User;

/**
 *  "AccessToken".
 */
class AccessToken extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'accesstoken';
    }

    public static function initToken(User $user)
    {
        return new self([
            'uid' => $user->id,
            'expireTime' => strtotime('+30 day'), //30天
            'token' => \Yii::$app->getSecurity()->generateRandomString(),
            'create_time' => time(),
        ]);
    }

    /**
     * 获取用户信息.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }

    /**
     * 获取是否是有效的token.
     *
     * @param string $token
     * return accesstoken
     */
    public static function isEffectiveToken($token)
    {
        $accessToken = self::findOne(['token' => $token]);
        if (null === $accessToken) {
            return false;//无效的token
        }
        if ($accessToken->expireTime < time()) {
            return false;//token失效
        }

        return $accessToken;
    }
}
