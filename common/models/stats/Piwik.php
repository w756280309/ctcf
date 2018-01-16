<?php

namespace common\models\stats;


use GuzzleHttp\Client;
use yii\helpers\ArrayHelper;
use Yii;

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
        $authKey = Yii::$app->params['piwik_auth_key'];
        if ($clientType !== 'all') {
            throw new \Exception('目前只支持全部客户端数据查询');
        }
        $wapData = Piwik::request('https://d.wendujf.com/index.php?module=API&method=UserId.getUsers&idSite=2&period=day&date='.$date.'&format=JSON&token_auth='.$authKey.'&filter_limit=-1');
        $appData = Piwik::request('https://d.wendujf.com/index.php?module=API&method=UserId.getUsers&idSite=3&period=day&date='.$date.'&format=JSON&token_auth='.$authKey.'&filter_limit=-1');
        $pcData = Piwik::request('https://d.wendujf.com/index.php?module=API&method=UserId.getUsers&idSite=4&period=day&date='.$date.'&format=JSON&token_auth='.$authKey.'&filter_limit=-1');
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
    //获取温都金服m端，wap端及pc端各渠道的名称及用户访问量,
    public static function getChannelUserNum($startDate, $endDate)
    {
        $authKey = Yii::$app->params['piwik_auth_key'];
        $wapData = Piwik::request('https://d.wendujf.com/index.php?module=API&action=index&idSite=2&period=day&idSite=2&method=Referrers.getAll&showColumns=nb_visits&period=range&date='.$startDate.','.$endDate . '&format=json&token_auth='.$authKey.'&filter_limit=-1');
        $appData = Piwik::request('https://d.wendujf.com/index.php?module=API&action=index&idSite=3&period=day&idSite=2&method=Referrers.getAll&showColumns=nb_visits&period=range&date='.$startDate.','.$endDate . '&format=json&token_auth='.$authKey.'&filter_limit=-1');
        $pcData = Piwik::request('https://d.wendujf.com/index.php?module=API&action=index&idSite=4&period=day&idSite=2&method=Referrers.getAll&showColumns=nb_visits&period=range&date='.$startDate.','.$endDate . '&format=json&token_auth='.$authKey.'&filter_limit=-1');
        $allKeys = [];
        if (!empty($wapData)) {
            $wapData = ArrayHelper::index($wapData, 'label');
            $allKeys = array_keys($wapData);
        }
        if (!empty($appData)) {
            $appData = ArrayHelper::index($appData, 'label');
            foreach ($appData as $appKey => $appValue) {
                if (in_array($appKey, $allKeys)) {
                    $wapData[$appKey]['nb_visits'] += $appValue['nb_visits'];
                } else {
                    $wapData[$appKey] = $appValue;
                }
            }
        }
        if (!$pcData) {
            $pcData = ArrayHelper::index($pcData, 'label');
            foreach ($pcData as $pcKey => $pcValue) {
                if (in_array($pcKey, $allKeys)) {
                    $wapData[$pcKey]['nb_visits'] += $pcValue['nb_visits'];
                } else {
                    $wapData[$pcKey] = $pcValue;
                }
            }
        }
        return $wapData;
    }
}