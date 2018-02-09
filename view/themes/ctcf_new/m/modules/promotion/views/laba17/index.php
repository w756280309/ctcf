<?php

$this->title = '腊八领积分';
?>

<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<style>
    .flex-content {
        position: relative;
    }
    .flex-content img {
        display: block;
        width: 100%;
    }
    .flex-content .invest-btn {
        position: absolute;
        bottom: 1.72rem;
        left: 50%;
        display: inline-block;
        width: 5.38666667rem;
        height: 1.4rem;
        border-radius: .7rem;
        cursor: pointer;
        -webkit-transform: translate(-50%,0);
        -moz-transform: translate(-50%,0);
        -o-transform: translate(-50%,0);
        transform: translate(-50%,0);
        z-index: 10;
    }
</style>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>

<div class="flex-content" id="app">
    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180119/img/laba-1.png" alt="" class="banner">
    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180119/img/laba-2.png" alt="" class="banner">
    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180119/img/laba-3.png" alt="" class="banner">
    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180119/img/laba-4.png" alt="" class="banner">
    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180119/img/laba-5.png" alt="" class="banner">
    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180119/img/laba-6.png" alt="" class="banner">
    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180119/img/laba-7.png" alt="" class="banner">
    <a @click="goInvest" class="invest-btn"></a>
</div>
<script>
    FastClick.attach(document.body);

    var app = new Vue({
        el: '#app',
        data: {
            isLoggedIn: datas.isLoggedIn,
            promoStatus: datas.promoStatus
        },
        methods: {
            goInvest: function () {
                var that = this;
                switch (that.promoStatus) {
                    case 0:
                        location.href= '/deal/deal/index';
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
