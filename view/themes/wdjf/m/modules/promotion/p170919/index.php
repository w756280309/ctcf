<?php

$this->title = '中秋佳品放送';
?>

<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170912/css/index.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/common.js"></script>
<style>
	[v-cloak]{display: none}
</style>
<div class="flex-content" id="app">
    <div class="part-one">
        <div class="go-invest" @click="link()">去投资</div>
        <p class="leiji">已累计年化：<span><?= rtrim(rtrim(bcdiv($totalAnnual, 10000, 2), '0'), '.') ?></span>万元</p>
    </div>
    <div class="part-two">
        <ul class="select-box clearfix">
            <li class="lf" v-for="(item, index) in items" :class="{ active:item.id==num }" @click="toggle(index)"  v-cloak>
                {{ item.message }}
            </li>
        </ul>
        <ul class="reward-box clearfix">
            <li class="first clearfix">
                <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://m.wenjf.com/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1379080&from=login&spm=19702.1.1.1" class="clearfix">
                    <img class="lf" src="<?= FE_BASE_URI ?>wap/campaigns/active20170912/images/gift-1-1.png" alt="">
                    <p style="padding-top:0.2rem">1088积分</p>
                    <p>桂新园月饼</p>
                    <p class="btn">立即兑换</p>
                </a>
            </li>
            <li class="lf">
                <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=716676&dbnewopen&dpm=19702.30.42.1&dcm=102.716676.0.0">
                    <img class="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170912/images/gift-1-3.png" alt="" style="border-radius: .2rem">
                    <p>1000积分</p>
                    <p>金龙鱼玉米油4L</p>
                    <div class="btn">立即兑换</div>
                </a>
            </li>
            <li class="rg">
                <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=913535&dbnewopen&dpm=19702.30.62.1&dcm=102.913535.0.0">
                    <img class="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170912/images/gift-1-2.png" alt="" style="border-radius: .2rem">
                    <p>600积分</p>
                    <p>天堂伞</p>
                    <div class="btn">立即兑换</div>
                </a>
            </li>
            <li class="lf">
                <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=716684&dbnewopen&dpm=19702.33.25.1&dcm=102.716684.0.0">
                    <img class="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170912/images/gift-1-4.png" alt="" style="border-radius: .2rem">
                    <p>500积分</p>
                    <p>Aquafresh三色牙膏</p>
                    <div class="btn">立即兑换</div>
                </a>
            </li>
            <li class="rg">
                <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1161571&dbnewopen&dpm=19702.30.31.1&dcm=102.1161571.0.0">
                    <img class="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170912/images/gift-1-5.png" alt="" style="border-radius: .2rem">
                    <p>400积分</p>
                    <p>意大利公鸡头皂</p>
                    <div class="btn">立即兑换</div>
                </a>
            </li>
        </ul>
        <ul style="display: none" class="reward-box clearfix">
            <li class="first clearfix">
                <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1333647&dbnewopen&dpm=19702.33.4.1&dcm=102.1333647.0.0" class="clearfix">
                    <img class="lf" src="<?= FE_BASE_URI ?>wap/campaigns/active20170912/images/gift-2-1.png" alt="">
                    <p style="padding-top:0.2rem">2000积分</p>
                    <p>100元人本超市卡</p>
                    <p class="btn">立即兑换</p>
                </a>

            </li>
            <li class="lf">
                <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1333659&dbnewopen&dpm=19702.33.3.1&dcm=102.1333659.0.0">
                    <img class="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170912/images/gift-2-2.png" alt="">
                    <p>1400积分</p>
                    <p>50元人本超市卡</p>
                    <div class="btn">立即兑换</div>
                </a>

            </li>
            <li class="rg">
                <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=981034&dbnewopen&dpm=19702.33.22.1&dcm=102.981034.0.0">
                    <img class="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170912/images/gift-2-3.png" alt="">
                    <p>1400积分</p>
                    <p>50元沃尔玛超市卡</p>
                    <div class="btn">立即兑换</div>
                </a>

            </li>
            <li class="lf">
                <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1108956&dbnewopen&dpm=19702.33.15.1&dcm=102.1108956.0.0">
                    <img class="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170912/images/gift-2-4.png" alt="">
                    <p>1400积分</p>
                    <p>50元浙北超市卡</p>
                    <div class="btn">立即兑换</div>
                </a>

            </li>
            <li class="rg">
                <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1108961&dbnewopen&dpm=19702.33.16.1&dcm=102.1108961.0.0">
                    <img class="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170912/images/gift-2-5.png" alt="">
                    <p>1400积分</p>
                    <p>50元大润发超市卡</p>
                    <div class="btn">立即兑换</div>
                </a>

            </li>
        </ul>
        <ul style="display: none;" class="reward-box clearfix">
            <li class="first clearfix">
                <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1268417&dbnewopen&dpm=19702.33.4.1&dcm=102.1268417.0.0" class="clearfix">
                    <img class="lf" src="<?= FE_BASE_URI ?>wap/campaigns/active20170912/images/gift-3-1.png" alt="">
                    <p style="padding-top:0.2rem">8880积分</p>
                    <p>小米智能电饭煲</p>
                    <p class="btn">立即兑换</p>
                </a>

            </li>
            <li class="lf">
                <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1224971&dbnewopen&dpm=19702.33.9.1&dcm=102.1224971.0.0">
                    <img class="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170912/images/gift-3-2.png" alt="">
                    <p>1580积分</p>
                    <p>小米充电宝</p>
                    <div class="btn">立即兑换</div>
                </a>

            </li>
            <li class="rg">
                <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=716686&dbnewopen&dpm=19702.33.21.1&dcm=102.716686.0.0">
                    <img class="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170912/images/gift-3-3.png" alt="">
                    <p>980积分</p>
                    <p>小米插线板</p>
                    <div class="btn">立即兑换</div>
                </a>

            </li>
            <li class="lf">
                <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1225006&dbnewopen&dpm=19702.33.7.1&dcm=102.1225006.0.0">
                    <img class="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170912/images/gift-3-4.png" alt="">
                    <p>1980积分</p>
                    <p>小米体重秤</p>
                    <div class="btn">立即兑换</div>
                </a>

            </li>
            <li class="rg">
                <a href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1090180&dbnewopen&dpm=19702.33.14.1&dcm=102.1090180.0.0">
                    <img class="" src="<?= FE_BASE_URI ?>wap/campaigns/active20170912/images/gift-3-5.png" alt="">
                    <p>198积分</p>
                    <p>数据线苹果安卓二合一</p>
                    <div class="btn">立即兑换</div>
                </a>

            </li>
        </ul>
    </div>
</div>
<script>
    var app = new Vue({
        el: '#app',
        data: {
            num: 0,
            isShow: true,
            items: [
                {message: '中秋佳品', id: 0},
                {message: '超市卡', id: 1},
                {message: '智能家居', id: 2}
            ]
        },
        methods: {
            //切换滑块
            toggle: function (index) {
                this.num = index;
                $("ul.reward-box").hide().eq(this.num).show();
            },
            link: function () {
                var promoStatus = $('input[name=promoStatus]').val();
                if ('1' === promoStatus) {
                    toastCenter("活动未开始");
                } else if('2' === promoStatus) {
                    toastCenter("活动已结束");
                } else {
                    location.href='/deal/deal/index?_mark='+Math.ceil(Math.random() * 1000000);
                }
            }
        }
    })
</script>