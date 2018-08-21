<?php

$this->title = '礼遇7月';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/css/index.min.css?v=1.4">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/axios.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/bscroll.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<div class="flex-content" id="P180701">
    <div class="banner-box">
        <img class="banner" @click.prevent src="<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/banner.png" alt="">
        <img class="gifts-list" @click.prevent="getRewardList"
             src="<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/gitf_list_btn.png" alt="">
        <p>活动时间：7月10日-7月20日</p>
    </div>
    <div class="content">
        <div class="flop-box">
            <div class="title-box">
                <div class="bg"></div>
                <p class="title">每日免费翻牌</p>
            </div>
            <div class="draw-module">
                <div class="gift-show">
                    <ul>
                        <li class="lf" :class="{moveFlop:isMoveFlop}">
                            <div class="gift" :class="{firstChild:isFlop,selected:select.selected0}">
                                <div class="gift-detail">
                                    <img @click.prevent :src="awards[0].imgUrl" alt="">
                                    <p v-text="awards[0].name"></p>
                                </div>
                            </div>
                            <div @click.prevent="flopOne(0)" class="noGift" :class="{lastChild:isFlop}"><img alt=""
                                                                                                             src="<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/background.png">
                            </div>
                        </li>
                        <li class="lf" :class="{moveFlop:isMoveFlop}">
                            <div class="gift" :class="{firstChild:isFlop,selected:select.selected1}">
                                <div class="gift-detail">
                                    <img @click.prevent :src="awards[1].imgUrl" alt="">
                                    <p v-text="awards[1].name"></p>
                                </div>
                            </div>
                            <div @click.prevent="flopOne(1)" class="noGift" :class="{lastChild:isFlop}"><img alt=""
                                                                                                             src="<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/background.png">
                            </div>
                        </li>
                        <li class="lf" :class="{moveFlop:isMoveFlop}">
                            <div class="gift" :class="{firstChild:isFlop,selected:select.selected2}">
                                <div class="gift-detail">
                                    <img @click.prevent :src="awards[2].imgUrl" alt="">
                                    <p v-text="awards[2].name"></p>
                                </div>
                            </div>
                            <div @click.prevent="flopOne(2)" class="noGift" :class="{lastChild:isFlop}"><img alt=""
                                                                                                             src="<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/background.png">
                            </div>
                        </li>
                        <li class="lf" :class="{moveFlop:isMoveFlop}">
                            <div class="gift" :class="{firstChild:isFlop,selected:select.selected3}">
                                <div class="gift-detail">
                                    <img @click.prevent :src="awards[3].imgUrl" alt="">
                                    <p v-text="awards[3].name"></p>
                                </div>
                            </div>
                            <div @click.prevent="flopOne(3)" class="noGift" :class="{lastChild:isFlop}"><img alt=""
                                                                                                             src="<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/background.png">
                            </div>
                        </li>
                        <li @click="flop" class="shuffle">
                            <p class="shuffle-copyright" v-html="shuffleCopyright"></p>
                            <img @click.prevent alt=""
                                 src="<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/shuffle.png">
                        </li>
                        <li class="lf" :class="{moveFlop:isMoveFlop}">
                            <div class="gift" :class="{firstChild:isFlop,selected:select.selected4}">
                                <div class="gift-detail">
                                    <img @click.prevent :src="awards[4].imgUrl" alt="">
                                    <p v-text="awards[4].name"></p>
                                </div>
                            </div>
                            <div @click.prevent="flopOne(4)" class="noGift" :class="{lastChild:isFlop}"><img alt=""
                                                                                                             src="<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/background.png">
                            </div>
                        </li>
                        <li class="lf" :class="{moveFlop:isMoveFlop}">
                            <div class="gift" :class="{firstChild:isFlop,selected:select.selected5}">
                                <div class="gift-detail">
                                    <img @click.prevent :src="awards[5].imgUrl" alt="">
                                    <p v-text="awards[5].name"></p>
                                </div>
                            </div>
                            <div @click.prevent="flopOne(5)" class="noGift" :class="{lastChild:isFlop}"><img alt=""
                                                                                                             src="<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/background.png">
                            </div>
                        </li>
                        <li class="lf" :class="{moveFlop:isMoveFlop}">
                            <div class="gift" :class="{firstChild:isFlop,selected:select.selected6}">
                                <div class="gift-detail">
                                    <img @click.prevent :src="awards[6].imgUrl" alt="">
                                    <p v-text="awards[6].name"></p>
                                </div>
                            </div>
                            <div @click.prevent="flopOne(6)" class="noGift" :class="{lastChild:isFlop}"><img alt=""
                                                                                                             src="<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/background.png">
                            </div>
                        </li>
                        <li class="lf" :class="{moveFlop:isMoveFlop}">
                            <div class="gift" :class="{firstChild:isFlop,selected:select.selected7}">
                                <div class="gift-detail">
                                    <img @click.prevent :src="awards[7].imgUrl" alt="">
                                    <p v-text="awards[7].name"></p>
                                </div>
                            </div>
                            <div @click.prevent="flopOne(7)" class="noGift" :class="{lastChild:isFlop}"><img alt=""
                                                                                                             src="<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/background.png">
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="share">
            <p>分享到朋友圈/好友，可额外获得一次翻牌机会！</p>
            <div @click="wxShareDealPop">立即分享</div>
        </div>
        <div class="regular">
            <div class="title-box">
                <div class="bg"></div>
                <p class="title">活动规则</p>
            </div>
            <ul>
                <li><i>1</i>活动时间：2018.7.10-7.20；</li>
                <li><i>2</i>活动期间，每天免费获得1次翻牌机会；</li>
                <li><i>3</i>分享到朋友圈/好友可额外获得1次翻牌机会，每日最多2次；</li>
                <li><i>4</i>翻牌次数每日0点重置，不可累积到下一日；</li>
                <li><i>5</i>本次活动虚拟奖品将立即发放到账，实物奖品将在7个工作内联系发放。</li>
            </ul>
        </div>
        <p class="tips">本活动最终解释权归楚天财富所有<br>&nbsp;&nbsp;&nbsp;投资需谨慎</p>
    </div>
    <popout v-if="popout.isClosePopout" v-on:changeresult="closePopout" :awards="popout.awards"></popout>
    <giftslist v-if="isShowGiftsList" v-on:closelist="closeGiftsList"
               :awards-list-view="awardsListView"></giftslist>
    <popskin1 v-if="isShowWxSahre" v-on:wxsharedeal="wxShareDeal"></popskin1>
</div>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/js/wxShare.js?v=1.4"></script>
<script>
    FastClick.attach(document.body);

    'use strict';
    var prizeBox = {
        template: '\n        <div @touchmove.prevent class="prize-bg">\n            <div class="prize-content">\n                <img class="close-prize" src="<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/close.png" @click.prevent="ClosePopout" alt="">\n                <div class="wrpper-top">\n                    <img @click.prevent src="<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/my_prize_title@2x.png" alt="">\n                </div>\n                <div class="wrapper">\n                    <p v-if="noPrize">\u60A8\u8FD8\u6CA1\u6709\u83B7\u5F97\u5956\u54C1</p>\n                    <ul class="content" style="background:#fff;">\n                        <li v-for="(item, index) in awardsListView">\n                            <div>\n                                <div class="lf">\n                                    <img :src="item.path" alt="">\n                                </div>\n                                <div class="rg-prize">\n                                    <p>{{item.name}}</p>\n                                    <p>{{item.awardTime}}</p>\n                                </div>\n                            </div>\n                        </li>\n                    </ul>\n                </div>\n            </div>\n        </div>\n        ',
        props: ['awardsListView'],
        created: function created() {
            this.awardsListView.length == 0 && (this.noPrize = true);
        },
        mounted: function mounted() {
            var scroll = new BScroll('.wrapper');
        },
        data: function data() {
            return {
                noPrize: false
            };
        },

        methods: {
            ClosePopout: function ClosePopout() {
                this.$emit('closelist');
            }
        }
    };
    var Popout = {
        template: '\n        <div @touchmove.prevent class="mask">\n            <div class="pop">\n                <img class="close"  @click.prevent="closePop" src="<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/close.png" alt="">\n                <img class="pop-bg"  @click.prevent src="<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/popout_bg.png" alt="">\n                <div class="reword-container">\n                    <p>{{awards.rewordTitle}}</p>\n                    <img class="reword-img" :src="awards.linkUrl" @click.prevent alt="">\n                </div>\n                <div class="btn" @click="closePop">\u6536\u4E0B\u793C\u54C1</div>\n            </div>\n        </div>\n        ',
        props: ['awards'],
        methods: {
            closePop: function closePop() {
                this.$emit('changeresult');
            }
        }
    };
    var PopSkin1 = {
        template: '\n        <div class="pop-skin1" @touchmove.prevent>\n            <div class="pop">\n                <p class="tips">您当前没有翻牌机会，<br>分享给好友或到朋友圈可额外<br>获得翻牌机会哦～</p>\n                <ul class="clearfix">\n                    <li @click="wxShareBtn(\'close\')" class="lf">\u77E5\u9053\u4E86</li>\n                    <li class="lf share-btn">\u7ACB\u523B\u5206\u4EAB</li>\n                </ul>\n            </div>\n        </div>\n        ',
        methods: {
            wxShareBtn: function(val) {
                this.$emit('wxsharedeal', val);
            }
        }
    };

    var RP88 = '<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/180701_CTCF_RP88.png';
    var PI_MACKET = '<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/180701_CTCF_PI_MACKET.png';
    var P18 = '<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/180701_CTCF_P18.png';
    var C3 = '<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/180701_CTCF_C3.png';
    var C5 = '<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/180701_CTCF_C5.png';
    var C20 = '<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/180701_CTCF_C20.png';
    var C50 = '<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/180701_CTCF_C50.png';
    var NOREWARD = '<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/180701_CTCF_NOREWARD.png';
    axios.interceptors.request.use(function (config) {
        config.headers = {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'};
        return config;
    }, function (error) {
        console.log("错误的传参");
    });
    var vm = new Vue({
        el: '#P180701',
        data: {
            status: {
                promoStatus: dataJson.promoStatus,
                isLoggedIn: dataJson.isLoggedIn
            },
            isMoveFlop: false,
            isFlop: false,
            shuffleCopyright: "点击<br>翻牌",
            isComplete: false,
            awards: [{imgUrl: C5, name: "5元代金券"}, {imgUrl: NOREWARD, name: '谢谢参与'}, {
                imgUrl: P18,
                name: '18积分'
            }, {imgUrl: PI_MACKET, name: '50元超市卡'}, {imgUrl: C50, name: '50元代金券'}, {
                imgUrl: C3,
                name: '3元代金券'
            }, {imgUrl: RP88, name: '88元现金红包'}, {imgUrl: C20, name: '20元代金券'}],
            // 奖品列表弹框
            isShowGiftsList: false,
            awardsListView: [],
            /*中间弹框*/
            popout: {
                isClosePopout: false,
                awards: {
                    rewordTitle: '谢谢参与',
                    linkUrl: NOREWARD
                }
            },
            select: {
                selected0: false,
                selected1: false,
                selected2: false,
                selected3: false,
                selected4: false,
                selected5: false,
                selected6: false,
                selected7: false
            },
            isShowWxSahre: false,
            isShowSharePop:false
        },
        mounted: function mounted() {
            wxShare.setParams("礼遇七月，年中回馈，海量好礼限时抽取！", "点击链接，立即参与~", "<?= Yii::$app->params['clientOption']['host']['wap'] ?>/ctcf/p180701/index", "https://admin.hbctcf.com/upload/link/link1530839165812521.png", "<?= Yii::$app->params['weixin']['appId'] ?>", "/ctcf/p180701/add-share");
            wxShare.AppMessageSuccessCallBack = wxShare.TimelineSuccessCallBack = function () {
                $.get("/ctcf/p180701/add-share?scene=timeline&shareUrl=" + encodeURIComponent(location.href));
            };
        },

        methods: {
            flop: function flop() {
                if (!this.baseVerify()) return;
                if(this.isShowSharePop) return;
                this.isShowSharePop = true;
                var token = this.getQueryString('token') ? '&token=' + this.getQueryString('token') : '';
                axios.get("/ctcf/p180701/draw-state?key=promo_180701" + token).then(function (response) {
                    if (response.data.state == 1 || response.data.state == 3) {
                        if (vm.isFlop) {
                            vm.toastCenter('去选一张卡牌吧');
                        } else {
                            vm.isFlop = !vm.isFlop;
                            var timer800 = setTimeout(function () {
                                vm.isMoveFlop = !vm.isMoveFlop;
                                var timer2000 = setTimeout(function () {
                                    vm.shuffleCopyright = "选择<br>卡牌";
                                    vm.isMoveFlop = !vm.isMoveFlop;
                                    vm.shuffle(vm.awards);
                                    for (var i in vm.select) {
                                        vm.select[i] = false;
                                    }
                                    var timer2800 = setTimeout(function () {
                                        vm.isComplete = !vm.isComplete;
                                        vm.isShowSharePop = !vm.isShowSharePop;
                                        clearTimeout(timer2800);
                                    }, 800);
                                    clearTimeout(timer2000);
                                }, 1200);
                                clearTimeout(timer800);
                            }, 800);
                        }
                    } else if (response.data.state == 2) {
                        vm.isShowWxSahre = !vm.isShowWxSahre;
                        vm.isShowSharePop = !vm.isShowSharePop;
                    } else {
                        vm.toastCenter('今日2次翻牌机会已经用完，明天再来吧');
                        vm.isShowSharePop = !vm.isShowSharePop;
                    }
                }).catch(function (error) {
                    vm.isComplete = !vm.isComplete;
                    vm.toastCenter(error.response.data.message);
                });
            },
            flopOne: function flopOne(index) {
                if (!this.isComplete) return;
                if (!this.baseVerify()) return;
                this.isComplete = !this.isComplete;
                var vm = this,
                    node = $(event.target).parent(),
                    token = this.getQueryString('token') ? '&token=' + this.getQueryString('token') : '';
                axios.get('/ctcf/p180701/get-reward?key=promo_180701' + token).then(function (response) {
                    vm.select['selected' + index] = true;
                    node.siblings('.gift').find('img').attr({'src': vm.getAwardUrl(response.data.result.sn)});
                    node.siblings('.gift').find('p').text(vm.getAwardName(response.data.result.sn));
                    vm.flopCard(index, response.data.result.sn, node);
                }).catch(function (error) {
                    vm.toastCenter(error.response.data.message);
                    vm.isShowSharePop = !vm.isShowSharePop;
                });
            },
            flopCard: function flopCard(index, award, node) {
                this.changeFlop(index, award);
                this.$nextTick(function () {
                    node.removeClass('lastChild');
                    node.siblings('.gift').removeClass('firstChild');
                    var timer800 = setTimeout(function () {
                        vm.isFlop = !vm.isFlop;
                        var timer2000 = setTimeout(function () {
                            vm.popout.isClosePopout = !vm.popout.isClosePopout;
                            vm.popout.awards.rewordTitle = vm.getAwardName(award);
                            vm.popout.awards.linkUrl = vm.getAwardUrl(award);
                            vm.shuffleCopyright = "点击<br>翻牌";
                            vm.isShowSharePop = false;
                            clearTimeout(timer2000);
                        }, 1200);
                        clearTimeout(timer800);
                    }, 800);

                });
            },
            shuffle: function shuffle(arr) {
                var n = arr.length,
                    random = void 0;
                while (0 != n) {
                    random = Math.floor(Math.random() * n--);
                    var _ref = [arr[random], arr[n]];
                    arr[n] = _ref[0];
                    arr[random] = _ref[1];
                }
                return arr;
            },
            changeFlop: function changeFlop(indexNum, award) {
                this.awards.map(function (item, index) {
                    if (item.imgUrl === vm.getAwardUrl(award)) {
                        var _ref2 = [vm.awards[index], vm.awards[indexNum]];
                        vm.awards[indexNum] = _ref2[0];
                        vm.awards[index] = _ref2[1];
                    }
                });
            },
            closeGiftsList: function closeGiftsList() {
                this.isShowGiftsList = !this.isShowGiftsList;
            },

            getRewardList: function getRewardList() {
                if (this.status.isLoggedIn) {
                    if (this.status.promoStatus == 1) {
                        this.toastCenter('活动未开始');
                    } else {
                        var token = this.getQueryString('token') ? '&token=' + this.getQueryString('token') : '';
                        axios.get('/ctcf/award-list/index?key=promo_180701' + token).then(function (response) {
                            vm.awardsListView = [];
                            response.data.forEach(function (item) {
                                item.path = "<?= ASSETS_BASE_URI ?>ctcf/promotion/p180701/images/" + item.path;
                                vm.awardsListView.push(item);
                            });
                            vm.isShowGiftsList = !vm.isShowGiftsList;
                        }).catch(function (error) {
                            vm.toastCenter(error.response.data.message);
                        });
                    }
                } else {
                    window.location.href = "/site/login";
                }
            },
            closePopout: function closePopout() {
                this.popout.isClosePopout = !this.popout.isClosePopout;
            },

            getAwardName: function getAwardName(sn) {
                switch (sn) {
                    case '180701_CTCF_P18':
                        return '18积分';
                        break;
                    case '180701_CTCF_C3':
                        return '3元优惠券';
                        break;
                    case '180701_CTCF_C5':
                        return '5元优惠券';
                        break;
                    case '180701_CTCF_C20':
                        return '20元优惠券';
                        break;
                    case '180701_CTCF_C50':
                        return '50元优惠券';
                        break;
                    case '180701_CTCF_NOREWARD':
                        return '谢谢参与';
                        break;
                    case '180701_CTCF_PI_MACKET':
                        return '超市卡';
                        break;
                    case '180701_CTCF_RP88':
                        return '88元现金';
                        break;
                }
            },
            getAwardUrl: function getAwardName(sn) {
                switch (sn) {
                    case '180701_CTCF_P18':
                        return P18;
                        break;
                    case '180701_CTCF_C3':
                        return C3;
                        break;
                    case '180701_CTCF_C5':
                        return C5;
                        break;
                    case '180701_CTCF_C20':
                        return C20;
                        break;
                    case '180701_CTCF_C50':
                        return C50;
                        break;
                    case '180701_CTCF_NOREWARD':
                        return NOREWARD;
                        break;
                    case '180701_CTCF_PI_MACKET':
                        return PI_MACKET;
                        break;
                    case '180701_CTCF_RP88':
                        return RP88;
                        break;
                }
            },
            wxShareDeal: function (val) {
                this.isShowWxSahre = !this.isShowWxSahre;
            },
            wxShareDealPop: function () {
                if (!this.baseVerify()) return;
                if ($(event.target).hasClass('share-btn')) return;
                $(event.target).addClass('share-btn');
            },
            baseVerify: function baseVerify() {
                switch (this.status.promoStatus) {
                    case 0:
                        if (this.status.isLoggedIn) {
                            return true;
                        } else {
                            window.location.href = "/site/login";
                            return false;
                        }
                        break;
                    case 1:
                        this.toastCenter('活动未开始');
                        return false;
                        break;
                    case 2:
                        this.toastCenter('活动已结束');
                        return false;
                        break;
                    default:
                        break;
                }
            },
            toastCenter: function toastCenter(val, active) {
                var $alert = $('<div class="error-info" style="display: block; position: fixed;font-size: .4rem;transform: translate(-50%,-50%) translateZ(3px);  -webkit-transform: translate(-50% ,-50%) translateZ(3px);  -moz-transform: translate(-50% ,-50%) translateZ(3px);"><div>' + val + '</div></div>');
                $('body').append($alert);
                $alert.find('div').width($alert.width());
                setTimeout(function () {
                    $alert.fadeOut();
                    setTimeout(function () {
                        $alert.remove();
                    }, 2000);
                    if (active) {
                        active();
                    }
                }, 2000);
            },
            getQueryString: function (name) {
                var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
                var r = window.location.search.substr(1).match(reg);
                if (r != null) return unescape(r[2]);
            }
        },
        components: {
            'giftslist': prizeBox,
            'popout': Popout,
            'popskin1': PopSkin1
        }
    });
</script>