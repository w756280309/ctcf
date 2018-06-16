<?php

$this->title = '闯关赢好礼';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180618/css/index.min.css?v=1.3">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180618/css/window-box.min.css">
<script src="<?= FE_BASE_URI ?>libs/bscroll.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>

<style>
    .flex-content{
        -webkit-overflow-scrolling: touch;
    }
</style>
<div id="app" ref="flexContent" class="flex-content">
    <giftslist v-if="isShowGiftsList" v-on:close-popout="closeGiftsList" :awards-list-view="awardsListView"></giftslist>
    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180618/images/active_banner.png" @click.prevent
         class="top-banner">
    <div class="middle-map">
        <img @click.prevent class="map" src="<?= FE_BASE_URI ?>wap/campaigns/active20180618/images/map.png" alt="">
        <img class="my-prize" @click.prevent="getGiftsList"
             src="data:image/png;base64,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"
             alt="">
        <img :class="{'state-position1':position1,'state-position2':position2,'state-position3':position3,'state-position4':position4,'state-position5':position5,'state-position6':position6}"
             class="moving" @click.prevent src="<?= FE_BASE_URI ?>wap/campaigns/active20180618/images/moving@2x.png"
             alt="">
        <img :class="{'show-content':showBg1}" class="top-bg1 top-bg" @click.prevent
             src="<?= FE_BASE_URI ?>wap/campaigns/active20180618/images/prize_top_bg@2x.png" alt="">
        <img :class="{'show-content':showBg2}" class="top-bg2 top-bg" @click.prevent
             src="<?= FE_BASE_URI ?>wap/campaigns/active20180618/images/prize_top_bg@2x.png" alt="">
        <img :class="{'show-content':showBg3}" class="top-bg3 top-bg" @click.prevent
             src="<?= FE_BASE_URI ?>wap/campaigns/active20180618/images/prize_top_bg@2x.png" alt="">
        <img :class="{'show-content':showBg4}" class="top-bg4 top-bg" @click.prevent
             src="<?= FE_BASE_URI ?>wap/campaigns/active20180618/images/prize_top_bg@2x.png" alt="">
        <img :class="{'show-content':showBg5}" class="top-bg5 top-bg" @click.prevent
             src="<?= FE_BASE_URI ?>wap/campaigns/active20180618/images/prize_top_bg@2x.png" alt="">
        <p>当前累计年化投资：{{ userAnnualAmount | round }}万元</p>
        <img class="go-licai" @click.prevent="goLicai"
             src="<?= FE_BASE_URI ?>wap/campaigns/active20180618/images/go_licai@2x.png" alt="">
        <div class="notice">注：年化投资额＝投资金额＊项目期限/365</div>
    </div>
    <div class="get-reward">
        <div class="reward-title"><img @click.prevent
                                       src="<?= FE_BASE_URI ?>wap/campaigns/active20180618/images/part_title@2x.png"
                                       alt=""></div>
        <div class="all-prize">
            <img @click.prevent src="<?= FE_BASE_URI ?>wap/campaigns/active20180618/images/all_prize@2x.png"
                 alt="">
        </div>
    </div>
    <div class="rules">
        <div class="relus-title">
            <img @click.prevent src="<?= FE_BASE_URI ?>wap/campaigns/active20180618/images/rules_title@2x.png"
                 alt="">
        </div>
        <ul>
            <li>活动时间：2018.6.18-6.24；</li>
            <li>活动期间，累计年化投资达到指定闯关金额，龙舟可移动至相应位置，并获得相应奖品；</li>
            <li>年化投资可累计，如：活动期间，累计年化投资额达50万元，可使龙舟达到终点位置，并获得所有关卡的奖品；</li>
            <li>本活动奖品中，积分和现金红包将立即发放到账（需先完成开户），实物奖品将于活动结束后7个工作日内联系发放，请保持通讯畅通；</li>
            <li>转让产品不参与本次活动。</li>
        </ul>
    </div>
    <div class="bottom-part">
        <p>本活动最终解释权归温都金服所有</p>
        <p>理财非存款&nbsp;&nbsp;投资需谨慎</p>
    </div>
</div>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?= FE_BASE_URI ?>libs/wxShare.js"></script>
<script>
    'use strict';

    var prizeBox = {
        template: '\n                <div @touchmove.prevent class="prize-bg">\n            <div class="prize-content">\n                <img class="close-prize" src="<?= FE_BASE_URI ?>wap/campaigns/active20180618/images/close_prize@2x.png" @click.prevent="ClosePopout" alt="">\n                <div class="wrpper-top">\n                    <img @click.prevent src="<?= FE_BASE_URI ?>wap/campaigns/active20180618/images/my_prize_title@2x.png" alt="">\n                </div>\n                <div class="wrapper">\n                    <p v-if="awardsListView.length ===0">\u60A8\u8FD8\u6CA1\u6709\u83B7\u5F97\u5956\u54C1</p>\n                    <ul v-else class="content">\n                        <li v-for="(item, index) in awardsListView">\n                            <div>\n                                <div class="lf">\n                                    <img :src="item.path" alt="">\n                                </div>\n                                <div class="rg-prize">\n                                    <p>{{item.name}}</p>\n                                    <p>{{item.awardTime}}</p>\n                                </div>\n                            </div>\n                        </li>\n                    </ul>\n                    <!-- \u8FD9\u91CC\u53EF\u4EE5\u653E\u4E00\u4E9B\u5176\u5B83\u7684 DOM\uFF0C\u4F46\u4E0D\u4F1A\u5F71\u54CD\u6EDA\u52A8 -->\n            </div>\n            </div>\n            </div>\n            ',

        props: ['awardsListView'],
        mounted: function mounted() {
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
                userAnnualAmount: dataJson.userAnnualInvest,
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
                if (this.userAnnualAmount > 0 && this.userAnnualAmount < 10000) {
                    this.defaultPosition();
                    this.dafaultBg();
                    this.position1 = true;
                } else if (this.userAnnualAmount >= 10000 && this.userAnnualAmount < 50000) {
                    this.defaultPosition();
                    this.dafaultBg();
                    this.position2 = true;
                    this.showBg1 = false;
                } else if (this.userAnnualAmount >= 50000 && this.userAnnualAmount < 100000) {
                    this.defaultPosition();
                    this.dafaultBg();
                    this.position3 = true;
                    this.showBg1 = false;
                    this.showBg2 = false;
                } else if (this.userAnnualAmount >= 100000 && this.userAnnualAmount < 200000) {
                    this.defaultPosition();
                    this.dafaultBg();
                    this.position4 = true;
                    this.showBg1 = false;
                    this.showBg2 = false;
                    this.showBg3 = false;
                } else if (this.userAnnualAmount >= 200000 && this.userAnnualAmount < 500000) {
                    this.defaultPosition();
                    this.dafaultBg();
                    this.position5 = true;
                    this.showBg1 = false;
                    this.showBg2 = false;
                    this.showBg3 = false;
                    this.showBg4 = false;
                } else if (this.userAnnualAmount >= 500000) {
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
                wxShare.setParams("端午赛龙舟，闯关赢大礼！", "点击链接，立即参与", "<?= Yii::$app->params['clientOption']['host']['wap'] ?>promotion/p180618/index", "https://static.wenjf.com/upload/link/link1528959646210678.png", "<?= Yii::$app->params['weixin']['appId'] ?>", "/promotion/p180618/index/add-share");
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
                    var vm = this;
                    switch (this.promoStatus) {
                        case 1:
                            this.toastCenter('活动未开始');
                            break;
                        default:
                            if (this.isLoggedIn) {
                                if (this.flag) {
                                    this.flag = false;
                                    $.ajax({
                                        url: "/promotion/award-list/index",
                                        data: {"key": "promo_180618"},
                                        type: 'get',
                                        dataType: "json",
                                        success: function success(data) {
                                            vm.awardsListView = [];
                                            data.forEach(function (item, i) {
                                                item.path = '<?= FE_BASE_URI ?>' + item.path;
                                                vm.awardsListView.push(item);
                                            });
                                            vm.isShowGiftsList = !vm.isShowGiftsList;
                                            vm.flag = true;
                                        },
                                        error: function error() {
                                            vm.flag = true;
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