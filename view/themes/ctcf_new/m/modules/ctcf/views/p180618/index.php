<?php

$this->title = '闯关赢好礼';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/css/index.min.css?v=1.4">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/css/window-box.min.css?v=1.3">
<script src="<?= FE_BASE_URI ?>libs/bscroll.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>

<style>
    .flex-content{
        -webkit-overflow-scrolling: touch;
    }
    [v-cloak]{
        display:none;
    }
</style>
<div id="app" ref="flexContent" class="flex-content">
    <giftslist v-if="isShowGiftsList" v-on:close-popout="closeGiftsList" :awards-list-view="awardsListView"></giftslist>
    <img src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/images/active_banner.png" @click.prevent
         class="top-banner">
    <div class="middle-map">
        <img @click.prevent class="map" src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/images/map.png?v=1.3" alt="">
        <img class="my-prize" @click.prevent="getGiftsList"
             src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/images/my_prize@2x.png" alt="">
        <img :class="{'state-position1':position1,'state-position2':position2,'state-position3':position3,'state-position4':position4,'state-position5':position5,'state-position6':position6}"
             class="moving" @click.prevent src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/images/moving@2x.png"
             alt="">
        <img :class="{'show-content':showBg1}" class="top-bg1 top-bg" @click.prevent
             src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/images/prize_top_bg@2x.png" alt="">
        <img :class="{'show-content':showBg2}" class="top-bg2 top-bg" @click.prevent
             src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/images/prize_top_bg@2x.png" alt="">
        <img :class="{'show-content':showBg3}" class="top-bg3 top-bg" @click.prevent
             src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/images/prize_top_bg@2x.png" alt="">
        <img :class="{'show-content':showBg4}" class="top-bg4 top-bg" @click.prevent
             src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/images/prize_top_bg@2x.png" alt="">
        <img :class="{'show-content':showBg5}" class="top-bg5 top-bg" @click.prevent
             src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/images/prize_top_bg@2x.png" alt="">
        <p v-cloak>当前累计年化投资：{{ userAnnualAmount | round }}万元</p>
        <img class="go-licai" @click.prevent="goLicai"
             src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/images/go_licai@2x.png" alt="">
        <div class="notice">注：年化投资额＝投资金额＊项目期限/365</div>
    </div>
    <div class="get-reward">
        <div class="reward-title"><img @click.prevent
                                       src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/images/part_title@2x.png"
                                       alt=""></div>
        <div class="all-prize">
            <img @click.prevent src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/images/all_prize@2x.png?v=1.3"
                 alt="">
        </div>
    </div>
    <div class="rules">
        <div class="relus-title">
            <img @click.prevent src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/images/rules_title@2x.png"
                 alt="">
        </div>
        <ul>
            <li>活动时间：2018.6.18-6.24；</li>
            <li>活动期间，累计年化投资达到指定闯关金额，龙舟可移动至相应位置，并获得相应奖品；</li>
            <li>年化投资可累计，如：活动期间，累计年化投资额达20万元，可使龙舟达到终点位置，并获得所有关卡的奖品；</li>
            <li>本活动奖品中，积分和现金红包将立即发放到账（需先完成开户），实物奖品将于活动结束后7个工作日内联系发放，请保持通讯畅通；</li>
            <li>转让产品不参与本次活动。</li>
        </ul>
    </div>
    <div class="bottom-part">
        <p>本活动最终解释权归楚天财富所有</p>
        <p>理财非存款&nbsp;&nbsp;投资需谨慎</p>
    </div>
</div>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?= FE_BASE_URI ?>libs/wxShare.js"></script>
<script>
    'use strict';

    var prizeBox = {
        template: '\n                <div @touchmove.prevent class="prize-bg">\n            <div class="prize-content">\n                <img class="close-prize" src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/images/close_prize@2x.png" @click.prevent="ClosePopout" alt="">\n                <div class="wrpper-top">\n                    <img @click.prevent src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/images/my_prize_title@2x.png" alt="">\n                </div>\n                <div class="wrapper">\n                    <p v-if="awardsListView.length ===0">\u60A8\u8FD8\u6CA1\u6709\u83B7\u5F97\u5956\u54C1</p>\n                    <ul v-else class="content">\n                        <li v-for="(item, index) in awardsListView">\n                            <div>\n                                <div class="lf">\n                                    <img :src="item.path" alt="">\n                                </div>\n                                <div class="rg-prize">\n                                    <p>{{item.name}}</p>\n                                    <p>{{item.awardTime}}</p>\n                                </div>\n                            </div>\n                        </li>\n                    </ul>\n                    <!-- \u8FD9\u91CC\u53EF\u4EE5\u653E\u4E00\u4E9B\u5176\u5B83\u7684 DOM\uFF0C\u4F46\u4E0D\u4F1A\u5F71\u54CD\u6EDA\u52A8 -->\n            </div>\n            </div>\n            </div>\n            ',

        props: ['awardsListView'],
        mounted: function mounted() {
            console.log(this.awardsListView);
            var scroll = new BScroll('.wrapper');
        },

        methods: {
            ClosePopout: function ClosePopout() {
                this.$emit('close-popout');
            }
        }
    };
    $(function () {
        FastClick.attach(document.body);

        var app = new Vue({
            el: "#app",
            data: {
                promoStatus: dataJson.promoStatus,
                isLoggedIn: dataJson.isLoggedIn,
                userAnnualAmount: 0,
                // 奖品外面的圆圈
                showBg1: true,
                showBg2: true,
                showBg3: true,
                showBg4: true,
                showBg5: true,
                // 龙舟移动的位置
                position1: false,
                position2: false,
                position3: false,
                position4: false,
                position5: false,
                position6: false,
                // 奖品列表弹框
                show: false,
                isShowGiftsList: false,
                awardsListView: [],
                flag: true
            },
            created: function created() {
                this.$on('showPrize', function () {
                    this.show = false;
                });
                (dataJson.userAnnualInvest)&&(this.userAnnualAmount=dataJson.userAnnualInvest);
                var newV = this.userAnnualAmount;
                if (newV > 0 && newV < 10000) {
                    this.defaultPosition();
                    this.dafaultBg();
                    this.position2 = true;
                    this.showBg1 = false;
                } else if (newV >= 10000 && newV < 50000) {
                    this.defaultPosition();
                    this.dafaultBg();
                    this.position3 = true;
                    this.showBg1 = false;
                    this.showBg2 = false;
                } else if (newV >= 50000 && newV < 100000) {
                    this.defaultPosition();
                    this.dafaultBg();
                    this.position4 = true;
                    this.showBg1 = false;
                    this.showBg2 = false;
                    this.showBg3 = false;
                } else if (newV >= 100000 && newV < 200000) {
                    this.defaultPosition();
                    this.dafaultBg();
                    this.position5 = true;
                    this.showBg1 = false;
                    this.showBg2 = false;
                    this.showBg3 = false;
                    this.showBg4 = false;
                } else if (newV >= 200000) {
                    this.defaultPosition();
                    this.dafaultBg();
                    this.position6 = true;
                    this.showBg1 = false;
                    this.showBg2 = false;
                    this.showBg3 = false;
                    this.showBg4 = false;
                    this.showBg5 = false;
                }
            },
            mounted: function mounted() {
                wxShare.setParams("端午赛龙舟，闯关赢大礼！", "点击链接，立即参与", "<?= Yii::$app->params['clientOption']['host']['wap'] ?>ctcf/p180618/index", "https://static.wenjf.com/upload/link/link1528959646210678.png", "<?= Yii::$app->params['weixin']['appId'] ?>", "/ctcf/p180618/index/add-share");
            },


            methods: {
                // toast 弹窗
                toastCenter: function toastCenter(val, active) {
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
                },
                // 关闭奖品列表
                closeGiftsList: function closeGiftsList() {
                    this.isShowGiftsList = !this.isShowGiftsList;
                },
                getGiftsList: function getGiftsList() {
                    /*mock数据*/
                    var that = this;
                    switch (this.promoStatus) {
                        case 1:
                            this.toastCenter('活动未开始');
                            break;
                        default:
                            if (this.isLoggedIn) {
                                if (this.flag) {
                                    this.flag = false;

                                    $.ajax({
                                        url: "/ctcf/award-list/index",
                                        data: { "key": "promo_1806181" },
                                        type: 'get',
                                        dataType: "json",
                                        success: function success(data) {
                                            that.awardsListView = [];
                                            data.forEach(function (item, i) {
                                                that.awardsListView.push(item);
                                            });
                                            that.isShowGiftsList = !that.isShowGiftsList;
                                            that.flag = true;
                                        },
                                        error: function error() {
                                            that.flag = true;
                                        }
                                    });
                                }
                            } else {
                                this.toastCenter('您还未登录');
                                setTimeout(function () {
                                    location.href = "/site/login";
                                }, 2000);
                            }
                            break;
                    }
                },

                goLicai: function goLicai() {
                    switch (this.promoStatus) {
                        case 1:
                            this.toastCenter('活动未开始');
                            break;
                        case 2:
                            this.toastCenter('活动已结束');
                            break;
                        case 0:
                            if (this.isLoggedIn == false) {
                                this.toastCenter('您还未登录');
                                setTimeout(function () {
                                    location.href = "/site/login";
                                }, 2000);
                            } else {
                                location.href = "/deal/deal/index";
                            }
                            break;
                    }
                },
                defaultPosition: function defaultPosition() {
                    this.position1 = false;
                    this.position2 = false;
                    this.position3 = false;
                    this.position4 = false;
                    this.position5 = false;
                    this.position6 = false;
                },
                dafaultBg: function dafaultBg() {
                    this.showBg1 = true;
                    this.showBg2 = true;
                    this.showBg3 = true;
                    this.showBg4 = true;
                    this.showBg5 = true;
                },

                bodyScroll: function bodyScroll(e) {
                    var e = e || window.event;
                    e.preventDefault();
                },
                showMyPrize: function showMyPrize() {
                    this.show = true;
                }
            },
            components: {
                'giftslist': prizeBox
            },
            filters: {
                'round': function round(value) {
                    return (Math.floor(value / 100) / 100).toFixed(2, 10) - 0;
                }
            }
        });
    });
</script>
