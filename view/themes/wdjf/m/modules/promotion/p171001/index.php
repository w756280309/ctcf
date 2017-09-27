<?php
$this->title = '国庆中秋三重大礼回馈';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/popover.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/National-day/css/index.css?v=0.3">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/popover.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/common.js"></script>
<script src="<?= FE_BASE_URI ?>libs/iscroll.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>

<div class="flex-content national-day" id="app">
    <div class="module">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/National-day/images/national-bg-01.png" alt="">
    </div>
    <div class="module">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/National-day/images/national-bg-02.png" alt="">
        <p class="txt-tip">活动期间任意投资一笔，可获得抽奖机会(最多1次)。</p>
        <a href="javascript:;" class="a-my-prize" @click="checkMyPrize($event)" data-num="1">我的奖品>></a>
        <a href="javascript:;" class="click-prize" @click="drawing($event)" data-num="2">点击抽奖</a>
    </div>
    <div class="module">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/National-day/images/national-bg-03.png" alt="">
        <p class="txt-tip">活动期间年化投资每累积10万元(不含转让产品)，可获得50元超市卡一张，上不封顶！</p>
        <p class="sum">已累计年化:<span v-cloak><?= $totalMoney ?></span>万元</p>
        <a class="to-invest" @click="goToInvest($event)" data-num="3">去投资</a>
    </div>
    <div class="module">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/National-day/images/national-bg-04.png" alt="">
        <p class="txt-tip">活动期间投资指定项目(附带<i>积分2倍</i>标志)，所获得的积分翻倍！</p>
        <a href="javascript:;" class="link-details" @click="understandDetails($event)" data-num="4"><span>了解详情</span></a>
        <p class="interpretation">本活动最终解释权归温都金服所有</p>
    </div>

    <!--抽奖机会-->
    <div class="draw-box" :class="[ isNeedInvest ? 'show' : '' ]">
        <div class="draw-chance">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/National-day/images/draw-close.png" @click="closeDraw()" class="draw-close" alt="关闭按钮">
            <div class="draw-top" v-if="canDraw===1" v-cloak>您已经抽过奖了哦！去看看其他活动吧</div>
            <div class="draw-top" v-else="canDraw===7" v-cloak>您还没有完成投资任务哦</div>
            <a class="draw-bottom" v-if="canDraw===1" @click="closeDraw()" v-cloak>我知道了</a>
            <a class="draw-bottom" v-else="canDraw===7" v-cloak href="/deal/deal/index">去投资</a>
        </div>
    </div>

    <!--对应的奖品列表-->
    <div class="prizes-box" :class="[ isActive ? 'show' : '' ]">
        <div class="outer-box">
            <img class="pop_close" src="<?= FE_BASE_URI ?>wap/campaigns/National-day/images/pop_close.png" @click="closePrizeList()" alt="">

            <div class="prizes-pomp">
                <div id="wrapper">
                    <ul>
                        <li class="prizes-title">我的奖品</li>
                        <li class="clearfix">
                            <div class="lf"><img v-bind:src="ticket.path" alt="礼品"></div>
                            <div class="lf">
                                <p>{{ticket.name}}</p>
                                <p>中奖时间:{{ticket.awardTime}}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var myScroll = new iScroll('wrapper',{
        vScrollbar:false,
        hScrollbar:false
    });
    var promoStatus = $('input[name=promoStatus]').val();
    var isLoggedin = $('input[name=isLoggedin]').val();
    var app = new Vue({
        el: '#app',
        data: {
            obj: '',
            canDraw: '',
            promoStatus: promoStatus,
            isLoggedin: isLoggedin,
            ticket: {path:'',name:'',awardTime:''},
            tId: '',
            isActive: false,
            isNeedInvest: false, // 显示弹框（登录后，去投资按钮，非中奖情况）
            showStatusBall: true, // 显示活动状态的弹框
            showLoginBall: false // 显示登录的弹框
        },
        created: function(){
            this.isActive = false;
            this.showStatusBall = true;
        },
        methods: {
            computNum: function (n){
                var n = event.currentTarget.getAttribute('data-num');
                switch(n) {
                    case '1':
                        this.obj = 'prizeList';
                        break;
                    case '2':
                        this.obj = 'draw';
                        break;
                    case '3':
                        this.obj = 'invest';
                        break;
                    case '4':
                        this.obj = 'link';
                        break;
                }
            },
            checkMyPrize: function(prizeList) {
                this.showPromoStatus(prizeList);
            },
            drawing: function(draw) {
                this.showPromoStatus(draw);
            },
            goToInvest: function(invest) {
                this.showPromoStatus(invest);
            },
            understandDetails: function(link) {
                this.showPromoStatus(link);
            },
            showPromoStatus: function() {
                this.computNum();
                if (this.promoStatus == 0){ //进行中
                    this.showStatusBall = false;
                    this.showLoginPop(this.obj);
                    return false;
                } else if (this.promoStatus == 1) {
                    toastCenter('活动未开始');
                } else if (this.promoStatus == 2) {
                    if (this.obj === 'prizeList') {
                        this.showLoginPop(this.obj);
                        return false;
                    } else {
                        toastCenter('活动已结束');
                    }
                }
            },
            showLoginPop: function() {
                this.computNum();
                if (this.isLoggedin === 'true') { //已登录
                    if (!this.showStatusBall) { // 活动进行中
                        if (this.obj === 'prizeList') {
                            this.innerPrizeList('我的奖品');
                        } else if (this.obj === 'draw') {
                            this.onePrize('点击抽奖');
                        } else if (this.obj === 'invest') {//  ('去投资');
                            window.location.href = '/deal/deal/index';
                        } else if (this.obj === 'link') {//  ('了解详情');
                            window.location.href = '/deal/deal/index';
                        }
                    } else {
                        if (this.obj === 'prizeList') {
                            this.innerPrizeList('我的奖品');
                        }
                    }
                } else if (this.isLoggedin === 'false') {
                    if (!this.showStatusBall) { // 活动进行中
                        this.goToLogin('未登录,  去登录弹框');
                        return false;
                    } else if (this.showStatusBall){
                        toastCenter('活动已结束');
                    }
                }
            },
            onePrize: function () {  //抽奖 弹框
                var _this = this;
                window.clearTimeout(_this.tId);
                _this.tId = window.setTimeout(function () {
                    var xhr = $.get('/promotion/p171001/draw?key=promo_171001');
                    xhr.done(function(data) {
                        _this.canDraw = data.code;
                        if( _this.canDraw == 0) { // 领取奖品弹框
                            _this.ticket.path = '<?= FE_BASE_URI ?>'+data.ticket.path;
                            poptpl.popComponent({
                                popBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/National-day/images/pop_bg_02.png) no-repeat',
                                popBorder: 0,
                                closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/National-day/images/pop_close.png",
                                btnMsg: "收下礼品",
                                popTopColor: "#fff",
                                bgSize: "100% 100%",
                                title: '<p style="font-size:0.72rem;">恭喜您获得了<span style="display:block;font-size: 0.50666667rem">'+data.ticket.name+'</span></p>',
                                popBtmBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/National-day/images/pop_btn_01.png) no-repeat',
                                popMiddleHasDiv: true,
                                popBtmColor: '#e01021',
                                contentMsg: "<img style='margin: -0.5rem auto 0.5rem;display: block;width: 4.8rem;' src='<?= FE_BASE_URI ?>"+data.ticket.path+"' alt='图片地址'/>",
                                popBtmBorderRadius: 0,
                                popBtmFontSize: ".50666667rem"
                            }, 'close');
                        }
                    });

                    xhr.fail(function(jqXHR){
                        if (400 === jqXHR.status && jqXHR.responseText) {
                            var resp = $.parseJSON(jqXHR.responseText);
                            if ( 7 === resp.code ) {
                                _this.canDraw = 7;
                                _this.isNeedInvest = true; //('已抽过奖弹框');
                            } else if( 5 === resp.code || 4 === resp.code ) {
                                _this.canDraw = 1;
                                _this.isNeedInvest = true; //('未抽过奖弹框');
                            } else {
                                toastCenter('系统繁忙，请稍后重试！', function () {
                                    location.href = '/promotion/p171001/index?_mark=<?= time() ?>';
                                });
                            }
                        }
                    });
                },400);
            },
            innerPrizeList: function() { // 我的奖品 奖品列表弹框
                var _this = this;
                var xhr = $.get('/promotion/p171001/award-list?key=promo_171001');
                xhr.done(function(data) {
                    if(data.length == 0){ //无奖品
                        poptpl.popComponent({
                            popBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/National-day/images/pop_bg_01.png) no-repeat',
                            popBorder: 0,
                            closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/National-day/images/pop_close.png",
                            popBtmHas: false,
                            popTopColor: "#fff",
                            bgSize: "100% 100%",
                            title: '<p style="font-size:0.72rem;">我的奖品</p>',
                            popBtmBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/National-day/images/pop_btn_01.png) no-repeat',
                            popMiddleHasDiv: true,
                            contentMsg: "<p style='color:#fff;margin-top: 2rem;font-size:0.56rem;'>您还没有获得奖品哦！</p>",
                        }, 'close');
                    } else { //奖品列表
                        _this.ticket= data[0];
                        var awardTime = _this.ticket.awardTime;
                        var time = Date.parse(awardTime.replace(/-/g,"/"));
                        var year = new Date(time).getFullYear();
                        var month = new Date(time).getMonth()+ 1;
                        var day = new Date(time).getDate();
                        _this.ticket.awardTime = year+'年'+month+'月'+day+'日';
                        _this.isActive = true;
                        _this.ticket.path = '<?= FE_BASE_URI ?>'+_this.ticket.path;
                    }
                });
            },
            closeDraw: function() {
                this.isNeedInvest = false;
            },
            closePrizeList: function() {
                this.isActive = false;
            },
            goToLogin: function () {//登录
                poptpl.popComponent({
                    popBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/National-day/images/pop_bg_01.png) no-repeat',
                    popBorder: 0,
                    closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/National-day/images/pop_close.png",
                    btnMsg: "马上登录",
                    title: '<p style="font-size:0.72rem;margin: 2.8rem 0 2rem;color: #fff;">您还没有登录哦！</p>',
                    popBtmBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/National-day/images/pop_btn_01.png) no-repeat',
                    popBtmBorderRadius: 0,
                    popBtmColor: '#e01021',
                    popBtmFontSize: ".50666667rem",
                    btnHref: '/site/login?next=/promotion/p171001/index'
                });
            }
        }
    });
    Vue.config.devtools = false;
</script>