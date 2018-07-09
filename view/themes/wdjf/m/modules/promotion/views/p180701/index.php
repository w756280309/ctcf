<?php

$this->title = '礼遇7月';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180701/css/index.min.css?v=1.9">
<script src="<?= FE_BASE_URI ?>libs/bscroll.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/axios.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>

<div class="flex-content" id="p180701">
    <div class="banner-box">
        <img class="banner" @click.prevent src="<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/banner.png"
             alt="">
        <img class="gifts-list" @click.prevent="getRewardList"
             src="<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/gitf_list_btn.png" alt="">
        <p>活动时间：7月10日-7月20日</p>
    </div>
    <div class="content">
        <div class="flop-box">
            <div class="title-box">
                <div class="bg"></div>
                <p class="title">积分翻牌 <i>100%</i>中奖</p>
            </div>
            <div class="draw-module">
                <div class="gift-show">
                    <ul>
                        <li class="lf" :class="{moveFlop:isMoveFlop}">
                            <div class="gift" :class="{firstChild:isFlop,selected:select.selected0}">
                                <div class="gift-detail">
                                    <img @click.prevent :src="awards[0].imgUrl" alt="">
                                    <p v-html="awards[0].name"></p>
                                </div>
                            </div>
                            <div @click.prevent="flopOne(0)" class="noGift" :class="{lastChild:isFlop}"><img alt=""
                                                                                                             src="<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/background.png">
                            </div>
                        </li>
                        <li class="lf" :class="{moveFlop:isMoveFlop}">
                            <div class="gift" :class="{firstChild:isFlop,selected:select.selected1}">
                                <div class="gift-detail">
                                    <img @click.prevent :src="awards[1].imgUrl" alt="">
                                    <p v-html="awards[1].name"></p>
                                </div>
                            </div>
                            <div @click.prevent="flopOne(1)" class="noGift" :class="{lastChild:isFlop}"><img alt=""
                                                                                                             src="<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/background.png">
                            </div>
                        </li>
                        <li class="lf" :class="{moveFlop:isMoveFlop}">
                            <div class="gift" :class="{firstChild:isFlop,selected:select.selected2}">
                                <div class="gift-detail">
                                    <img @click.prevent :src="awards[2].imgUrl" alt="">
                                    <p v-html="awards[2].name"></p>
                                </div>
                            </div>
                            <div @click.prevent="flopOne(2)" class="noGift" :class="{lastChild:isFlop}"><img alt=""
                                                                                                             src="<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/background.png">
                            </div>
                        </li>
                        <li class="lf" :class="{moveFlop:isMoveFlop}">
                            <div class="gift" :class="{firstChild:isFlop,selected:select.selected3}">
                                <div class="gift-detail">
                                    <img @click.prevent :src="awards[3].imgUrl" alt="">
                                    <p v-html="awards[3].name"></p>
                                </div>
                            </div>
                            <div @click.prevent="flopOne(3)" class="noGift" :class="{lastChild:isFlop}"><img alt=""
                                                                                                             src="<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/background.png">
                            </div>
                        </li>
                        <li @click="flop" class="shuffle">
                            <p class="shuffle-copyright" v-html="shuffleCopyright"></p>
                            <img @click.prevent alt=""
                                 src="<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/shuffle.png">
                        </li>
                        <li class="lf" :class="{moveFlop:isMoveFlop}">
                            <div class="gift" :class="{firstChild:isFlop,selected:select.selected4}">
                                <div class="gift-detail">
                                    <img @click.prevent :src="awards[4].imgUrl" alt="">
                                    <p v-html="awards[4].name"></p>
                                </div>
                            </div>
                            <div @click.prevent="flopOne(4)" class="noGift" :class="{lastChild:isFlop}"><img alt=""
                                                                                                             src="<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/background.png">
                            </div>
                        </li>
                        <li class="lf" :class="{moveFlop:isMoveFlop}">
                            <div class="gift" :class="{firstChild:isFlop,selected:select.selected5}">
                                <div class="gift-detail">
                                    <img @click.prevent :src="awards[5].imgUrl" alt="">
                                    <p v-html="awards[5].name"></p>
                                </div>
                            </div>
                            <div @click.prevent="flopOne(5)" class="noGift" :class="{lastChild:isFlop}"><img alt=""
                                                                                                             src="<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/background.png">
                            </div>
                        </li>
                        <li class="lf" :class="{moveFlop:isMoveFlop}">
                            <div class="gift" :class="{firstChild:isFlop,selected:select.selected6}">
                                <div class="gift-detail">
                                    <img @click.prevent :src="awards[6].imgUrl" alt="">
                                    <p v-html="awards[6].name"></p>
                                </div>
                            </div>
                            <div @click.prevent="flopOne(6)" class="noGift" :class="{lastChild:isFlop}"><img alt=""
                                                                                                             src="<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/background.png">
                            </div>
                        </li>
                        <li class="lf" :class="{moveFlop:isMoveFlop}">
                            <div class="gift" :class="{firstChild:isFlop,selected:select.selected7}">
                                <div class="gift-detail">
                                    <img @click.prevent :src="awards[7].imgUrl" alt="">
                                    <p v-html="awards[7].name"></p>
                                </div>
                            </div>
                            <div @click.prevent="flopOne(7)" class="noGift" :class="{lastChild:isFlop}"><img alt=""
                                                                                                             src="<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/background.png">
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <p class="points">您当前积分：{{points}}积分</p>
        </div>
        <div class="invest">
            <div class="title-box">
                <div class="bg"></div>
                <p class="title">精选项目推荐</p>
            </div>
            <div class="intro-invest">
                <img @click.prevent src="<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/invest.png" alt="">
                <a class="go-invest list24" :href="investUrl.list24">查看详情</a>
                <a class="go-invest list36" :href="investUrl.list36">查看详情</a>
            </div>
        </div>
        <div class="regular">
            <div class="title-box">
                <div class="bg"></div>
                <p class="title">活动规则</p>
            </div>
            <ul>
                <li><i>1</i>活动时间：2018.7.10-7.20；</li>
                <li><i>2</i>活动期间，每消耗200积分可以获得1次翻牌机会，100％中奖，您可以通过投资、签到等获得积分；</li>
                <li><i>3</i>本次活动虚拟奖品将立即发放到账，实物奖品将在7个工作日内联系发放。</li>
            </ul>
        </div>
        <p class="tips">本活动最终解释权归温都金服所有<br>理财非存款 投资需谨慎</p>
    </div>
    <popout v-if="popout.isClosePopout" v-on:changeresult="closePopout" :awards="popout.awards"></popout>
    <giftslist v-if="isShowGiftsList" v-on:closelist="closeGiftsList" :awards-list-view="awardsListView"></giftslist>
</div>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?= FE_BASE_URI ?>libs/wxShare.js"></script>
<script>
    var C10 = '<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/180701_C10.png';
    var PI_DOVE = '<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/180701_PI_DOVE.png';
    var PI_COCK = '<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/180701_PI_COCK.png';
    var PI_SHUKE = '<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/180701_PI_SHUKE.png';
    var P200 = '<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/180701_P200.png';
    var PI_WEIDA = '<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/180701_PI_WEIDA.png';
    var PI_MARKET = '<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/180701_PI_MARKET.png';
    var PI_GOLD = '<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/180701_PI_GOLD.png';

    'use strict';

    var prizeBox = {
        template: '\n        <div @touchmove.prevent class="prize-bg">\n            <div class="prize-content">\n                <img @click.prevent="closePopout" class="close-prize" src="<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/close.png" alt="">\n                <div class="wrpper-top">\n                    <img @click.prevent src="<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/my_prize_title@2x.png" alt="">\n                </div>\n                <div class="wrapper">\n                    <p v-if="noPrize">\u60A8\u8FD8\u6CA1\u6709\u83B7\u5F97\u5956\u54C1</p>\n                    <ul class="content" style="background:#fff;">\n                        <li v-for="(item, index) in awardsListView">\n                            <div>\n                                <div class="lf">\n                                    <img :src="item.path" alt="">\n                                </div>\n                                <div class="rg-prize">\n                                    <p>{{item.name}}</p>\n                                    <p>{{item.awardTime}}</p>\n                                </div>\n                            </div>\n                        </li>\n                    </ul>\n                </div>\n            </div>\n        </div>\n        ',
        data: function data() {
            return {
                noPrize: false
            };
        },

        props: ['awardsListView'],
        created: function created() {
            this.awardsListView.length == 0 && (this.noPrize = true);
        },
        mounted: function mounted() {
            var scroll = new BScroll('.wrapper');
        },

        methods: {
            closePopout: function closePopout() {
                this.$emit('closelist');
            }
        }
    };
    var Popout = {
        template: '\n        <div @touchmove.prevent class="mask">\n        <div class="pop">\n            <img class="close"  @click.prevent="closePop" src="<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/close.png" alt="">\n            <img class="pop-bg"  @click.prevent src="<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/popout_bg.png" alt="">\n            <div class="reword-container">\n                <p>{{awards.rewordTitle}}</p>\n                    <img class="reword-img" :src="awards.linkUrl" @click.prevent alt="">\n                </div>\n                <div class="btn" @click="closePop">\u6536\u4E0B\u793C\u54C1</div>\n            </div>\n        </div>\n        ',
        props: ['awards'],
        methods: {
            closePop: function closePop() {
                this.$emit('changeresult');
            }
        }
    };
    axios.interceptors.request.use(function (config) {
        config.headers = {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'};
        return config;
    }, function (error) {
        console.log("错误的传参");
    });
    var vm = new Vue({
        el: '#p180701',
        data: {
            status: {
                promoStatus: dataJson.promoStatus,
                isLoggedIn: dataJson.isLoggedIn
            },
            isMoveFlop: false,
            isFlop: false,
            isFlopFlag:true,
            shuffleCopyright: "点击<br>翻牌<span style='color: #b74609;font-size: 0.2933333rem;text-align: center;position: absolute;display: block;width: 100%;left: 0;bottom: 0.16rem;'>(200积分/次)</span>",
            isComplete: false,
            awards: [{imgUrl: C10, name: "10元代金券"}, {imgUrl: PI_DOVE, name: '多芬洗漱套装'}, {
                imgUrl: PI_COCK,
                name: '公鸡头清洁剂'
            }, {imgUrl: PI_SHUKE, name: '舒克炭丝牙刷'}, {imgUrl: P200, name: '200积分'}, {
                imgUrl: PI_WEIDA,
                name: '维达抽纸'
            }, {imgUrl: PI_MARKET, name: '50元超市卡'}, {imgUrl: PI_GOLD, name: '999足金手串'}],
            points: '-',
            // 奖品列表弹框
            isShowGiftsList: false,
            awardsListView: [],
            /*中间弹框*/
            popout: {
                isClosePopout: false,
                awards: {
                    rewordTitle: '',
                    linkUrl: ''
                }
            },
            investUrl: {
                list24: '',
                list36: ''
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
            }
        },
        mounted: function mounted() {
            this.getPoints();
            this.setInvestUrl();
            wxShare.setParams("礼遇七月，年中回馈，积分抽奖限时开启！", "点击链接，立即参与~", "<?= Yii::$app->params['clientOption']['host']['wap'] ?>promotion/p180701/index", "https://static.wenjf.com/upload/link/link1530695111167902.png", "<?= Yii::$app->params['weixin']['appId'] ?>", "/promotion/p180701/add-share");
        },

        methods: {
            flop: function flop() {
                if (!this.baseVerify()) return;
                if (this.isFlop) {
                    this.toastCenter('去选一张卡牌吧');
                } else {
                    if (this.points < 200) {
                        this.toastCenter('积分不足');
                        return;
                    }
                    if(!this.isFlopFlag) return;
                    this.isFlop = !this.isFlop;
                    this.isFlopFlag = !this.isFlopFlag;
                    var timer800 = setTimeout(function () {
                        vm.isMoveFlop = !vm.isMoveFlop;
                        var timer2000 = setTimeout(function () {
                            vm.shuffleCopyright = "选择<br>卡牌";
                            vm.isMoveFlop = !vm.isMoveFlop;
                            vm.shuffle(vm.awards);
                            for (var i in vm.select) {
                                vm.select[i] = false;
                            }
                            var timer2800 = setTimeout(function(){
                                vm.isComplete = !vm.isComplete;
                                clearTimeout(timer2800);
                            },800);
                            clearTimeout(timer2000);
                        }, 1200);
                        clearTimeout(timer800);
                    }, 800);
                }
            },
            flopOne: function flopOne(index) {
                if (!this.isComplete) return;
                if (!this.baseVerify()) return;
                this.isComplete = !this.isComplete;
                var node = $(event.target).parent();
                var token = this.getQueryString('token') ? '&token=' + this.getQueryString('token') : '';
                axios.get('/promotion/p180701/get-reward?key=promo_180701' + token).then(function (response) {
                    var awardName = vm.getAwardName(response.data.result.sn);
                    vm.select['selected' + index] = true;
                    node.siblings('.gift').find('img').attr({'src': '<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/' + response.data.result.sn + '.png'});
                    node.siblings('.gift').find('p').html(awardName);
                    vm.flopCard(index, response.data.result.sn, node);
                }).catch(function (error) {
                    vm.toastCenter(error.response.data.message);
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
                            vm.popout.awards.linkUrl = '<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/' + award + '.png';
                            vm.getPoints();
                            vm.shuffleCopyright = "点击<br>翻牌<span style='color: #b74609;font-size: 0.2933333rem;text-align: center;position: absolute;display: block;width: 100%;left: 0;bottom: 0.16rem;'>(200积分/次)</span>";
                            vm.isFlopFlag = !vm.isFlopFlag;
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
                /*
                * 1、获取对应的奖励
                * 2、遍历数组交换award和在award上的奖品
                * 3、 返回对应的奖品列表
                * */
                var imgAwardUrl = '<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/' + award + '.png';
                this.awards.map(function (item, index) {
                    if (item.imgUrl === imgAwardUrl) {
                        var _ref2 = [vm.awards[index], vm.awards[indexNum]];
                        vm.awards[indexNum] = _ref2[0];
                        vm.awards[index] = _ref2[1];
                    }
                });
            },
            getPoints: function getPoints() {
                if (!this.status.isLoggedIn) return;
                var token = this.getQueryString('token') ? '?token=' + this.getQueryString('token') : '';
                axios.get('/promotion/p180701/get-points' + token).then(function (response) {
                    vm.points = response.data.points;
                }).catch(function (error) {
                    console.log(error.response.data.message);
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
                        axios.get('/promotion/award-list/index?key=promo_180701' + token).then(function (response) {
                            vm.awardsListView = [];
                            response.data.forEach(function (item) {
                                item.path = "<?= FE_BASE_URI ?>wap/campaigns/active20180701/images/" + item.path;
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

            setInvestUrl: function setInvestUrl() {
                if (this.status.isLoggedIn) {
                    this.investUrl.list24 = "/promotion/p180620/list?bidTime=24";
                    this.investUrl.list36 = "/promotion/p180620/list?bidTime=36";
                } else {
                    this.investUrl.list24 = this.investUrl.list36 = "/site/login";
                }
            },
            getAwardName: function getAwardName(sn) {
                switch (sn) {
                    case '180701_C10':
                        return '10元代金券';
                        break;
                    case '180701_P200':
                        return '200积分';
                        break;
                    case '180701_PI_WEIDA':
                        return '维达抽纸';
                        break;
                    case '180701_PI_SHUKE':
                        return '舒客炭丝牙刷';
                        break;
                    case '180701_PI_COCK':
                        return '公鸡头清洁剂';
                        break;
                    case '180701_PI_DOVE':
                        return '多芬洗漱套装';
                        break;
                    case '180701_PI_MARKET':
                        return '50元超市卡';
                        break;
                    case '180701_PI_GOLD':
                        return '999足金手串';
                        break;
                    default:
                        break;
                }
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
                //return null;
            }
        },
        components: {
            'giftslist': prizeBox,
            'popout': Popout
        }
    });
</script>