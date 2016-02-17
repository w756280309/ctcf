<?php

namespace P2pl;

/**
 * p2p配置类
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class ClientOption
{
    const BIND_NOTIFY_URL = "http://1.202.51.139:8001/user/qpay/notify/backend";
    const BIND_RET_URL = "http://1.202.51.139:8001/user/qpay/notify/frontend";
    const REC_NOTIFY_URL = "http://1.202.51.139:8001/user/qpay/qpaynotify/backend";
    const REC_RET_URL = "http://1.202.51.139:8001/user/qpay/qpaynotify/frontend";
    const ORDER_NOTIFY_URL = "http://1.202.51.139:8001/order/qpay/notify/backend";
    const ORDER_RET_URL = "http://1.202.51.139:8001/order/qpay/notify/frontend";
}
