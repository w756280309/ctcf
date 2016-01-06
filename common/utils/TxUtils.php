<?php

namespace common\utils;

final class TxUtils
{
    public static function generateSn($prefix = '')
    {
        list($sec, $usec) = explode('.', sprintf('%.6f', microtime(true)));

        return $prefix
            .date('ymdHis', $sec)
            .$usec
            .mt_rand(1000, 9999);
    }
}
