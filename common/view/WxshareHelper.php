<?php

namespace common\view;

use common\models\adv\Share;
use Yii;
use yii\web\View;

class WxshareHelper
{
    public static function registerTo($viewObj, $share)
    {
        $appId = Yii::$app->params['weixin']['appId'];
        $feBaseUri = FE_BASE_URI;

        if (empty($share) || !($share instanceof Share) || empty($appId)) {
            return;
        }

        $_js = <<<JS
$(function() {
    closeShare();

    $('.share').on('click', function() {
        var host = window.location.host.toLocaleLowerCase();

        //判断域名,用以区别是否是app内嵌网页
        if(host.substr(0,4)==='app.') {
            if (shareCallBack) {
                shareCallBack();
            }
            //分享四要素(标题+描述+链接地址+图标地址)
            var shareObj = {title: title, des: desc, linkurl: linkUrl, thumurl: imgUrl};
            if(browser.versions.ios || browser.versions.iPad || browser.versions.iPhone) {
                //苹果设备
                window.webkit.messageHandlers.share.postMessage(shareObj);
            } else if(browser.versions.android) {
                //android 设备,四个参数位置不可颠倒
                window.shareAction.share(title, desc, linkUrl, imgUrl);
            } else {
                //其它
                popShare();
            }
        } else {
            popShare();
        }
    });
});

//判断终端类型(ios/android)
var browser={
    versions:function() {
        var u = navigator.userAgent, app = navigator.appVersion;
        return {//移动终端浏览器版本信息
            trident: u.indexOf('Trident') > -1, //IE内核
            presto: u.indexOf('Presto') > -1, //opera内核
            webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
            gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1, //火狐内核
            mobile: !!u.match(/AppleWebKit.*Mobile.*/)||!!u.match(/AppleWebKit/), //是否为移动终端
            ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
            android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1, //android终端或者uc浏览器
            iPhone: u.indexOf('iPhone') > -1 || u.indexOf('Mac') > -1, //是否为iPhone或者QQHD浏览器
            iPad: u.indexOf('iPad') > -1, //是否iPad
            webApp: u.indexOf('Safari') == -1, //是否web应该程序，没有头部与底部
            weiXin: u.indexOf('MicroMessenger') > -1 ,
            weibo: u.indexOf('Weibo') > -1 ,
            qq: u.indexOf('QQ/') > -1 && (!!u.match(/AppleWebKit.*Mobile.*/)||!!u.match(/AppleWebKit/))
        };
    }(),
    language:(navigator.browserLanguage || navigator.language).toLowerCase()
};

$.get("/weixin/auth", {
    appId: 1,
    url: location.protocol+'\/\/'+location.hostname+location.pathname+location.search
}, function (data) {
    wx.config({
        //debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: '$appId', // 必填，公众号的唯一标识
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
            success: function () {
                if (shareCallBack) {
                    shareCallBack();
                }
            },
        });
    });

    wx.error(function(res){
        console.log(res);
    });
});
    
function closeShare() {
    $('body').on('click',function(e) {
        if($(e.target).hasClass('share-box') || $(e.target).hasClass('share-box-img')){
            $('.share-box').hide().remove();
            $('.mark-box').hide().remove();
        }
    });
}

function popShare() {
    var html = '<div class="mark-box" style="display: none;position: fixed;left: 0;bottom:0;width: 100%;height: 100%;background: #000;opacity: 0.6;z-index: 11;" ></div>'
        +'<div class="share-box" style="display: none;position: fixed;left: 0;bottom:0;width: 100%;height: 100%;z-index: 12;text-align: right;">'
        +'<img class="share-box-img" style="float: right;width: 80%;" src="<?= $feBaseUri ?>wap/campaigns/active20170711/img/weixinShare.png" alt="">'
        +'</div>';

    $('body').prepend(html);
    $('.mark-box').show();
    $('.share-box').show();
}
JS;
        $viewObj->registerJsFile('https://res.wx.qq.com/open/js/jweixin-1.0.0.js', ['position' => View::POS_END]);
        $viewObj->registerJs($_js, View::POS_END, 'body_close');
    }
}