<?php

namespace Lhjx\Http;

final class HttpUtils
{
    /**
     * 根据$_SERVER里的USER AGENT信息判断是否是微信内置浏览器的访问
     *
     * @return bool
     */
    public static function isWeixinRequest()
    {
        return $_SERVER["HTTP_USER_AGENT"] && false !== strpos($_SERVER["HTTP_USER_AGENT"], 'MicroMessenger');
    }
}
