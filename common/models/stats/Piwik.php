<?php

namespace common\models\stats;


use GuzzleHttp\Client;

class Piwik
{
    public static function request($apiUrl)
    {
        $client = new Client([
            'allow_redirects' => false,
            'connect_timeout' => 30,
            'timeout' => 30,
        ]);

        $response = $client->request('GET', $apiUrl);
        if (200 !== $response->getStatusCode()) {
            return [];
        }
        $content = $response->getBody()->getContents();
        return json_decode($content, true);
    }

    //获取访问温都系统的用户ID
    public static function getVisitorId($date, $clientType = 'all')
    {
        if ($clientType !== 'all') {
            throw new \Exception('目前只支持全部客户端数据查询');
        }
        $wapData = Piwik::request('https://d.wendujf.com/index.php?module=API&method=UserId.getUsers&idSite=2&period=day&date='.$date.'&format=JSON&token_auth=621d83856c7018f96302c2d4101b47ee&filter_limit=-1');
        $appData = Piwik::request('https://d.wendujf.com/index.php?module=API&method=UserId.getUsers&idSite=3&period=day&date='.$date.'&format=JSON&token_auth=621d83856c7018f96302c2d4101b47ee&filter_limit=-1');
        $pcData = Piwik::request('https://d.wendujf.com/index.php?module=API&method=UserId.getUsers&idSite=4&period=day&date='.$date.'&format=JSON&token_auth=621d83856c7018f96302c2d4101b47ee&filter_limit=-1');
        $allData = [];

        if (!empty($wapData)) {
            $ids = array_column($wapData, 'label');
            $allData = array_merge($allData, $ids);
        }

        if (!empty($appData)) {
            $ids = array_column($appData, 'label');
            $allData = array_merge($allData, $ids);
        }

        if (!empty($pcData)) {
            $ids = array_column($pcData, 'label');
            $allData = array_merge($allData, $ids);
        }

        return array_unique($allData);
    }
}