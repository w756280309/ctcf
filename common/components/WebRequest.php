<?php

namespace common\components;


use yii\web\Request;

/**
 * 自定义Request对象
 * Class WebRequest
 * @package common\components
 * @property bool $fromApp          判断请求是否来自App
 * @property bool $fromOutSite      是否从站外地址进入网站
 */
class WebRequest extends Request
{

    /**
     * 判断是否从站外地址跳到本站
     * @return bool
     */
    public function getFromOutSite()
    {
        if ($this->referrer) {
            $hosts = [
                parse_url(\Yii::$app->params['clientOption']['host']['api'], PHP_URL_HOST),
                parse_url(\Yii::$app->params['clientOption']['host']['frontend'], PHP_URL_HOST),
                parse_url(\Yii::$app->params['clientOption']['host']['wap'], PHP_URL_HOST),
                parse_url(\Yii::$app->params['clientOption']['host']['app'], PHP_URL_HOST),
                parse_url(\Yii::$app->params['clientOption']['host']['tx'], PHP_URL_HOST),
                parse_url(\Yii::$app->params['clientOption']['host']['tx_www'], PHP_URL_HOST),
            ];

            $referHost = parse_url($this->referrer, PHP_URL_HOST);
            return !in_array($referHost, $hosts);
        }

        return false;
    }

    /**
     * 判断是否来自APP的请求
     * @return bool
     */
    public function getFromApp()
    {
        return boolval(strpos($this->hostInfo, '//app.'));
    }
}