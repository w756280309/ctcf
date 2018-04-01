<?php

$this->title = '踏青季送积分';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180329/css/index.min.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>

<div class="flex-content" id="app">
    <img class="banner" src="<?= FE_BASE_URI ?>wap/campaigns/active20180329/images/banner@2x.png" onclick="return;"
         alt="">
    <div class="part_01">
        <img class="regular" src="<?= FE_BASE_URI ?>wap/campaigns/active20180329/images/regular.png" onclick="return;"
             alt="">
        <!--金额后台直接渲染-->
        <div class="invest-amount">已累计年化：<?= rtrim(rtrim(bcdiv($amount, 10000, 2), '0'), '.') ?>万元</div>
        <div @click="goInvest" class="go-invest"></div>
    </div>
    <div class="part_02">
        <img class="gifts" src="<?= FE_BASE_URI ?>wap/campaigns/active20180329/images/gifts.png" onclick="return;"
             alt="">
        <a class="gift-btn btn1" href="/site/app-download?redirect=/mall/portal/guest?dbredirect=<?= urlencode('/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=913535') ?>"></a>
        <a class="gift-btn btn2" href="/site/app-download?redirect=/mall/portal/guest?dbredirect=<?= urlencode('/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1426624') ?>"></a>
        <a class="gift-btn btn3"
           href="/site/app-download?redirect=/mall/portal/guest?dbredirect=<?= urlencode('/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1362474') ?>"></a>
        <a class="gift-btn btn4"
           href="/site/app-download?redirect=/mall/portal/guest?dbredirect=<?= urlencode('/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1362363') ?>   "></a>
        <a class="gift-btn btn5"
           href="/site/app-download?redirect=/mall/portal/guest?dbredirect=<?= urlencode('/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1476629') ?>"></a>
        <a class="gift-btn btn6"
           href="/site/app-download?redirect=/mall/portal/guest?dbredirect=<?= urlencode('/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1362486') ?>"></a>
    </div>
    <div class="part_03"></div>
    <div class="part_04"><img class="btm" src="<?= FE_BASE_URI ?>wap/campaigns/active20180329/images/btm.png" onclick="return;" alt=""></div>
</div>
<script>
    FastClick.attach(document.body);
    var app = new Vue({
        el: '#app',
        data: {
            promoStatus: dataJson.promoStatus
        },
        methods: {
            goInvest: function () {
                var that = this;
                switch (that.promoStatus) {
                    case 0:
                        location.href = '/deal/deal/index';
                        break;
                    case 1:
                        that.toastCenter('活动未开始');
                        break;
                    case 2:
                        that.toastCenter('活动已结束');
                        break;
                    default:
                        break;
                }
            },
            toastCenter: function (val, active) {
                var $alert = $('<div class="error-info" style="display: block; position: fixed;font-size: .4rem;"><div>' + val + '</div></div>');
                $('body').append($alert);
                $alert.find('div').width($alert.width());
                setTimeout(function () {
                    $alert.fadeOut();
                    setTimeout(function () {
                        $alert.remove();
                    }, 200);
                    if (active) {
                        active();
                    }
                }, 2000);
            }
        }
    });
</script>
<script>
    (function () {
        if (typeof(WeixinJSBridge) == "undefined") {
            document.addEventListener("WeixinJSBridgeReady", function (e) {
                setTimeout(function () {
                    WeixinJSBridge.invoke('setFontSizeCallback', {"fontSize": 0}, function (res) {
                    });
                }, 0);
            });
        } else {
            setTimeout(function () {
                WeixinJSBridge.invoke('setFontSizeCallback', {"fontSize": 0}, function (res) {
                });
            }, 0);
        }
    })();
</script>