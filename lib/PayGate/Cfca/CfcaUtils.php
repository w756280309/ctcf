<?php

namespace PayGate\Cfca;

final class CfcaUtils
{
    public static function generateSn($prefix = '')
    {
        list($sec, $usec) = explode('.', sprintf('%.6f', microtime(true)));

        return $prefix
            .date('ymdHis', $sec)
            .$usec
            .mt_rand(1000, 9999);
    }

    public static function renderXml($xml, array $data)
    {
        foreach ($data as $key => $value) {
            $xml = preg_replace(
                '/\\{\\{\\s*'.$key.'\\s*\\}\\}/',
                $value,
                $xml
            );
        }

        return trim($xml);
    }
}
