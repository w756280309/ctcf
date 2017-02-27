<?php

namespace common\utils;

use Wcg\Security\Aes;
use Yii;

/**
 * 加解密类
 */
class SecurityUtils
{
    /**
     * 获取用于加密用户信息的key
     * @throws \Exception
     * @return string
     */
    private static function getRandomKey()
    {
        $keyFile = Yii::$app->params['wdjf_security_key'];
        if (!file_exists($keyFile)) {
            throw new \Exception('用于加密用户信息的key不能为空');
        }
        $randomKey = file_get_contents($keyFile);
        if (empty($randomKey)) {
            throw new \Exception('用于加密用户信息的key不能为空');
        }
        return $randomKey;
    }

    /**
     * 加密
     * @param   string $plaintext 需要加密的明文
     * @return  string
     */
    public static function encrypt($plaintext)
    {
        return Aes::encrypt($plaintext, self::getRandomKey());
    }

    /**
     * 解密
     * @param   string $confidentialMessage 密文串
     * @return string
     */
    public static function decrypt($confidentialMessage)
    {
        return Aes::decrypt($confidentialMessage, self::getRandomKey());
    }
}