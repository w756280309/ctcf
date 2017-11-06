<?php
$this->title = '11月理财节';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/popover.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20171111/css/page-third.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/popover.js?v=2.0"></script>
<script src="<?= FE_BASE_URI ?>libs/iscroll.js"></script>
<script  src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/js/rain.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<div class="flex-content" id="app">
    <div id="box" class="red-box"></div>
    <div id="mask"></div>
    <div class="banner">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-third/banner-top.png" alt="banner">
    </div>
    <div class="banner banner-bottom">
        <!--  我的奖品入口 -->
        <a class="my-prize" @click="computNum" data-num="0"></a>
        <!--  喜卡兑换入口 -->
        <a class="active-link left-link" @click="computNum" data-num="1" v-if="activeTicketCount > 0"><span>我的喜卡：<i class="card" v-cloak>{{activeTicketCount}}</i>张</span></a>
        <a class="active-link left-link" data-num="1" v-if="activeTicketCount == 0"><span>我的喜卡：<i class="card" v-cloak>{{activeTicketCount}}</i>张</span></a>
        <!--  红包雨入口 -->
        <a class="active-link right-link" @click="rainResult" ></a>
    </div>
    <div class="banner-title"></div>
    <div class="banner-rate" >已累计年化：<span v-cloak><?= $totalMoney ?></span>万元</div>
    <div class="banner-rule"></div>
    <ul class="invest-box clearfix">
        <li class="first"><a href="/deal/deal/index"></a></li>
        <li class="second"><a href="/deal/deal/index"></a></li>
        <li class="third"><a href="/deal/deal/index"></a></li>
    </ul>
    <p class="last-tips" @click="openGladCard" >本活动最终解释权归温都金服所有</p>
    <!-- 喜卡 -->
    <div class="glad-box" :class="[!!beforeGladCard ? 'showed': '' ]">
        <div class="outer-box no-open">
            <img @click="openGladCard" class="chai-btn open-glad-card" src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-third/chai-open.png" alt="拆红包">
        </div>
    </div>
    <div class="glad-box" :class="[!!afterGladCard ? 'showed': '' ]">
        <div class="outer-box yes-open">
            <p class="happy-money"><span>￥</span>{{happyMoney}}</p>
            <a @click="closeGladCard" class="chai-btn close-glad-card"></a>
        </div>
    </div>
    <!-- 红包雨 -->
    <div class="rea-packet-box number-start-box" :class="[!!numberBox ? 'showed': '' ]">
        <img class="number number_3" src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-third/number_3.png" alt="3">
        <img class="number number_2" src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-third/number_2.png" alt="2">
        <img class="number number_1" src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-third/number_1.png" alt="1">
        <img class="number number_0" src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-third/number_0.png" alt="开始">
    </div>
    <!-- 游戏结果 弹框 -->
    <div class="rea-packet-box" :class="[!!rainResultBox ? 'showed': '' ]">
        <div class="rain-result">
            <!-- 倒计时 -->
            <p class="countDown">00:00</p>
            <div class="result-content">
                <span class="redPacket-count yellow-redPacket-count">{{goldRedCount}}</span>
                <span class="redPacket-count red-redPacket-count">{{redCount}}</span>
            </div>
            <p class="result-number result-txt">收集红包：<i>{{allRedCount}}</i></p>
            <p class="grade result-txt">综合评分：<img :src="rank" alt="等级"></p>
            <!--  宝箱  -->
            <a class="draw-box" >
                <img class="drawer" v-if="drawBoxStatus == 'true'" @click="computNum" data-num="2"  src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-third/draw-box-before.png" alt="未开启宝箱">
                <img v-else  src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-third/draw-box-after.png" alt="已开启宝箱">
                <img :class="[!!drawBoxStatus == 'true' ? 'show': '' ]" src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-third/hander-icon.png" alt="" class="hand">
            </a>
            <div class="btn-box">
                <a class="box-shadow" @click="starting">再玩一次</a>
                <a class="box-shadow" @click="rainResult">返回主会场</a>
            </div>
        </div>
    </div>
    <!-- 玩游戏询问 弹框 -->
    <div class="start-play-box" :class="[ !!startPlayBox ? 'showed' : '' ]">
        <div class="outer-box">
            <img class="pop_close" src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/pop_close.png" @click="startPlay" alt="">
            <div class="prizes-pomp">
                <a class="close-start-btn yes-btn" @click="starting"></a>
                <a class="close-start-btn no-btn" @click="startPlay"></a>
            </div>
        </div>
    </div>
    <!--对应的奖品列表-->
    <div class="prizes-box" :class="[ !!myPrizeBox ? 'showed' : '' ]">
        <div class="outer-box">
            <img class="pop_close" src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/pop_close.png" @click="myPrize" alt="关闭按钮">
            <div class="prizes-pomp">
                <p class="prizes-title">奖品列表</p>
                <div id="wrapper">
                    <ul>
                        <li class="clearfix" v-for="item in ticket">
                            <div class="lf"><img :src="item.path" alt="礼品"></div>
                            <div class="lf">
                                <p>{{item.name}}</p>
                                <p>中奖时间:{{item.awardTime}}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="drawBoxStatus" value="<?= $drawBoxStatus ?>">
    <input type="hidden" name="activeTicketCount" value="<?= $activeTicketCount ?>">
</div>
<script>
    var promoStatus = $('input[name=promoStatus]').val();
    var isLoggedin = $('input[name=isLoggedin]').val();
    var drawBoxStatus = $('input[name=drawBoxStatus]').val();
    var activeTicketCount = $('input[name=activeTicketCount]').val();
    var rain = '';
    var myScroll = '';
    var allowClick1 = true;
    var allowClick2 = true;
    var app = new Vue({
        el: '#app',
        data: {
            obj:'',
            happyMoney:'',
            count: 15,
            timer: '',
            redCount: 0,
            rank: '<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-third/A.png',
            goldRedCount: 0,
            allRedCount: 0,
            ticket: {path:'',name:'',awardTime:''}, // 奖品列表
            prize: { //中奖
                path:'<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-third/prizes/coupon-5.png',name:'2017110820',time:'2017-03-16'
            },
            promoStatus: promoStatus,
            isLoggedin: isLoggedin,
            drawBoxStatus: drawBoxStatus,
            activeTicketCount: activeTicketCount,
            myPrizeBox: false, //我的奖品弹框
            rainResultBox: false, //红包雨结果页面
            startPlayBox: false, //初始 进入红包雨页面
            numberBox: false, //数字倒计时
            beforeGladCard: false, // 未拆喜卡
            afterGladCard: false, // 已拆喜卡
            isActive: false
        },
        created: function(){
            this.drawBoxStatus = this.isLoggedin === 'true' ? this.drawBoxStatus : 'true';
            var that = this;
            $(function(){
                FastClick.attach(document.body);
                that.init();
            });
        },
        methods: {
            init: function (event) {
                var e = event || window.event;
                this.startPlay(e);
            },
            computNum: function (event){
                var e = event || window.event;
                var that = this;
                var n = e.currentTarget.getAttribute('data-num');
                switch(n) {
                    case '0':
                        that.obj = 'my-prize';
                        that.showPromoStatus('my-prize');
                        break;
                    case '1':
                        that.obj = 'left-link';
                        that.showPromoStatus('left-link');
                        break;
                    case '2':
                        that.obj = 'drawer';
                        that.showPromoStatus('drawer');
                        break;
                }
            },
            showLoginPop: function(event) {
                var e = event || window.event;
                var that = this;
                if (that.isLoggedin === 'true') { //已登录
                    if (that.promoStatus === '0') { // 活动进行中
                        if (that.obj === 'my-prize') {
                            that.myPrize(e);
                        } else if (that.obj === 'drawer') {
                            that.isDrawBox(e);
                        } else if (that.obj === 'left-link') {
                            that.openGladCard(e);
                        }
                    } else {
                        if (that.obj === 'my-prize') {
                            that.myPrize(e);
                        }
                    }
                } else if (that.isLoggedin === 'false') {
                    if (that.promoStatus === '0') { // 活动进行中
                        that.goToLogin('未登录,  去登录弹框');
                        return false;
                    } else if (that.promoStatus === '1'){
                        toastCenter('活动未开始');
                    } else if (that.promoStatus === '2'){
                        toastCenter('活动已结束');
                    }
                }
            },
            showPromoStatus: function(event) { //活动状态
                var e = event || window.event;
                var that = this;
                if (that.promoStatus == 0){ //进行中
                    that.showLoginPop(that.obj);
                    return false;
                } else if (that.promoStatus == 1) {
                    toastCenter('活动未开始');
                } else if (that.promoStatus == 2) {
                    toastCenter('活动已结束');
                }
            },
            startPlay: function(event) {
                var e = event || window.event;
                var that = this;
                if (that.startPlayBox) {
                    that.startPlayBox = !that.startPlayBox;
                    $('body').off('touchmove');
                } else {
                    that.startPlayBox = !that.startPlayBox;
                    $('body').on('touchmove', function(e){that.eventTarget(e);}, false);
                }
            },
            starting: function(event){
                var e = event || window.event;
                var that = this;
                that.downNumber(e);
                that.startPlayBox = false;
                that.rainResultBox = false;
                that.redCount = 0;
                that.goldRedCount = 0;
            },
            downNumber: function(event) {
                var e = event || window.event;
                var that = this;
                var n = 3;
                if (that.numberBox) {
                    that.numberBox = !that.numberBox;
                    $('body').off('touchmove');
                } else {
                    that.numberBox = !that.numberBox;
                }
                var ts = setInterval(function(e){
                    $('.number').hide();
                    $('.number_'+n).show();
                    if (n < 0) {
                        clearInterval(ts);
                        that.playGame(e);
                    }
                    n--;
                },1000);
                $('body').on('touchmove', function(e){that.eventTarget(e)}, false);
            },
            playGame: function(event) {
                var e = event || window.event;
                var that = this;
                that.numberBox = !that.numberBox;
                var a = Math.floor(that.count);
                if (a < 10) {
                    a = "0"+a;
                }
                $('#box').append('<p class="countDown">00:'+a+'</p>');
                if (that.rainResultBox) { // 重新玩
                    that.rainResultBox = !that.rainResultBox;
                    $('body').off('touchmove');
                    $('.countDown').html("00:"+that.count);
                    that.redCount = 0;
                    that.goldRedCount = 0;
                }
                that.rainLoaded(e);
                $.get('/data/data-new.json',function(res) {rain.start(res);});
                that.downTime(e);
                $('#mask,#box').show();
                $('body').on('touchmove',function(e){ that.eventTarget(e)}, false);
            },
            showPop: function () { // 游戏结果
                var that = this;
                that.allRedCount = parseInt(that.redCount)+parseInt(that.goldRedCount);
                var rank = that.allRedCount;
                if ( rank < 5 ) {
                    that.rank = '<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-third/A.png';
                } else if ( rank < 15 ) {
                    that.rank = '<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-third/B.png';
                } else if ( rank < 30 ) {
                    that.rank = '<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-third/C.png';
                } else if ( rank < 45 ) {
                    that.rank = '<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-third/D.png';
                } else {
                    that.rank = '<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-third/S.png';
                }
            },
            isDrawBox: function () { // 开宝箱
                if(!allowClick1) {
                    return false;
                }
                var that = this;
                var xhr = $.get('/promotion/p171111/draw?key=promo_million_1111');
                allowClick1 = false;
                xhr.done(function(data) {
                    poptpl.popComponent({
                        popBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-third/pop_bg_success.png) no-repeat',
                        popBorder: 0,
                        closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/pop_close.png",
                        btnMsg: "收下礼品",
                        popTopColor: "#f03350",
                        bgSize: "100% 100%",
                        title: '<p style="font-size:0.72rem;">恭喜您获得<span style="display:block;font-size: 0.50666667rem"></span></p>',
                        popBtmBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/pop_btn.png) no-repeat',
                        popMiddleHasDiv: true,
                        popBtmColor: '#e01021',
                        contentMsg: "<img style='margin: -0.5rem auto 0.5rem;display: block;width: 4.8rem;' src='<?= FE_BASE_URI ?>"+data.ticket.path+"' alt='图片地址'/>",
                        popBtmBorderRadius: 0,
                        popBtmFontSize: ".50666667rem"
                    }, 'close');
                    this.drawBoxStatus = 'false';
                    allowClick1 = true;
                });
                xhr.fail(function(jqXHR) {
                    if (400 === jqXHR.status && jqXHR.responseText) {
                        toastCenter('系统繁忙，请稍后重试！');
                    }
                    allowClick1 = true;
                });
            },
            myPrize: function(event) {
                var that = this;
                var e = event || window.event;
                if (that.myPrizeBox) {
                    that.myPrizeBox = !that.myPrizeBox;
                    $('body').off('touchmove');
                } else {
                    var that = this;
                    var xhr = $.get('/promotion/p171111/third-award-list');
                    xhr.done(function(data) {
                        if (data.length == 0) { //无奖品
                            poptpl.popComponent({
                                popBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/pop_bg_waiting.png) no-repeat',
                                popBorder: 0,
                                closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/pop_close.png",
                                popBtmHas: false,
                                popTopColor: "#f03350",
                                bgSize: "100% 100%",
                                title: '<p style="font-size:0.72rem;">我的奖品</p>',
                                popBtmBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/pop_btn.png) no-repeat',
                                popMiddleHasDiv: true,
                                contentMsg: "<p style='color:#f03350;margin-top: 2rem;font-size:0.56rem;'>您还没有获得奖品哦！</p>"
                            }, 'close');
                        } else {
                            that.ticket = data;
                            for (var i = 0;i < that.ticket.length; i++) {
                                var awardTime = that.ticket[i].awardTime;
                                var time = Date.parse(awardTime.replace(/-/g,"/"));
                                var year = new Date(time).getFullYear();
                                var month = new Date(time).getMonth()+ 1;
                                var day = new Date(time).getDate();
                                that.ticket[i].awardTime = year+'年'+month+'月'+day+'日';
                                that.ticket[i].path = '<?= FE_BASE_URI ?>'+that.ticket[i].path;
                            }
                            that.myPrizeBox = !that.myPrizeBox;
                        }
                        setTimeout(function(e){ that.loaded(e); },50);
                        $('body').on('touchmove',function(e){that.eventTarget(e)}, false);
                    });
                    xhr.fail(function(jqXHR) {
                        if (400 === jqXHR.status && jqXHR.responseText) {
                            toastCenter('系统繁忙，请稍后重试！');
                        }
                    });
                }
            },
            rainResult: function(event) {
                var that = this;
                var e = event || window.event;
                if (that.rainResultBox) {
                    that.rainResultBox = !that.rainResultBox;
                    $('body').off('touchmove');
                } else {
                    that.rainResultBox = !that.rainResultBox;
                    $('body').on('touchmove',function(e){that.eventTarget(e)}, false);
                }
            },
            loaded: function(event) {
                var e = event || window.event;
                myScroll = new iScroll('wrapper',{
                    vScrollbar:false,
                    hScrollbar:false
                });
            },
            rainLoaded: function(event) {
                var e = event || window.event;
                var that = this;
                var el = that.createId('box');
                rain =  new redPack({
                    el: el,         // 容器
                    speed: 10,      // 速度，越小越快
                    density: 200,   //  红包密度，越小越多
                    callback: function(e) {  // 点击红包的回调
                        if (e.target.getAttribute('redClassName') === 'redpack') {
                            that.redCount++;
                        } else if (e.target.getAttribute('redClassName') === 'gold-redpack'){
                            that.goldRedCount++;
                        }
                    }
                });
            },
            createId: function(id) {
                return document.getElementById(id);
            },
            downTime: function(event) {
                var e = event || window.event;
                var that = this;
                var el = that.createId('box');
                var t = setInterval(function (e) {
                    that.count--;
                    var a = Math.floor(that.count);
                    if (a < 10) {
                        a = "0"+a;
                    }
                    $('.countDown').html("00:"+ a);
                    if (that.count < 0 ) {
                        clearInterval(t);
                        that.count = 15;
                        rain.stop();
                        rain.clear(el);
                        $('.countDown').html("00:00");
                        that.showPop();
                        setTimeout(function(e) {
                            $('#mask,#box').hide();
                            that.rainResult(e);
                        },800);
                    }
                }, 1000);
            },
            openGladCard: function(event) { // 喜卡
                var e = event || window.event;
                var that = this;
                if(!allowClick2) {
                    return false;
                }
                if (that.beforeGladCard) {
                    $('.open-glad-card').addClass('rotate');
                    var xhr = $.get('/promotion/p171111/draw?key=promo_171111');
                    allowClick2 = false;
                    xhr.done(function(data) {
                        that.happyMoney = data.ticket.ref_amount;
                        setTimeout(function(e){
                            that.beforeGladCard = !that.beforeGladCard;
                            that.closeGladCard(e);
                            $('.open-glad-card').removeClass('rotate');
                        },700);
                        $('body').off('touchmove');
                        allowClick2 = true;
                    });
                    xhr.fail(function(jqXHR) {
                        if (400 === jqXHR.status && jqXHR.responseText) {
                            toastCenter('系统繁忙，请稍后重试！');
                        }
                        allowClick2 = true;
                    });
                } else {
                    that.beforeGladCard = !that.beforeGladCard;
                    $('body').on('touchmove', function(e){that.eventTarget(e)}, false);
                }
            },
            closeGladCard: function(event) {
                var e = event || window.event;
                var that = this;
                var card = that.activeTicketCount;
                if (that.afterGladCard) {
                    card--;
                    that.activeTicketCount = card;
                    that.afterGladCard = !that.afterGladCard;
                    $('body').off('touchmove');
                } else {
                    that.afterGladCard = !that.afterGladCard;
                    $('body').on('touchmove',function(e){that.eventTarget(e)}, false);
                }
            },
            goToLogin: function () { //登录弹框
                poptpl.popComponent({
                    popBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/pop_bg_waiting.png) no-repeat',
                    popBorder: 0,
                    closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/pop_close.png",
                    btnMsg: "马上登录",
                    title: '<p style="font-size:0.72rem;margin: 2.8rem 0 2rem;color: #f03350;">您还没有登录哦！</p>',
                    popBtmBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/pop_btn.png) no-repeat',
                    popBtmBorderRadius: 0,
                    popBtmColor: '#f03350',
                    popBtmFontSize: ".45333333rem",
                    btnHref: '/site/login'
                });
            },
            eventTarget: function(event) {
                var e = event || window.event;
                e.preventDefault();
            }

        }
    });
    Vue.config.devtools = false;
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
</script>