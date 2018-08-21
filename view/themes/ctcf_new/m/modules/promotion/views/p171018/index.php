<?php

$this->title = '520天纪念活动';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20171016/css/index.css?v=0.1">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<div class="flex-content" id="app">
    <img class="banner" src="<?= FE_BASE_URI ?>wap/campaigns/active20171016/images/banner.png" alt="banner" />
    <div class="rule-title"></div>
    <div class="rule">
        <span>已累计年化：<?= $totalMoney ?>万元</span>
        <p>活动期间，年化出借金额每累计<i>10万元</i>，即可获赠额外<i>520积分</i>。</p>
        <h4>最终积分<i>（每年化10万）：</i></h4>
        <p class="txt">520（活动奖励）＋600（出借所得）＝1120积分</p>
    </div>
    <a class="btn-invest" @click="link"></a>
    <div class="goods-title">
        <span class="active-left left-title" data-num="0" @click="toggle"></span>
        <span class=" right-title" data-num="1" @click="toggle"></span>
    </div>
    <ul class="left clearfix" v-show="activeLeftBox">
        <li v-for="item in leftItems"><a :href="item.url"><img :src="item.img" alt="积分兑换"></a></li>
    </ul>
    <ul class="right clearfix" v-show="activeRightBox">
        <li v-for="item in rightItems"><a :href="item.url"><img :src="item.img" alt=""></a></li>
    </ul>
    <p class="bottom-txt bottom-up">本次活动最终解释权归楚天财富所有</p>
    <p class="bottom-txt bottom-down"><i></i>产品有风险<i></i>出借须谨慎</p>
</div>
<script>
    var promoStatus = $('input[name=promoStatus]').val();
    var app = new Vue({
        el: '#app',
        data : {
            num: '0',
            promoStatus: promoStatus,
            activeBox: true,
            activeLeftBox: true,
            activeRightBox: false,
            leftItems: [
                {
                    url: '/site/app-download?redirect=/mall/portal/guest?dbredirect=https%3A%2F%2Fgoods.m.duiba.com.cn%2Fmobile%2FappItemDetail%3FappItemId%3D1426624',
                    img: '<?= FE_BASE_URI ?>wap/campaigns/active20171016/images/goods-hot-1.png'
                },
                {
                    url: '/site/app-download?redirect=/mall/portal/guest?dbredirect=https%3A%2F%2Fgoods.m.duiba.com.cn%2Fmobile%2FappItemDetail%3FappItemId%3D1268312',
                    img: '<?= FE_BASE_URI ?>wap/campaigns/active20171016/images/goods-hot-2.png'
                },
                {
                    url: '/site/app-download?redirect=/mall/portal/guest?dbredirect=https%3A%2F%2Fgoods.m.duiba.com.cn%2Fmobile%2FappItemDetail%3FappItemId%3D898279',
                    img: '<?= FE_BASE_URI ?>wap/campaigns/active20171016/images/goods-hot-3.png'
                },
                {
                    url: '/site/app-download?redirect=/mall/portal/guest?dbredirect=https%3A%2F%2Fgoods.m.duiba.com.cn%2Fmobile%2FappItemDetail%3FappItemId%3D1411890',
                    img: '<?= FE_BASE_URI ?>wap/campaigns/active20171016/images/goods-hot-4.png'
                },
                {
                    url: '/site/app-download?redirect=/mall/portal/guest?dbredirect=https%3A%2F%2Fgoods.m.duiba.com.cn%2Fmobile%2FappItemDetail%3FappItemId%3D1362486',
                    img: '<?= FE_BASE_URI ?>wap/campaigns/active20171016/images/goods-hot-5.png'
                }
            ],
            rightItems: [
                {
                    url: '/site/app-download?redirect=/mall/portal/guest?dbredirect=https%3A%2F%2Fgoods.m.duiba.com.cn%2Fmobile%2FappItemDetail%3FappItemId%3D1333647',
                    img: '<?= FE_BASE_URI ?>wap/campaigns/active20171016/images/goods-market-1.png'
                },
                {
                    url: '/site/app-download?redirect=/mall/portal/guest?dbredirect=https%3A%2F%2Fgoods.m.duiba.com.cn%2Fmobile%2FappItemDetail%3FappItemId%3D1333659',
                    img: '<?= FE_BASE_URI ?>wap/campaigns/active20171016/images/goods-market-2.png'
                },
                {
                    url: '/site/app-download?redirect=/mall/portal/guest?dbredirect=https%3A%2F%2Fgoods.m.duiba.com.cn%2Fmobile%2FappItemDetail%3FappItemId%3D981034',
                    img: '<?= FE_BASE_URI ?>wap/campaigns/active20171016/images/goods-market-3.png'
                },
                {
                    url: '/site/app-download?redirect=/mall/portal/guest?dbredirect=https%3A%2F%2Fgoods.m.duiba.com.cn%2Fmobile%2FappItemDetail%3FappItemId%3D1108956',
                    img: '<?= FE_BASE_URI ?>wap/campaigns/active20171016/images/goods-market-4.png'
                },
                {
                    url: '/site/app-download?redirect=/mall/portal/guest?dbredirect=https%3A%2F%2Fgoods.m.duiba.com.cn%2Fmobile%2FappItemDetail%3FappItemId%3D1108961',
                    img: '<?= FE_BASE_URI ?>wap/campaigns/active20171016/images/goods-market-5.png'
                }
            ]
        },
        methods: {
            toggle: function(event) {
                var event = event || window.event;
                this.num = event.currentTarget.getAttribute('data-num');
                if ('0' === this.num) {
                    this.activeBox = true;
                    this.activeLeftBox = this.activeBox;
                    this.activeRightBox = !this.activeBox;
                    $('.goods-title span').removeClass('active-right').eq(this.num).addClass('active-left');
                } else if ('1' === this.num) {
                    this.activeBox = false;
                    this.activeLeftBox = this.activeBox;
                    this.activeRightBox = !this.activeBox;
                    $('.goods-title span').removeClass('active-left').eq(this.num).addClass('active-right');
                }
            },
            link: function () {
                if(this.promoStatus == '1') {
                    toastCenter("活动未开始");
                } else if (this.promoStatus == '2') {
                    toastCenter("活动已结束");
                } else {
                    location.href = "/deal/deal/index";
                }
            }

        }
    });
    function toastCenter(val, active) {
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

    Vue.config.devtools = false;
</script>