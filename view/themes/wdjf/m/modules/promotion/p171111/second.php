<?php
$this->title = '11月理财节';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/popover.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20171111/css/page-second.css?v=0.12">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/popover.js?v=2.0"></script>
<script src="<?= FE_BASE_URI ?>libs/iscroll.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<style>
    [v-cloak]{display: none}
</style>
<div class="flex-content" id="app">
    <div class="banner">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/banner-top.png" alt="banner">
    </div>
    <div class="banner banner-bottom">
        <div class="time" v-for="l in list"  v-cloak>预约倒计时：<span >{{l.time}}</span></div>
    </div>
    <!-- 一、预约理财 -->
    <div class="slide slide-one">
        <div class="order-before order" :class="[ orderPop == '0' ? 'show': '']">
            <p class="txt-tip">活动期间预约11月9-11日理财，可获得对应金额加息券1张。</p>
            <div class="select-box">
                <div class="money input-box">
                    <label for="">预约金额</label>
                    <input id="order-money" type="tel" placeholder="请输入金额" @click="inputListener('#order-money')" value="" onafterpaste="this.value=this.value.replace(/\D/g,'')">
                    <i class="unit">万元</i>
                </div>
                <div class="select-item input-box clearfix">
                    <label for="">预约标</label>
                    <span class="item-txt" @click="selectInput" v-cloak v-if="appointmentId == true">温盈恒<i class="bracket">(180天以上）</i></span>
                    <span class="item-txt" @click="selectInput"  v-cloak v-if="appointmentId == false">温盈金(180天及以下)</span>
                    <img class="down-icon" src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/down-icon.png" alt="下拉箭头">
                    <div class="down-pop" @click="selectBox" v-cloak v-if="appointmentId == false">温盈恒(180天以上)</div>
                    <div class="down-pop" @click="selectBox" v-cloak v-if="appointmentId == true">温盈金(180天及以下)</div>
                </div>
            </div>
            <p class="down-txt"><span v-cloak v-if="appointmentId == false">选择预约温盈恒系列标，能获得更高价值加息券哦！</span></p>
            <!-- 立即预约 -->
            <a class="btn-order now-order" @click="nowOrder"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/btn-now-order.png" alt="立即预约"></a>
            <p class="tel">电话预约：400-101-5151</p>
        </div>
        <div class="order-after order"  :class="[ orderPop == '1' ? 'show': '']">
            <h4 class="result-title">恭喜您预约成功，<br>获得加息券一张！</h4>
            <div class="coupon clearfix coupon-shadow">
                <div class="coupon-left lf">
                    <p class="lf-top"><span>+</span><i>{{rateCoupon.couponRate}}</i>%</p>
                    <p class="lf-bottom">{{rateCoupon.lowestInvestMoney}}万元起投</p>
                </div>
                <div class="coupon-right rg">
                    <p class="lf-top">加息券-<span>{{rateCoupon.couponLength}}</span>天</p>
                    <p class="lf-bottom" v-if="rateCoupon.appointmentObjectId == 0">仅限温盈恒<i class="bracket">(180天以上)</i>使用</p>
                    <p class="lf-bottom" v-else="rateCoupon.appointmentObjectId == 1">仅限温盈金<i class="bracket">(180天及以下)</i>使用</p>
                </div>
            </div>
            <p class="result-tip">加息券将在11月9日发放到您的账户, 仅限11月9-11日期间使用。</p>
            <!--重新预约-->
            <a class="btn-order re-order" @click="reOrder"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/btn-re-order.png" alt="重新预约"></a>
            <p class="tel">电话预约：400-101-5151</p>
        </div>

    </div>
    <div class="slide slide-two">
        <div class="seckill">
            <a class="seckill-record" v-if="secondKillRecord == '1'" v-cloak @click="prizeRecord">秒杀记录>></a>
            <div class="seckill-nav clearfix">
                <ul>
                    <li v-cloak v-for="(item,index) in timeNav" :class="[ activeNav == index ?'active':'']" @click="toggle(index)">
                        <span>{{item}}</span>点开抢
                    </li>
                </ul>
            </div>
            <div class="seckill-content clearfix">
                <ul>
                    <li v-cloak v-for="(item, index) in secondKillList" :class="[ activeNav == index ? 'active':'']" >
                        <img :src="item.path" :alt="item.activityNumber">
                        <span class="seckill-time-btn" v-cloak  @click="seckillAction" v-if="item.secondKillStatus == 1" :num="item.activityNumber">{{item.time}}</span>
                        <span class="seckill-btn" v-cloak @click="seckillAction" v-if="item.secondKillStatus == 0" :num="item.activityNumber" ></span>
                        <span class="seckill-btn-over" v-cloak v-if="item.secondKillStatus == 2" :num="item.activityNumber" ></span>
                    </li>
                </ul>
            </div>
            <p class="time-txt">每日10点、15点、20点准点开启秒杀，数量有限，先到先得！</p>
        </div>
    </div>
    <div class="slide slide-third">
        <div class="order-third">
            <p class="txt-tip">喜卡可以在11月9-11日期间兑换现金红包，最高1111元哦！</p>
            <div class="result-box-task lf ">
                <img class="finished" v-if="isAppointmented == 1" src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/icon_finished.png" alt="">
                <p class="top-invest">成功预约理财</p>
                <p class="center-txt"><span class="orderTask">{{isAppointmented}}</span>/1</p>
                <a href="#" class="link">去预约</a>
            </div>
            <div class="result-box-task rg">
                <img class="finished" v-if="isVested == 1" src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/icon_finished.png" alt="">
                <p class="top-invest">任意投资一笔</p>
                <p class="center-txt"><span class="investTask">{{isVested}}</span>/1</p>
                <a href="/deal/deal/index" class="link">去投资</a>
            </div>
        </div>
    </div>
    <p class="last-tips">本活动最终解释权归温都金服所有</p>
    <div class="prizes-box" :class="[ !!isActive ? 'show' : '' ]">
        <div class="outer-box">
            <img class="pop_close" @click="closePrizeList" src="<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/pop_close.png" alt="">
            <div class="prizes-pomp">
                <p class="prizes-title">奖品列表</p>
                <div id="wrapper">
                    <ul>
                        <li class="clearfix" v-for="item in ticket">
                            <div class="lf"><img :src="item.path" alt="礼品"></div>
                            <div class="lf">
                                <p>{{item.name}}</p>
                                <p>中奖时间:{{item.time}}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var promoStatus = $('input[name=promoStatus]').val();
    var isLoggedin = $('input[name=isLoggedin]').val();
    var myScroll;
    var app = new Vue({
        el: '#app',
        data: {
            ticket: [],
            prize: {},
            promoStatus: promoStatus,
            isLoggedin: isLoggedin,
            isSeckillActive: '',
            secondKillRecord: 0, //秒杀记录
            timeNav: [10,15,20],
            secondKillList: [],
            orderPop: '', //预约框  1：已预约  0：未预约
            isVested: '1', //喜卡－投资状态
            appointmentId: true,
            isAppointmented: '0',// 是否预约 1：已预约  0：未预约
            appointObject: { appointmentObjectId: 0, appointmentObjectName: '温盈恒180天以上'}, // 预约标的
            rateCoupon: [{}],
            appointmentTime: '',
            appointmentTimeLast: '1510156800', // 8号24点时间戳
            todayDater: '', // 日期
            appointmentTimeGap: 0, // 倒计时格式
            list: [{}],
            giftsTimeList: [1509933600,1509951600,1509969600],
            timeGapBtn: '', // 商品提交id
            activeNav: '1',
            isActive: false,
            showStatusBall: true, // 显示活动状态的弹框
            showLoginBall: false // 显示登录的弹框
        },
        created: function(){
            this.orderPop = this.isAppointmented;
            this.isActive = false;
            this.showStatusBall = true;
            this.isSeckillActive = true;
            var that = this;
            $(function () {
                that.init();
            });
        },
        methods: {
            init: function () {
                var _this = this;
                var xhr = $.get('/promotion/p171111/get-initialize');
                xhr.done(function(data) {
                    _this.appointmentTime = data.appointmentTime; // 时间戳 1
                    _this.orderPop = data.isAppointmented; //预约框状态 2
                    _this.isVested  = data.isVested;//投资状态 6
                    _this.secondKillRecord = data.secondKillRecord; //秒杀记录 3
                    _this.activeNav = data.activeNav; // 导航高亮 7
                    _this.isAppointmented = data.isAppointmented; //预约状态 5
                    _this.secondKillList = data.secondKillList; // 当天秒杀商品列表
                    var awardTime = _this.appointmentTime * 1000;
                    _this.todayDater = new Date(awardTime).getDate();
                    if (_this.todayDater == '6') {
                        _this.giftsTimeList = [1509933600, 1509951600, 1509969600];
                    } else if (_this.todayDater == '7') {
                        _this.giftsTimeList = [1510020000, 1510038000, 1510056000];
                    } else if (_this.todayDater == '8') {
                        _this.giftsTimeList = [1510106400, 1510124400, 1510142400];
                    }
                    _this.downTime();
                    if (_this.isAppointmented == '1' ) {
                        _this.rateCoupon = data.rateCoupon;
                        for (var i = 0; i < data.rateCoupon.length; i++ ) {
                            _this.rateCoupon[i] = data.rateCoupon[i];
                            _this.rateCoupon[i].couponRate = data.rateCoupon[i].couponRate;
                            _this.rateCoupon[i].lowestInvestMoney = data.rateCoupon[i].lowestInvestMoney;
                            _this.rateCoupon[i].couponLength = data.rateCoupon[i].couponLength;
                            _this.rateCoupon[i].appointmentObjectId = data.rateCoupon[i].appointmentObjectId;
                        }
                    }
                    if (_this.orderPop == '1') { //有加息券  _this.appointObject
                        _this.appointObject = _this.newArrayVue(data.appointObject); //投资项目 类型 预约标
                    }
                    for(var j = 0; j < data.secondKillList.length; j++) {
                        _this.secondKillList[j].secondKillStatus = data.secondKillList[j].secondKillStatus;
                        _this.secondKillList[j].time = '';
                        _this.secondKillList[j].activityNumber = data.secondKillList[j].activityNumber;
                        _this.secondKillList[j].path = '<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/gifts-'+data.secondKillList[j].activityNumber+'.png';
                    }
                });
            },
            downTime: function() { // 设置 倒计时
                var _this = this;
                var count = _this.appointmentTimeLast - _this.appointmentTime; //倒计时
                var giftsTime1 = _this.giftsTimeList[0] - _this.appointmentTime;
                var giftsTime2 = _this.giftsTimeList[1] - _this.appointmentTime;
                var giftsTime3 = _this.giftsTimeList[2] - _this.appointmentTime;
                var t = setInterval(function () {
                    var a = app.theTimeGap(count);
                    for (var i = 0; i < _this.list.length; i++) {
                        Vue.set(_this.list[i],'time',a);
                    }
                    if (count === 0 ) {
                        clearInterval(t);
                        Vue.set(_this.list[0],'time',"00:00:00");
                        return false;
                    }
                    count--;
                }, 1000);
                var t1 = setInterval(function () {
                    var a1 = app.theTimeGap(giftsTime1);
                    for (var i = 0; i < _this.secondKillList.length; i++) {
                        Vue.set(_this.secondKillList[0],'time',a1);
                    }
                    if (giftsTime1 < 0 ) {
                        clearInterval(t1);
                        if (_this.secondKillList[0].secondKillStatus == 2) {
                            return
                        } else {
                            _this.secondKillList[0].secondKillStatus = 0;
                        }
                    }
                    giftsTime1--;
                }, 1000);
                var t2 = setInterval(function () {
                    var a2 = app.theTimeGap(giftsTime2);
                    for (var i = 0; i < _this.secondKillList.length; i++) {
                        Vue.set(_this.secondKillList[1],'time',a2);
                    }
                    if (giftsTime2 < 0 ) {
                        clearInterval(t2);
                        if (_this.secondKillList[1].secondKillStatus == 2) {
                            return
                        } else {
                            _this.secondKillList[1].secondKillStatus = 0;
                        }
                    }
                    giftsTime2--;
                }, 1000);
                var t3 = setInterval(function () {
                    var a3 = app.theTimeGap(giftsTime3);
                    for (var i = 0; i < _this.secondKillList.length; i++) {
                        Vue.set(_this.secondKillList[2],'time',a3);
                    }
                    if (giftsTime3 < 0 ) {
                        clearInterval(t3);
                        if (_this.secondKillList[2].secondKillStatus == 2) {
                            return
                        } else {
                            _this.secondKillList[2].secondKillStatus = 0;
                        }
                    }
                    giftsTime3--;
                }, 1000);
            },
            theTimeGap: function(s){ // 倒计时格式
                var hour = Math.floor(s/3600);
                var minute = Math.floor( (s- hour*3600) /60 );
                var second = s - (hour*3600)- (minute*60);
                if (hour < 10) {
                    hour = "0"+hour;
                }
                if (minute < 10) {
                    minute = "0"+minute;
                }
                if (second < 10) {
                    second = "0"+second;
                }
                return  hour +" : " + minute + " : " + second;
            },
            computNum: function (event){
                var e = event || window.event;
                var num = e.currentTarget.getAttribute('num');
                var _this = this;
                switch(num) {
                    case '2017110610':
                        _this.timeGapBtn = '1509933600';
                        break;
                    case '2017110615':
                        _this.timeGapBtn = '1509951600';
                        break;
                    case '2017110620':
                        _this.timeGapBtn = '1509969600';
                        break;
                    case '2017110710':
                        _this.timeGapBtn = '1510020000';
                        break;
                    case '2017110715':
                        _this.timeGapBtn = '1510038000';
                        break;
                    case '2017110720':
                        _this.timeGapBtn = '1510056000';
                        break;
                    case '2017110810':
                        _this.timeGapBtn = '1510106400';
                        break;
                    case '2017110815':
                        _this.timeGapBtn = '1510124400';
                        break;
                    case '2017110820':
                        _this.timeGapBtn = '1510142400';
                        break;
                }
            },
            eventTarget:function (event) {
                var e = event || window.event;
                e.preventDefault();
            },
            inputListener: function(obj) {
                document.querySelector(obj).addEventListener('input', function () {
                    var _this = this;
                    _this.onkeyup = function() {
                        _this.value = _this.value.replace(/\D/g,'');
                        var FirstChar= _this.value.substr(0,1);
                        if (FirstChar == 0) {
                            _this.value = ''+_this.value.substr(1);
                        }
                        if (_this.value.length > 4) {
                            _this.value = _this.value.substr(0,4);
                            return false;
                        }
                    }
                });
            },
            secondsFormat: function (s) {
                var hour = Math.floor(s/3600);
                var minute = Math.floor( (s- hour*3600) /60 );
                var second = s - (hour*3600)- (minute*60);
                return this.appointmentTimeGap = hour + ":" + minute + ":" + second;
            },
            seckillAction: function (event) { //秒杀
                var e = event || window.event;
                this.computNum(e);
                this.showPromoStatus(e);
            },
            showLoginPop: function(event) {
                var e = event || window.event;
                if (this.isLoggedin === 'true') { //已登录
                    this.seckillResult(e);
                } else if (this.isLoggedin === 'false') {
                    this.goToLogin('未登录,  去登录弹框');
                }
            },
            showPromoStatus: function(event) { //活动状态
                var e = event || window.event;
                if (this.promoStatus == 0){ //进行中
                    this.showLoginPop(e);
                    return false;
                } else if (this.promoStatus == 1) {
                    toastCenter('活动未开始');
                } else if (this.promoStatus == 2) {
                    toastCenter('活动已结束');
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
            noStarting: function () { //未开始秒杀 弹框
                poptpl.popComponent({
                    popBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/pop_bg_waiting.png) no-repeat',
                    popBorder: 0,
                    closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/pop_close.png",
                    btnMsg: "我知道了",
                    title: '<p style="font-size:0.72rem;margin: 2.8rem 0 2rem;color: #f03350;">秒杀还未开始哦！</p>',
                    popBtmBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/pop_btn.png) no-repeat',
                    popBtmBorderRadius: 0,
                    popBtmColor: '#f03350',
                    popBtmFontSize: ".45333333rem"
                },'close');
            },
            prizeRecord: function(event) { // 秒杀记录
                var _this = this;
                var e = event || window.event;
                $.ajax({
                    url: '/promotion/p171111/second-kill-record',
                    dataType: 'json',
                    type: 'get',
                    success: function (data) {
                        if (data.code == 0) {
                            _this.isActive =  !_this.isActive;
                            for (var i=0; i< data.ticket.length; i++) {
                                _this.ticket[i] = data.ticket[i];
                                _this.ticket[i].activeNumber = data.ticket[i].activityNumber;
                                _this.ticket[i].path = '<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/prizes-'+_this.ticket[i].activeNumber+'.png';
                                _this.ticket[i].name = data.ticket[i].name;
                                var awardTime = data.ticket[i].createTime;
                                var myDate = new Date(awardTime*1000);
                                var year = myDate.getFullYear();
                                var month = myDate.getMonth() + 1;
                                var day = myDate.getDate();
                                _this.ticket[i].time = year+'年'+month+'月'+day+'日';
                            }
                            $('body').on('touchmove',function(e) {_this.eventTarget(e);}, false);
                            setTimeout(function(){ _this.loaded(); },100);
                        } else {
                            alert(data.message);
                        }
                    }
                });
            },
            nowOrder: function() { //预约
                var _this = this;
                var reg = /^\d{0,4}$/;
                var money = $('input[type="tel"]').val();
                $('input[type="tel"]').css('border','none');
                if (money == '' || !reg.test(money)) {
                    $('input[type="tel"]').css('border','1px solid rgb(255,0,0)');
                    return false;
                }
                if (_this.isLoggedin === 'false') {
                    _this.goToLogin('未登录,  去登录弹框');
                    return false;
                }
                $.ajax({
                    url: '/promotion/p171111/appointment',
                    type: 'get',
                    data: {
                        appointmentAward: money,
                        appointmentObjectId: _this.appointObject.appointmentObjectId
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.code == 0) {
                            _this.rateCoupon = data.rateCoupon;
                            for (var i = 0; i < data.rateCoupon.length; i++ ) {
                                _this.rateCoupon[i] = data.rateCoupon[i];
                                _this.rateCoupon[i].couponRate = data.rateCoupon[i].couponRate;
                                _this.rateCoupon[i].lowestInvestMoney = data.rateCoupon[i].lowestInvestMoney;
                                _this.rateCoupon[i].couponLength = data.rateCoupon[i].couponLength;
                                _this.rateCoupon[i].appointmentObjectId = data.rateCoupon[i].appointmentObjectId;
                            }
                            _this.isAppointmented = 1; // 已预约 按钮
                            _this.orderPop = 1; // 已预约
                        } else { // 失败
                            alert(data.message)
                        }
                    },
                    error: function(data) {
                        alert('error:'+data.message)
                    }
                });
            },
            seckillResult: function (event) { // 秒杀接口
                var _this = this;
                var e = event || window.event;
                _this.timeGapBtn = e.currentTarget.getAttribute('num');
                var number = _this.timeGapBtn;
                $.ajax({
                    url: '/promotion/p171111/second-kill',
                    dataType: 'json',
                    type: 'get',
                    data: { activeNumber: number},
                    success: function (data) {
                        if (data.code == 0) { // 领取奖品弹框
                            _this.prize = data.prize;
                            _this.prize.activityNumber = data.prize.activityNumber;
                            _this.prize.path = '<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/prizes-'+data.prize.activityNumber+'.png';
                            poptpl.popComponent({
                                popBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/pop_bg_success.png) no-repeat',
                                popBorder: 0,
                                closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/pop_close.png",
                                btnMsg: "收下礼品",
                                popTopColor: "#f03350",
                                bgSize: "100% 100%",
                                title: '<p style="font-size:0.72rem;">恭喜您秒杀成功!</p>',
                                popBtmBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/pop_btn.png) no-repeat',
                                popMiddleHasDiv: true,
                                contentMsg: "<img style='margin: -0.5rem auto 0.5rem;display: block;width: 4.8rem;' src='" + data.prize.path + "' alt='图片地址'/>",
                                popBtmBorderRadius: 0,
                                popBtmFontSize: ".45333333rem",
                                popBtmColor: '#f03350'
                            }, 'close');
                            _this.init();
//                            _this.secondKillRecord = 1; //秒杀记录 3
                        } else if (data.code == 1) {
                            _this.noStarting();
                        }  else if (data.code == 8) { // 积分不够
                            poptpl.popComponent({
                                popBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/pop_bg_fair.png) no-repeat',
                                popBorder: 0,
                                closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/pop_close.png",
                                btnMsg: "去投资",
                                popTopColor: "#f03350",
                                bgSize: "100% 100%",
                                title: '<p style="font-size:0.72rem;">您的积分不足，<span style="display:block;font-size: 0.72rem;">您快去投资赚积分吧！</span></p>',
                                popBtmBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/pop_btn.png) no-repeat',
                                popMiddleHasDiv: true,
                                contentMsg: "<div style='margin: 0 auto 0.5rem;display: block;width: 4.8rem;height: 3rem;'></div>",
                                popBtmBorderRadius: 0,
                                popBtmFontSize: ".45333333rem",
                                popBtmColor: '#f03350',
                                btnHref: "/deal/deal/index"
                            }, 'close');
                        } else if (data.code == 2 || data.code == 3) { //遗憾
                            _this.waiting();
                            setTimeout(function() {
                                poptpl.popComponent({
                                    popBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/pop_bg_fair.png) no-repeat',
                                    popBorder: 0,
                                    closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/pop_close.png",
                                    btnMsg: "知道了",
                                    popTopColor: "#f03350",
                                    bgSize: "100% 100%",
                                    title: '<p style="font-size:0.72rem;">很遗憾，<span style="display:block;font-size: 0.72rem;">商品已被抢完了哦！</span></p>',
                                    popBtmBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/pop_btn.png) no-repeat',
                                    popMiddleHasDiv: true,
                                    contentMsg: "<div style='margin: 0 auto 0.5rem;display: block;width: 4.8rem;height: 3rem;'></div>",
                                    popBtmBorderRadius: 0,
                                    popBtmFontSize: ".45333333rem",
                                    popBtmColor: '#f03350'
                                }, 'close');
                                if($('#waiting').parents($('.pop').eq(0)).css('display') == 'block'){
                                    $('div.pop').eq(0).prev($('.mask')).remove();
                                    $('div.pop').eq(0).remove();
                                }
                                _this.init();
                            },2000);
                        } else if (data.code == 5) { // 不能再次秒杀了
                            poptpl.popComponent({
                                popBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/pop_bg_fair.png) no-repeat',
                                popBorder: 0,
                                closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/pop_close.png",
                                btnMsg: "知道了",
                                popTopColor: "#f03350",
                                bgSize: "100% 100%",
                                title: '<p style="font-size:0.72rem;">每件商品<span style="display:block;font-size: 0.72rem;">最多只能获得1件哦！</span></p>',
                                popBtmBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/pop_btn.png) no-repeat',
                                popMiddleHasDiv: true,
                                contentMsg: "<div style='margin: 0 auto 0.5rem;display: block;width: 4.8rem;height: 3rem;'></div>",
                                popBtmBorderRadius: 0,
                                popBtmFontSize: ".45333333rem",
                                popBtmColor: '#f03350'
                            }, 'close');
                        } else if (data.code == 6) {
                            _this.goToLogin('未登录');
                        } else if (data.code == 7) { }// 奖品编号错误
                    }
                });
            },
            toggle: function (index) {
                $(".seckill-content ul li").hide().eq(index).show();
                $(".seckill-nav ul li").removeClass('active').eq(index).addClass('active');
            },
            closePrizeList: function() {
                $('body').off('touchmove');
                this.isActive = !this.isActive;
            },
            selectInput: function() {
                $('.down-pop').show();
            },
            selectBox: function() {
                this.appointmentId = !this.appointmentId;
                if(this.appointmentId == true) {
                    this.appointObject.appointmentObjectId = 0; // 预约标180天以上
                } else if(this.appointmentId == false) {
                    this.appointObject.appointmentObjectId = 1; // 预约标180天及以上
                }
                $('.down-pop').hide();
            },
            waiting: function() {
                poptpl.popComponent({
                    popBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/page-second/pop_bg_waiting.png) no-repeat',
                    popBorder: 0,
                    closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20171111/images/pop_close.png",
                    btnMsg: "",
                    popTopColor: "#f03350",
                    bgSize: "100% 100%",
                    title: '<p style="height: 1.4rem;"></p>',
                    popBtmBackground: 'none',
                    popMiddleHasDiv: true,
                    contentMsg: "<div id='waiting' style='margin: 0 auto 0.5rem;display: block;width: auto;line-height: 1.62666667rem; color: #f03350;font-size: 1.06666667rem'>秒杀排队中<br>请稍候...</div>",
                    popBtmBorderRadius: 0,
                    popBtmFontSize: ".45333333rem",
                    popBtmColor: '#f03350'
                }, 'close');
            },
            reOrder: function() {
                this.appointObject =  { appointmentObjectId: 0, appointmentObjectName: '温盈恒180天以上'};
                this.orderPop = 0;
                this.appointmentId = true
            },
            newArrayVue: function(str) {
                var arrayList = new Array();
                arrayList = str;
                return arrayList;
            },
            loaded: function() {
                myScroll = new iScroll('wrapper',{
                    vScrollbar:false,
                    hScrollbar:false
                });
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
    $(function(){
        $('#wrapper').on('click',function(event){
            var e = event || window.event;
            e.stopPropagation();
        });
    });

</script>
