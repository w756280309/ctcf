<?php

$this->title = '全民年货节';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180206/css/index.min.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<div class="flex-content" id="app">
    <img class="banner" src="<?= FE_BASE_URI ?>wap/campaigns/active20180206/images/banner_01.png" alt="">
    <img class="banner" src="<?= FE_BASE_URI ?>wap/campaigns/active20180206/images/banner_02.png" alt="">
    <img class="banner" src="<?= FE_BASE_URI ?>wap/campaigns/active20180206/images/banner_03.png" alt="">
    <img class="banner" src="<?= FE_BASE_URI ?>wap/campaigns/active20180206/images/banner_04.png" alt="">
    <img class="banner" src="<?= FE_BASE_URI ?>wap/campaigns/active20180206/images/banner_05.png" alt="">
    <img class="banner" src="<?= FE_BASE_URI ?>wap/campaigns/active20180206/images/banner_06.png" alt="">
    <img class="banner" src="<?= FE_BASE_URI ?>wap/campaigns/active20180206/images/banner_07.png" alt="">
    <img class="banner" src="<?= FE_BASE_URI ?>wap/campaigns/active20180206/images/banner_08.png" alt="">
    <div class="presents">
        <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https%3A%2F%2Fgoods.m.duiba.com.cn%2Fmobile%2FappItemDetail%3FappItemId%3D1790825"></a>
        <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https%3A%2F%2Fgoods.m.duiba.com.cn%2Fmobile%2FappItemDetail%3FappItemId%3D1790741"></a>
        <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https%3A%2F%2Fgoods.m.duiba.com.cn%2Fmobile%2FappItemDetail%3FappItemId%3D1790674"></a>
        <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https%3A%2F%2Fgoods.m.duiba.com.cn%2Fmobile%2FappItemDetail%3FappItemId%3D1790645"></a>
        <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https%3A%2F%2Fgoods.m.duiba.com.cn%2Fmobile%2FappItemDetail%3FappItemId%3D1769911"></a>
        <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https%3A%2F%2Fgoods.m.duiba.com.cn%2Fmobile%2FappItemDetail%3FappItemId%3D1790625"></a>
        <div class="invest" @click="goInvest"></div>
    </div>
</div>
<script>
    FastClick.attach(document.body);
    var promoStatus = $("input[name='promoStatus']").val();
    var app = new Vue({
        el: '#app',
        data: {
            promoStatus: promoStatus
        },
        methods: {
            goInvest: function () {
                var that = this;
                switch (that.promoStatus) {
                    case '0':
                        location.href= '/deal/deal/index';
                        break;
                    case '1':
                        that.toastCenter('活动未开始');
                        break;
                    case '2':
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