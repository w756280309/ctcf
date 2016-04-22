<?php

namespace common\models\invite;

use yii\web\Request;

/**
 * 邀请好友.
 *
 * @author zhanghongyu<zhanghongyu@wangcaigu.com>
 */
class InviteHelper
{
    /**
     * 获取邀请码yqm.
     *
     * @param Request $request
     *
     * @return string || false
     */
    public static function extractToken(Request $request)
    {
        $yzm = $request->cookies->getValue('yqm');
        if (null === $yzm || '' === $yzm) {
            return false;
        }

        return $yzm;
    }

    /**
     * 根据邀请码获取邀请对象包含所属邀请人.
     *
     * @param string $token 邀请码
     *
     * @return Invite 对象
     */
    public static function findInviterByToken($token)
    {
        return Invite::findOne(['code' => $token]);
    }
}
