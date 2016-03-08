<?php

namespace common\models;

/**
 * 日志适配器.
 *
 * @author zhanghongyu<zhanghongyu@wangcaigu.com>
 */
class Logger implements \PayGate\Ump\LoggerInterface
{
    /**
     * @param array             $rqData 不包含签名的内容，可以为null
     * @param array             $rq     签名后的数据，可以为null
     * @param ResponseInterface $rp
     * @param $duration 记录同步请求响应时间
     * 说明:目前同步记录1开户；2建标；3标的更新；4标的转账；5 融资用户提现申请;6交易密码
     * return tradelog 对象
     */
    public function log($direction = 1, $rqData = null, $rq = null, $rp = null, $duration = 0)
    {
        $log = TradeLog::initLog($direction, $rqData, $rq, $rp, $duration);
        $log->save();
    }
}
