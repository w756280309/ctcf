<?php

namespace common\view;

use common\models\adv\Share;
use Yii;
use yii\web\View;

class WxshareHelper
{
    public static function registerTo($viewObj, $share)
    {
        if (empty($share) || !($share instanceof Share)) {
            return;
        }

        if (empty($share->url)) {
            $share->url = rtrim(Yii::$app->params['clientOption']['host']['wap'], '/').Yii::$app->request->url;
        }

        $_js = <<<JS
$.get("/weixin/auth", {
    appId: 1,
    url: location.protocol+'\/\/'+location.hostname+location.pathname+location.search
}, function (data) {
    wx.config({
        //debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: 'wxb93cb1e173a01341', // 必填，公众号的唯一标识
        timestamp: data.timestamp, // 必填，生成签名的时间戳
        nonceStr: data.nonceStr, // 必填，生成签名的随机串
        signature: data.signature,// 必填，签名，见附录1
        jsApiList: ['onMenuShareAppMessage', 'onMenuShareTimeline'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });

    wx.ready(function(){
        wx.onMenuShareAppMessage({
            title: '$share->title', // 分享标题
            desc: '$share->description', // 分享描述
            link: '$share->url', // 分享链接
            imgUrl: '$share->imgUrl', // 分享图标
        });
        wx.onMenuShareTimeline({
            title: '$share->title', // 分享标题
            link: '$share->url', // 分享链接
            imgUrl: '$share->imgUrl', // 分享图标
        });
    });

    wx.error(function(res){
        console.log(res);
    });
});
JS;
        $viewObj->registerJsFile('https://res.wx.qq.com/open/js/jweixin-1.0.0.js', ['position' => View::POS_END]);
        $viewObj->registerJs($_js, View::POS_END, 'body_close');
    }
}