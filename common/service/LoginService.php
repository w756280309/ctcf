<?php

namespace common\service;

use common\models\log\LoginLog;

/**
 * Desc 主要用于对用户登陆失败的情况做日志处理
 * Created by Pingter.
 * User: Pingter
 * Date: 15-12-23
 * Time: 下午4:02
 */
class LoginService
{
    /**
     * 添加登录失败时的日志信息.
     *
     * @param string $loginName 登录用户名
     * @param int    $type      登录渠道
     */
    public function logFailure($loginName, $type, $status = LoginLog::STATUS_ERROR)
    {
        $log = new LoginLog([
            'ip' => $this->getRealIp(),
            'type' => $type,
            'user_name' => $loginName,
            'status' => $status,
        ]);

        $log->save();
    }

    /**
     * 检查登录日志表，判断是否为10分钟内累计登录失败次数为大于3次的情况，如果是，返回true.
     *
     * @param string $loginName 登录用户名
     *
     * @return boolean
     */
    public function isCaptchaRequired($loginName = null)
    {
        $query = LoginLog::find()
            ->where(['ip' => $this->getRealIp()]);

        if ($loginName) {
            $query->orWhere(['user_name' => $loginName]);
        }

        $failLoginNum = $query
            ->andWhere(['status' => LoginLog::STATUS_ERROR])
            ->andWhere(['>', 'created_at', time() - 10 * 60])
            ->count();

        return $failLoginNum >= 3;
    }

    private function getRealIp()
    {
        foreach (['HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $name) {
            if (isset($_SERVER[$name])) {
                return $_SERVER[$name];
            }
        }

        return null;
    }
}
