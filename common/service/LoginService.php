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
class LoginService {

    /**
     * 添加登陆失败时的日志信息
     * @param $request 请求对象 $loginId 登陆用户名 $type 登陆渠道
     */
    public function logFailure($request, $loginId, $type) {
        $log = new LoginLog([
            'ip' => $this->getRealIp(),
            'type' => $type,
            'user_name' => $loginId
        ]);

        $log->save();
    }

    /**
     * 检查登陆日志表，判断是否为$seconds(秒)内累计登陆失败次数为大于$count(次)的情况，如果是，返回true
     * @param $request 请求对象  $loginId 登陆用户名 $seconds 限定分钟数 $count 登陆失败次数
     * @return boolean
     */
    public function isCaptchaRequired($request, $loginId, $seconds, $count) {
        $start_time = time() - $seconds;
        $data = LoginLog::find()->where(['ip' => $this->getRealIp()])->orWhere(['user_name' => $loginId]);
        $num = $data->andFilterWhere(['>','created_at',$start_time])->count();
        return $num>=$count;
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
