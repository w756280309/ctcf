<?php

namespace common\components;


use yii\web\Request;

/**
 * 自定义Request对象
 * Class WebRequest
 * @package common\components
 */
class WebRequest extends Request
{

    /**
     * 判断是否从站外地址跳到本站
     * @return bool
     */
    public function isFromOutSite()
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
     * 判断是否来自信任站点
     * @return bool
     */
    public function isFromTrustSite()
    {
        if ($this->referrer) {
            $hosts = [
                'activity.m.duiba.com.cn',//兑吧活动
                'www.duiba.com.cn',//兑吧商城
                'home.m.duiba.com.cn',//兑吧首页
                'goods.m.duiba.com.cn',//兑吧商品页面
            ];

            $referHost = parse_url($this->referrer, PHP_URL_HOST);
            return in_array($referHost, $hosts);
        }

        return false;
    }

    /**
     * 判断是否来自APP的请求
     * @return bool
     */
    public function isFromApp()
    {
        return boolval(strpos($this->hostInfo, '//app.'));
    }

    /**
     * 判断请求是否来自 Android
     * @return bool
     */
    public function isFromAndroid()
    {
        return $this->isFromApp() && false !== strpos($this->userAgent, 'WjfAndroid');
    }

    /**
     * 判断请求是否来自 Ios
     * @return bool
     */
    public function isFromIos()
    {
        return $this->isFromApp() && false !== strpos($this->userAgent, 'WjfApple');
    }
}