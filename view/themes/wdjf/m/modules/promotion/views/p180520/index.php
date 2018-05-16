<?php

$this->title = '温都金服2周年';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/gifts-list.css?v=20180509">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180424/css/index.min.css?v=1.7">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/iscroll.js"></script>
<script src="<?= FE_BASE_URI ?>libs/handlebars-v4.0.10.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/gifts-list.js"></script>
<script src="<?= FE_BASE_URI ?>libs/phaser.min.js"></script>

<div class="flex-content" id="active" v-cloak>
    <div class="banner">
        <img @click.prevent src="<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/banner.png" alt="">
    </div>
    <div class="packet-rain">
        <div @click="closeRegular" class="regular">活动规则</div>
        <div class="gift-list" @click="giftList">奖品列表</div>
        <img @click.prevent src="<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/packet-rain.png" alt="">
        <div class="content">
            <p>天降红包雨</p>
            <p>每日10:00/16:00准时开启，每轮 红包数量有限，领完为止！</p>
            <p class="animated" :class="{gray:!packetRain.isStartPacketRain,pulse:packetRain.isStartPacketRain}" @click="packetRainStart">{{packetRain.content}}</p>
        </div>
    </div>
    <div class="points">
        <div class="inner">
            <p class="title">520当天，全场5倍积分！</p>
            <table>
                <tr>
                    <td><span>年化投资(元)</span></td>
                    <td><span>原积分</span></td>
                    <td>现得积分</td>
                </tr>
                <tr>
                    <td>50000</td>
                    <td>300</td>
                    <td>1500</td>
                </tr>
                <tr>
                    <td>20000</td>
                    <td>1200</td>
                    <td>6000</td>
                </tr>
                <tr>
                    <td>50000</td>
                    <td>3000</td>
                    <td>15000</td>
                </tr>
                <tr>
                    <td>100000</td>
                    <td>6000</td>
                    <td>30000</td>
                </tr>
            </table>
            <div v-cloak class="invest" :class="{activeStatus:!activeBtn.activeStatus}">
                <a @click="goInvest" :href="activeBtn.btnContent">{{activeBtn.content}}</a>
            </div>
        </div>
    </div>
    <div class="charity points">
        <div class="inner">
            <img @click.prevent src="<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/charity.png" alt="">
            <p class="des" v-cloak>我有{{medal.medalCount}}枚慈善勋章</p>
            <div v-cloak @click="openPacket" class="btn" :class="{openPacketStatus:!charityBtn.activeStatus}">{{charityBtn.content}}</div>
        </div>
    </div>
    <div v-show="isShowPop" v-cloak class="mask">
        <div class="pop">
            <img @click.prevent src="<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/open-packet.png" alt="">
            <p class="content" v-html="medal.award"></p>
            <div @click="closePop" class="confirm">收下奖品</div>
        </div>
    </div>
    <div v-show="isShowRegular" v-cloak class="regular-pop">
        <div class="part-top">
            <img @click="closeRegular" src="<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/close.png" alt="">
            <div class="regular-one">
                <div class="title">天降红包雨</div>
                <div class="des">
                    <p>活动时间：2018.5.17-5.20；</p>
                    <p>红包雨每日两场，分别在10点、16点开启，每轮红包限量，全部发放后本场次关闭；</p>
                    <p>每人每轮最多可参与1次，每日最多可参与2轮；</p>
                    <p>本游戏所得红包将立即到账。</p>
                </div>
            </div>
            <div class="regular-two mgt55">
                <div class="title">全场5倍积分</div>
                <div class="des">
                    <p>活动时间：2018.5.20；</p>
                    <p>活动期间全场理财5倍积分！</p>
                </div>
            </div>
            <div class="regular-three mgt55">
                <div class="title">慈善勋章抽奖</div>
                <div class="des">
                    <p>活动时间：2018.5.20；</p>
                    <p>通过慈善任务获得的勋章，可以在活动当天进行抽奖，1枚勋章=1次抽奖机会，可获得随机现金红包或积分奖励；</p>
                    <p>抽奖所得奖励将立即到账（必须完成开户）。</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="gameStage" @click.prevent></div>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?= FE_BASE_URI ?>libs/wxShare.js?v=3.0"></script>
<script>
    var assetConfig = {
        process:"<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/processing.png",
        processBg:"<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/process_bg.png",
        one:"<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/1@2x.png",
        two:"<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/2@2x.png",
        three:"<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/3@2x.png",
        start:"<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/bg.png",
        award:"<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/award.png",
        noaward:"<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/no-award.png",
        boom:"<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/boom.png",
        S:"<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/S.png",
        A:"<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/A.png",
        B:"<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/B.png",
        C:"<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/C.png",
        D:"<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/D.png",
        E:"<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/E.png",
        redpacket:"<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/red.png",
        yellowpacket:"<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/yellow.png",
    }, vm, couponLevel;
    $(function () {
        FastClick.attach(document.body);
        vm = new Vue({
            el: "#active",
            data: {
                promoRedStatus: dataJson.promoRedStatus,
                promoCharityStatus:dataJson.promoCharityStatus,
                isLogin: dataJson.isLoggedIn,
                startTime:dataJson.startTime,
                endTime:dataJson.endTime,
                isShowPop:false,
                isShowRegular:false,
                isDrawDuration:dataJson.isDrawDuration,
                packetRain:{
                    now:dataJson.now,
                    content:"本轮红包雨已结束",
                    isStartPacketRain:false
                },
                medal:{
                    award:"",
                    medalCount:dataJson.medalCount
                },
                activeBtn:{
                    activeStatus:false,
                    btnContent:"javascript:void(0);",
                    content:"5月20日开启"
                },
                charityBtn:{
                    activeStatus:false,
                    content:"5月20日开启"
                }
            },
            created: function () {
                wxShare.setParams("温都金服2周年啦！百万福利从天而降，还有5倍积分哦！", "点击链接，立即参与", "<?= Yii::$app->params['clientOption']['host']['wap'] ?>/promotion/p180520/index", "https://static.wenjf.com/upload/link/link1526280214187363.png", "<?= Yii::$app->params['weixin']['appId'] ?>", "/promotion/p180520/add-share");
                wxShare.TimelineSuccessCallBack = function () {
                    $.get("/promotion/p180520/add-share?scene=timeline&shareUrl=" + encodeURIComponent(location.href))
                };

                if(this.promoCharityStatus == 0){
                    this.activeBtn.btnContent = "/deal/deal/index";
                    this.activeBtn.activeStatus = true;
                    this.activeBtn.content = "立即投资";

                    this.charityBtn.activeStatus = true;
                    this.charityBtn.content = "立即开奖";
                }
                this.packetRainStatus();
            },
            methods: {
                packetRainStart:function(){
                    if (this.packetRain.isStartPacketRain) {
                        if(this.isLogin ){
                            if(!this.isDrawDuration){
                                startRainRedPacket();
                            } else {
                                this.toastCenter("本轮已抽奖");
                            }
                        } else {
                            window.location.href = "/site/login";
                        }
                    }
                },
                goInvest:function(){
                    if(!this.activeBtn.activeStatus){
                        var status = this.startActive();
                    }
                },
                packetRainStatus:function(){
                    var date = new Date(this.packetRain.now),
                        days = date.getDate(),
                        hours = date.getHours(),
                        vm = this,
                        timer = function(time){
                            if(hours>=time && hours<10){
                                vm.packetRain.content = "10点场即将开始";
                            } else if(hours>=10 && hours<12) {
                                vm.packetRain.content = "红包雨进行中";
                                vm.packetRain.isStartPacketRain = true;
                            } else if(hours>=14 && hours<16) {
                                vm.packetRain.content = "16点场即将开始";
                            } else if(hours>=16 && hours<18){
                                vm.packetRain.content = "红包雨进行中";
                                vm.packetRain.isStartPacketRain = true;
                            }
                        };

                    var startDate = new Date(this.startTime),
                        startDays = startDate.getDate(),
                        endDate = new Date(this.endTime),
                        endDays = endDate.getDate();
                    if(days==startDays && !this.isDrawDuration){
                        timer(0);
                    } else if(days>startDays && days<endDays+1 &&!this.isDrawDuration){
                        timer(8);
                    }
                },
                getCoupon:function(){
                    var vm = this;
                    $.ajax({
                        type:"GET",
                        url:"/promotion/p180520/get-packet-draw",
                        data:"",
                        success:function(data){
                            if(data.code == 0 ){
                                switch(data.sn){
                                    case "180516_C50":
                                        couponLevel = "S";
                                        break;
                                    case "180516_C20":
                                        couponLevel = "A";
                                        break;
                                    case "180516_C15":
                                        couponLevel = "B";
                                        break;
                                    case "180516_C10":
                                        couponLevel = "C";
                                        break;
                                    case "180516_C8":
                                        couponLevel = "D";
                                        break;
                                    case "180516_C5":
                                        couponLevel = "E";
                                        break;
                                    default :
                                        break;
                                }
                                vm.isDrawDuration = true;
                                vm.packetRain.content = "本轮红包雨已结束";
                                vm.packetRain.isStartPacketRain = false;
                            } else {
                                vm.toastCenter(data.message);
                            }
                        },
                        error:function(error){vm.toastCenter(error.responseJSON.message);},
                    });
                },
                openPacket:function(){
                    var vm =this;
                    var status = this.startActive();
                    if(status){
                        $.ajax({
                            type:"GET",
                            url:"/promotion/p180520/get-charity-draw",
                            data:"",
                            success:function(data){
                                if(data.code == 0 ){
                                    vm.isShowPop = true;
                                    if(data.refType == 'POINT'){
                                        vm.medal.award = '<span>'+data.sn.replace("180520_P",'')+'</span>'+ '积分';
                                    } else if(data.refType == 'RED_PACKET'){
                                        vm.medal.award = '<span>'+data.sn.replace("180520_RP",'')+'</span>' + '元现金红包';
                                    }
                                    vm.medal.medalCount = data.medalCount;
                                } else {
                                    vm.toastCenter(data.message);
                                }
                            },
                            error:function(error){vm.toastCenter(error.responseJSON.message);},
                        });
                    }
                },
                closePop:function () {
                    this.isShowPop = !this.isShowPop;
                },
                closeRegular:function () {
                    this.isShowRegular = !this.isShowRegular;
                },
                giftList: function () {
                    var vm = this;
                    var status;
                    switch (vm.promoRedStatus) {
                        case 0:
                            if (vm.isLogin) {
                                status = true;
                            } else {
                                window.location.href = "/site/login";
                            }
                            break;
                        case 1:
                            vm.toastCenter('活动未开始');
                            break;
                        case 2:
                            status = true;
                            break;
                        default:
                            break;
                    }
                    if(status){
                        $.ajax({
                            type: "GET",
                            url: "/promotion/p180520/award-list",
                            data: {key:"promo_180516"},
                            success: function (data) {
                                if (data.length != 0) {
                                    var arr = [],obj={};
                                    $.each(data,function(index,item){
                                        obj.name = item.name;
                                        obj.awardTime = item.awardTime;
                                        obj.path = '<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/'+item.path;
                                        arr[index]=obj;
                                        obj = {};
                                    });
                                    giftsList({
                                        isGifts: true,//有奖品，无奖品为false
                                        closeImg: '<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/close.png',
                                        list: arr
                                    });
                                } else if (data.length === 0) {
                                    giftsList({
                                        isGifts: false,//无奖品为false
                                        closeImg: '<?= FE_BASE_URI ?>wap/campaigns/active20180424/images/close.png',
                                        list: []
                                    });
                                } else {
                                    _this.toastCenter(data.message);
                                }
                            },
                            error: function (error) {
                                _this.toastCenter(error)
                            }
                        })
                    }
                },
                baseVerify: function (value) {
                    var _this = this;
                    switch (_this.promoRedStatus) {
                        case 0:
                            if (_this.isLogin) {
                                return true;
                            } else {
                                window.location.href = "/site/login";
                                return false;
                            }
                            break;
                        case 1:
                            if(value){return false;}
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
                startActive: function () {
                    var _this = this;
                    switch (_this.promoCharityStatus) {
                        case 0:
                            if (_this.isLogin) {
                                return true;
                            } else {
                                window.location.href = "/site/login";
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
                refresh:function(){
                    var remainTime;
                    var date = new Date(this.packetRain.now),
                        days = date.getDate(),
                        hours = date.getHours(),
                        minutes = date.getMinutes(),
                        seconds = date.getSeconds();
                    if(days>=16 && days<21){
                        if(hours<10){
                            remainTime = (9-hours)*3600 + (59-minutes)*60 + (59-seconds);
                        } else if(hours<16){
                            remainTime = (15-hours)*3600 + (59-minutes)*60 + (59-seconds);
                        }
                        var timer = setInterval(function(){
                            if(--remainTime == 0){
                                location.reload();
                                clearInterval(timer);
                            }
                        },1000);
                    }
                }
            }
        });

        vm.refresh();
    });
</script>
<script src="<?= FE_BASE_URI ?>wap/campaigns/active20180424/js/index.js?v=20180521"></script>