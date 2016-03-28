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
        return 'AccessToken';
    }

    public static function initToken(User $user, \yii\web\HeaderCollection $headers)
    {
        return new self([
            'uid' => $user->id,
            'expireTime' => strtotime('+30 day'), //30天
            'token' => \Yii::$app->getSecurity()->generateRandomString(),
            'clientType' => $headers['clienttype'],
            'deviceName' => $headers['devicename'],
            'clientInfo' => $headers['clientinfo'],
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
     * @param array $headers
     *                       return accesstoken
     */
    public static function isEffectiveToken(array $headers)
    {
        $accessToken = self::findOne(['token' => $headers['wjftoken']]);
        if (null === $accessToken) {
            return false;//无效的token
        }
        if ($accessToken->expireTime < time()) {
            return false;//token失效
        }

        return $accessToken;
    }
}
