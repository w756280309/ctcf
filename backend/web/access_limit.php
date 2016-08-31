<?php

// 仅允许本地及192.168.*网段访问
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])
        || '192.168' === substr(@$_SERVER['REMOTE_ADDR'], 0, 7)
        || 'cli-server' === php_sapi_name()
    )
) {
    header('HTTP/1.0 403 Forbidden');
    exit();
}