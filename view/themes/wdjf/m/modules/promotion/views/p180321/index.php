<?php

$this->title = '极速答题赢宝箱';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=1.0">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180309/css/index.min.css?v=1.2">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180309/css/gifts-list.min.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>libs/iscroll.js"></script>
<script src="<?= FE_BASE_URI ?>libs/handlebars-v4.0.10.js"></script>
<script src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/js/gifts-list.js"></script>

<style>
    .flex-content .competition .cpt-area ul li:last-child div.isTrue {
        background: url(<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/btn_02.png) no-repeat;
        background-size: 100% 100%;
    }

    .flex-content .competition .cpt-area ul li:last-child div.isFalse {
        background: url(<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/btn_03.png) no-repeat;
        background-size: 100% 100%;
    }

    .obtains {
        width: 1.33333333rem !important;
    }
</style>
<div class="flex-content" id="app" v-cloak>
    <!--答题场景页-->
    <div v-if="isShowScene">
        <div class="banner"><img  onclick="return false;" src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/banner.png" alt=""></div>
        <div class="operate-area">
            <img  onclick="return false;" class="box" src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/treasure_box.png" alt="">
            <img @click="giftListExamine" class="gift-list"
                 src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/gift_list.png" alt="">
            <img v-if="!isShowRegular" @click="showRegular" class="regular-list" src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/regular.png" alt="">
            <img @click="timeDown(3,showTimeDown,event)" class="answer"
                 src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/answer.png" alt="">
<!--            <p class="regular-btn" v-if="!isShowRegular" @click="showRegular"><span>活动规则</span></p>-->
            <div class="active-footer">
                <img onclick="return false;" src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/active-footer.png" alt="">
            </div>
        </div>
    </div>

    <!--倒计时开始-->
    <div v-if="isShowTimeDown" class="count-down-box">
        <img  onclick="return false;" class="start-bg" src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/start_bg.png" alt="">
        <img  onclick="return false;" class="time-down" :src="timeDownSrc" alt="">
    </div>

    <!--答题页面-->
    <div class="competition" v-if="!isShowScene">
        <div v-if="isAnswer" class="cpt-area">
            <ul>
                <li class="clearfix">
                    <img  onclick="return false;" class="lf text-png" src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/text.png"
                         alt="">
                    <div class="rg">
                        <div :style="{width:progressWidth+'%'}" class="progress">
                            <div class="time-count-down" v-html="totalTime+'s'"></div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="title">第{{question.id}}题</div>
                    <div class="ctn">{{question.title}}</div>
                </li>
                <li class="clearfix">
                    <div @click="answering(1)" id="correct-btn" class="correct lf animated">正确</div>
                    <div @click="answering(0)" id="error-btn" class="error rg animated">错误</div>
                </li>
            </ul>
        </div>
        <div v-if="!isAnswer" class="results">
            <ul>
                <li>
                    <p>30秒共答对{{count}}题<br>你被评为<span>{{designation}}</span></p>
                    <p>
                        <span>获得{{treasure}}宝箱：</span>
                        <img  onclick="return false;" :class="{obtains:isGold}"
                             src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/box_01.png" alt="">
                        <img onclick="return false;" :class="{obtains:isSilver}"
                             src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/box_02.png" alt="">
                        <img onclick="return false;" :class="{obtains:isCopper}"
                             src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/box_03.png" alt="">
                    </p>
                    <p><img  onclick="return false;" :src="box" alt=""></p>
                </li>
                <li v-if="isOpenBox" class="special">
                    <div @click="openBox" class="error">开启宝箱</div>
                </li>
                <li v-if="!isOpenBox" class="clearfix">
                    <div @click="weixinShare" class="correct lf weixinShare">去分享</div>
                    <div @click="openBox" class="error rg">再玩一次</div>
                </li>
            </ul>
<!--            <div class="result-regular" style="margin-top: 0;"><span @click="showRegular">活动规则</span></div>-->
            <img @click="giftListExamine" class="gift-list"
                 src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/gift_list.png" alt="">
            <img @click="showRegular" class="regular-list" src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/regular.png" alt="">
        </div>
    </div>
    <!--活动规则-->
    <div v-if="isShowRegular" class="regular" @touchmove.prevent>
        <img @click="showRegular" class="close" src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/close.png"
             alt="">
        <p>活动规则</p>
        <ul>
            <li>1、活动时间：2018.3.22至3.28；</li>
            <li>2、游戏玩法：在30秒倒计时内，用最快速度答对题，题目是判断题，选择“正确”和“错误”选项答题，每题答完后，会自动进入下一题，直到30秒结束为止；</li>
            <li>3、完成答题后，将会授予你荣誉称号，并获得相应的宝箱类型（黄金宝箱、白银宝箱、青铜宝箱），开启宝箱，抽取奖励；</li>
            <li>4、每轮答题不限题目数量，在30秒限时内，答题越多，获得奖励的几率越高哦！欢迎您邀请亲朋好友一起来玩，抢走大红包！</li>
            <li>5、活动期间每位用户每天最多有2次免费游戏机会，其中第2次游戏机会必须分享本活动到朋友圈才能获得；</li>
            <li>6、活动期间每天的游戏机会将在次日0点重置，请当日使用完；</li>
            <li>7、活动期间，每天2次游戏机会用完后，可以次日进入本活动页继续参与游戏；</li>
            <li>8、本次活动虚拟奖品将立即发放到账，实物奖品将在7个工作日内与您联系；</li>
            <li>9、本活动最终解释权归温都金服所有。</li>
        </ul>
    </div>

    <!--弹框从这开始-->
    <!--答题机会用完弹框-->
    <div class="mask" v-if="noChance">
        <div class="pop">
            <img @click="closeMask" class="pop-close"
                 src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/pop_close.png" alt="">
            <p class="pop-title">您今天已经用完了全部答题<br>机会，请明天再来吧！</p>
            <div  @click="closeMask" class="confirm">知道了</div>
            <p>提示:活动期间每天都能来答题哦！</p>
        </div>
    </div>
    <!--答题分享到朋友圈再次答题-->
    <div class="mask" v-if="hasOneChance">
        <div class="pop">
            <img @click="closeMask" class="pop-close"
                 src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/pop_close.png" alt="">
            <p class="pop-title">您没有答题次数了，分享到<br>朋友圈还能再玩一次哦！</p>
            <div @click="weixinShare" class="confirm weixinShare">立即分享</div>
            <p>提示:必须分享到朋友圈才有效哦！</p>
        </div>
    </div>

    <!--开启宝箱获得奖励弹框-->
    <div v-if="win" class="mask">
        <div class="pop-special">
            <img @click="closeMask" class="pop-close"
                 src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/pop_close.png" alt="">
            <p class="pop-title">宝箱已开启</p>
            <p class="pop-subtitle">恭喜获得{{reward.ref_amount}}元红包</p>
            <img  onclick="return false;" class="gifts" src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/coupon.png" alt="">
            <div class="clearfix">
                <div @click="weixinShare" class="share lf weixinShare"></div>
                <div @click="replay" class="restart rg">再玩一次</div>
            </div>
        </div>
    </div>

    <!--开启宝箱未中奖-->
    <div v-if="lose" class="mask">
        <div class="pop-special">
            <img @click="closeMask" class="pop-close"
                 src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/pop_close.png" alt="">
            <p class="pop-title">宝箱已开启</p>
            <p class="pop-subtitle" style="margin-top: 0.666667rem">遗憾，您没有中奖</p>
            <img onclick="return false;" class="no-gifts" src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/no_gitfs_01.png" alt="">
            <div class="clearfix">
                <div @click="weixinShare" class="share lf weixinShare"></div>
                <div @click="replay" class="restart rg">再玩一次</div>
            </div>
        </div>
    </div>

    <!--添加活动攻略-->
    <div v-if="isShowStrategy" class="mask">
        <div class="strategy">
            <img @click="closeMask" class="pop-close" src="<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/pop_close.png" alt="">
            <p>30秒快速答题小游戏，<br>答对题目越多，<br>越有机会赢得高级宝箱哦！</p>
            <div @click="closeMask" class="confirm">知道了</div>
        </div>
    </div>
</div>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?= FE_BASE_URI ?>libs/wxShare.js?v=3.0"></script>
<script>
    //    console.log(dataJson);
    FastClick.attach(document.body);
    var app = new Vue({
        el: '#app',
        data: {
            promoStatus: dataJson.promoStatus,
            isLogin: dataJson.isLoggedIn,
            /*第一页场景*/
            isShowScene: true,
            timeDownSrc: "<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/3.png",
            isShowTimeDown: false,
            isShowRegular: false,
            progressWidth: 10 * dataJson.time / 3,
            totalTime: dataJson.time,
            /*是否在答题ing*/
            isAnswer: true,
            /*防止重复点击*/
            isRepeatClick: true,
            /*状态锁住*/
            isLock: false,
            win: false,
            lose: false,
            /*开启宝箱*/
            isOpenBox: true,
            box: "",
            noChance: false,
            hasOneChance: false,
            reward: {
                ref_amount: '',
            },
            question: {
                id: 1,
                title: dataJson.questions.length ? dataJson.questions[0].title : ""
            },
            counter: 0,
            count: dataJson.sessionCount,
            questions: dataJson.questions,
            isStopAnswer: false,
            /*获得什么宝箱*/
            isGold: false,
            isSilver: false,
            isCopper: false,
            /*称号*/
            designation: "",
            canStartFlag:false,
            treasure:'',
            isShowStrategy:dataJson.requirePopRegular
        },
        created: function () {
            var _this = this;
            wxShare.setParams("测测你的反应速度是多少？我正在玩【竞速答题赢宝箱 】游戏，红包疯抢中，快来玩吧！", "点击链接，马上参与~", "<?= Yii::$app->params['clientOption']['host']['wap'] ?>/promotion/p180321/index", "https://static.wenjf.com/upload/link/link1521546018709563.jpg", "<?= Yii::$app->params['weixin']['appId'] ?>", "/promotion/p180321/add-share");
            wxShare.TimelineSuccessCallBack = function () {
                $.get("/promotion/p180321/add-share?scene=timeline&shareUrl=" + encodeURIComponent(location.href))
            };
            if(dataJson.time < 30 && dataJson.time>0){
                this.isShowScene = false;
                this.progress();
            } else {
                /*答题结果的判断*/
                if(dataJson.result.status == 1){
                    this.isShowScene = this.isAnswer = false;
                    this.count = dataJson.result.answer.count;
                    this.answerEnd();
                }
            }


        },
        methods: {
            baseVerify: function () {
                var _this = this;
                switch (_this.promoStatus) {
                    case 0:
                        if (_this.isLogin) {
                            return true;
                        } else {
                            _this.toastCenter('未登录',function(){
                                window.location.href = "/site/login";
                            });
                            return false;
                        }
                        break;
                    case 1:
                        _this.toastCenter('活动未开始');
                        return false;
                        break;
                    case 2:
                        _this.toastCenter('活动已结束');
                        return false;
                        break;
                    default:
                        break;
                }
            },
            baseVerifySpecial: function () {
                var _this = this;
                switch (_this.promoStatus) {
                    case 0:
                        if (_this.isLogin) {
                            return true;
                        } else {
                            _this.toastCenter('未登录',function(){
                                window.location.href = "/site/login";
                            });
                            return false;
                        }
                        break;
                    case 1:
                        _this.toastCenter('活动未开始');
                        return false;
                        break;
                    case 2:
                        return true;
                        break;
                    default:
                        break;
                }
            },
            weixinShare: function () {
                var status = this.baseVerify();
                if (status) {
                    $('.weixinShare').addClass('share-btn');
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
            },
            timeDown: function (time, callBack,event) {
                var event = event || window.event;
                event.preventDefault();
                var _this = this;
                var status = this.baseVerify();
                if (status && this.isRepeatClick) {
                    this.isRepeatClick = !this.isRepeatClick;
                    this.isLock = !this.isLock;
                    this.begin(function(){
                        _this.isShowTimeDown = true;
                        var timer = setInterval(function () {
                            if (time < 1) {
                                clearInterval(timer);
                                _this.isLock = !_this.isLock;
                                callBack();
                                _this.isShowScene = !_this.isShowScene;
                                _this.isRepeatClick = !_this.isRepeatClick;
                                _this.progress();
                                return false;
                            } else {
                                time--;
                                _this.timeDownSrc = "<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/" + time + ".png";
                            }
                        }, 1000)
                    });
                }
            },
            showRegular: function () {
                if (!this.isLock) {
                    this.isShowRegular = !this.isShowRegular;
                }
            },
            showTimeDown: function () {
                this.isShowTimeDown = false;
                this.timeDownSrc = "<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/3.png";
            },
            progress: function () {
                var _this = this;
                var progress = setInterval(function () {
                    if (_this.progressWidth < 6) {
                        clearInterval(progress);
                    } else {
                        _this.progressWidth = _this.progressWidth - 0.33333333;
                    }
                }, 100);
                var timer = setInterval(function () {
                    if (_this.totalTime < 1 && _this.progressWidth < 6) {
                        _this.isAnswer = !_this.isAnswer;
                        _this.progressWidth = 100;
                        _this.totalTime = 30;
                        _this.answerEnd();
                        clearInterval(timer);
                    } else {
                        _this.totalTime--;
                    }
                }, 1000);
            },
            giftListExamine: function () {
                var _this = this;
                var status = this.baseVerifySpecial();
                if (status) {
                    $.ajax({
                        type: "GET",
                        url: "/promotion/p180321/award-list?key=promo_180321",
                        data: "",
                        success: function (data) {
                            if (data.length != 0) {
                                var lists = [];
                                $.each(data, function (i, n) {
                                    if (n.sn !== '180318_ZW') {
                                        var obj = {};
                                        obj.path = '<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/coupon.png';
                                        obj.name = n.name;
                                        obj.awardTime = "中奖时间 " + n.awardTime;
                                        lists.push(obj);
                                    }
                                });
                                giftsList({
                                    isGifts: true,//有奖品，无奖品为false
                                    closeImg: '<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/pop_close.png',
                                    list: lists
                                });
                            } else {
                                giftsList({
                                    isGifts: false,//无奖品为false
                                    closeImg: '<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/pop_close.png',
                                    list: []
                                });
                            }
                        },
                        error: function (error) {
                            _this.toastCenter(error)
                        }
                    })
                }
            },
            /*开始答题接口*/
            begin: function (callBack) {
                var _this = this;
                $.get('/promotion/p180321/begin')
                    .done(function (data) {
                        callBack();
                    })
                    .fail(function (jqXHR) {
                        if (400 === jqXHR.status && jqXHR.responseText) {
                            var resp = jqXHR.responseJSON;
                            if (resp.code == 4) {
                                _this.hasOneChance = !_this.hasOneChance;
                            } else if (resp.code == 5) {
                                _this.noChance = !_this.noChance;
                            } else {
                                _this.toastCenter(resp.message);
                            }
                    }
                    _this.isRepeatClick = true;
                    _this.isLock = false;
                });
            },
            openBox: function () {
                var _this = this;
                if (_this.isOpenBox) {
                    var xhr = $.get("/promotion/p180321/open");
                    xhr.done(function (data) {
                        if(data.ticket.sn == "180318_ZW") {
                            _this.lose = !_this.lose;
                        } else {
                            _this.reward.ref_amount = Math.ceil(data.ticket.ref_amount);
                            _this.win = !_this.win;
                            _this.isOpenBox = !_this.isOpenBox;
                            if (_this.count > 5) {
                                _this.treasure = "黄金";
                                _this.box = "<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/box_on_01.png";
                            } else if (_this.count < 3) {
                                _this.treasure = "青铜";
                                _this.box = "<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/box_on_03.png";
                            } else {
                                _this.treasure = "白银";
                                _this.box = "<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/box_on_02.png";
                            }
                        }
                    });
                    xhr.fail(function (jqXHR) {
                        if (400 === jqXHR.status && jqXHR.responseText) {
                            var resp = jqXHR.responseJSON;
                            _this.toastCenter(resp.message);
                        }
                    });
                } else {
                    this.replay();
                }
            },
            closeMask: function () {
                this.win = this.lose = this.noChance = this.hasOneChance = this.isShowStrategy = false;
            },
            replay: function () {
                this.isShowScene = !this.isShowScene;
                this.isAnswer = !this.isAnswer;
                this.win = this.lose = false;
                this.isOpenBox = !this.isOpenBox;
                this.box = "";
                this.isStopAnswer = false;
                this.counter = 0;
                this.question.id = 1;
                this.isGold = this.isSilver = this.isCopper = false;
                this.designation = "";
                var _this = this;
                $.ajax({
                    type: "GET",
                    url: "/promotion/p180321/questions",
                    data:"",
                    success:function(data){
                        _this.questions = data?data:_this.questions;
                        _this.question.title = _this.questions[0].title;
                    },
                    error:function(err){_this.toastCenter()},
                })
            },
            answering: function (value) {
                var _this = this;
                if (!this.isStopAnswer) {
                    this.isStopAnswer = !this.isStopAnswer;
                    $.ajax({
                        type: "GET",
                        url: "/promotion/p180321/answer",
                        data: {qid: _this.questions[_this.counter].id, opt: value},
                        success: function (data) {
                            /*开始换题*/
                            _this.counter++;
                            _this.count = data.count;
                            if (data.code == 1) {
                                if (value == 1) {
                                    $("#correct-btn").addClass("isTrue");
                                } else {
                                    $("#error-btn").addClass("isTrue");
                                }
                            } else if (data.code == 0) {
                                if (value == 1) {
                                    $("#correct-btn").addClass("isFalse shake");
                                } else {
                                    $("#error-btn").addClass("isFalse shake");
                                }
                            }
                            var timer = setTimeout(function () {
                                if (!_this.questions[_this.counter]) return false;
                                $("#correct-btn,#error-btn").removeClass("isTrue").removeClass("isFalse").removeClass("shake");
                                _this.isStopAnswer = !_this.isStopAnswer;
                                _this.question.id++;
                                _this.question.title = _this.questions[_this.counter].title;
                                clearTimeout(timer);
                            }, 700)
                        },
                        error: function (err) {
                            this.toastCenter(err)
                        }
                    })
                }

            },
            answerEnd: function () {
                if (this.count > 5) {
                    this.designation = "极速达人";
                    this.isGold = !this.isGold;
                    this.box = "<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/box_off_01.png";
                } else if (this.count < 3) {
                    this.designation = "先行者";
                    this.isCopper = !this.isCopper;
                    this.box = "<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/box_off_03.png";
                } else {
                    this.designation = "冲刺能手";
                    this.isSilver = !this.isSilver;
                    this.box = "<?= FE_BASE_URI ?>wap/campaigns/active20180309/images/box_off_02.png";
                }
            }
        }
    });
    //    Vue.config.devtools = true;
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