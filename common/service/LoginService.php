<?php
namespace common\service;

use Yii;
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
     * @return boolen
     */
    public function logFailure($request, $loginId, $type) {
        $log = new LoginLog([
            'ip' => $request->userId,
            'type' => $type,
            'user_name' => $loginId
        ]);
        
        return $log->save();
    }
   
    /**
     * 检查登陆日志表，判断是否为半小时内累计登陆失败次数为大于三的情况，如果是，返回true
     */
    public function checkLog() {
        $start_time = time() - 30 * 60;        
        $data = LoginLog::find()->where(['ip' => $this->ip])->orWhere(['user_name' => $this->user_name]);
        $num = $data->andFilterWhere(['>','created_at',$start_time])->count();
        return $num>3?TRUE:FALSE;
    }
    
    /*
     * 给user_name赋值
     */
//    public function setUsername($value) {
//        $this->user_name = $value;
//    }
}