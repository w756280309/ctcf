<?php
$this->title = '周年庆前奏';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/gifts-list.css">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/css/preheat.min.css?v=2018052801">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/iscroll.js"></script>
<script src="<?= FE_BASE_URI ?>libs/handlebars-v4.0.10.js"></script>
<script src="<?= FE_BASE_URI ?>libs/phaser.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/js/gifts-list.js"></script>

<div class="flex-content" id="preheat" v-cloak>
    <div class="banner">
        <img @click.prevent src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/preheat_01.png" alt="">
        <img @click="showRegular(1)" class="regular-entry"
             src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/regular_btn.png" alt="按钮">
    </div>
    <div class="part-one">
        <img @click.prevent class="bg" src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/preheat_02.png"
             alt="">
        <div class="content">
            <div class="title"><span>预约有礼</span></div>
            <p>预约成功后，可在周年庆主会场期间（6.1-6.5）进行抽奖，最高<em>666</em>元现金红包！</p>
            <div id="appointment" class="btn" @click="appointment" data-click="true">{{isAppointmentMsg}}</div>
        </div>
    </div>
    <div class="part-two">
        <img @click.prevent class="bg" src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/preheat_03.png"
             alt="">
        <div class="content">
            <div class="title"><span>神券秒杀</span></div>
            <p>每日100张先到先得</p>
            <div id="second" class="btn" @click="second" data-click="true">{{isSecondMsg}}</div>
            <div class="record" @click="giftList('/ctcf/p180528/second-list')">秒杀记录>></div>
        </div>
    </div>
    <div class="part-three">
        <img @click.prevent class="bg" src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/preheat_04.png"
             alt="">
        <div class="content">
            <div class="title"><span>天降红包雨</span></div>
            <p>每日10：00、16：00 准时开启领完即止</p>
            <div id="open" class="btn animated" :class="{pulse:redPacketStatus.code==0}" @click="open">{{redPacketStatus.message}}</div>
            <div class="record" @click="giftList('/ctcf/p180528/red-packet-list')">我的红包>></div>
        </div>
    </div>
    <p class="tips">本活动最终解释权归楚天财富所有</p>
    <!--活动规则-->
    <div @touchmove.prevent v-cloak v-if="isShowRegular" class="mask" @click="showRegular(1)">
        <img class="title" src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/regular_title.png" alt="">
        <img class="close" src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/close.png" alt="">
        <div class="part">
            <span class="type"><i>预约有礼</i></span>
            <p>活动时间：2018.5.28-5.31；</p>
            <p>活动期间预约成功，可在周年庆主会场期间（6.1-6.5）进行抽奖，最高666元现金红包！</p>
        </div>
        <div class="part">
            <span class="type"><i>神券秒杀</i></span>
            <p>活动时间：2018.5.28-5.31；</p>
            <p>活动期间，每天限量发放100张大额神券，先到先得；</p>
            <p>秒杀到的神券将立即到账，仅限周年庆结束前使用。</p>
        </div>
        <div class="part">
            <span class="type special"><i>天降红包雨</i></span>
            <p>活动时间：2018.5.28-5.31；</p>
            <p>红包雨每日两场，分别在10点、16点开启，每轮红包限量，全部发放后本场次关闭；</p>
            <p>每人每轮最多可参与1次，每日最多可参与2轮；</p>
            <p>本游戏所得红包将立即到账。</p>
        </div>
    </div>
    <!--预约成功-->
    <div @touchmove.prevent v-cloak v-if="isAppointmentSuccess" class="mask">
        <div class="order">
            <img @click.prevent src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/order.png" alt="">
            <div @click="showRegular(2)" class="order-confirm"></div>
        </div>
    </div>
    <!--抢券成功-->
    <div @touchmove.prevent v-cloak v-if="isSecondSuccess" class="mask">
        <div class="coupon">
            <img @click.prevent src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/coupon.png" alt="">
            <div @click="showRegular(3)" class="coupon-confirm"></div>
        </div>
    </div>
    <div @touchmove.prevent id="gameStage"></div>
</div>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?= FE_BASE_URI ?>libs/wxShare.js"></script>
<script>
    var assetConfig = {
        process: "<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/processing.png",
        processBg: "<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/process_bg.png",
        one: "<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/1@2x.png",
        two: "<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/2@2x.png",
        three: "<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/3@2x.png",
        start: "<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/bg.png",
        award: "<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/award.png",
        noaward: "<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/no-award.png",
        boom: "<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/boom.png",
        S: "<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/S.png",
        A: "<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/A.png",
        B: "<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/B.png",
        C: "<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/C.png",
        D: "<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/D.png",
        redpacket: "<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/red.png",
        yellowpacket: "<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/yellow.png",
    }, vm, couponLevel;
    $(function () {
        FastClick.attach(document.body);
        vm = new Vue({
            el: "#preheat",
            data: {
                promoStatus: dataJson.promoStatus,
                isLogin: dataJson.isLoggedIn,
                isAppointment: dataJson.isAppointment,
                isSecond: dataJson.isSecond,
                redPacketStatus: {
                    code: dataJson.redPacket.code,
                    message: dataJson.redPacket.message
                },
                isShowRegular: false,
                isAppointmentMsg: '立即预约',
                isSecondMsg: '立即秒杀',
                isAppointmentSuccess: false,
                isSecondSuccess: false
            },
            created: function () {
                wxShare.setParams("神券秒杀、天降福利！快来预约楚天财富周年庆啦！", "点击链接，立即参与", "<?= Yii::$app->params['clientOption']['host']['wap'] ?>/ctcf/p180528/index", "https://admin.hbctcf.com/upload/link/link1527131364684008.jpg", "<?= Yii::$app->params['weixin']['appId'] ?>", "/ctcf/p180528/add-share");
                wxShare.TimelineSuccessCallBack = function () {
                    $.get("/ctcf/p180528/add-share?scene=timeline&shareUrl=" + encodeURIComponent(location.href))
                };

                if (this.isAppointment) {
                    this.isAppointmentMsg = '预约成功';
                }
                if (this.isSecond) {
                    this.isSecondMsg = '已秒完';
                }
                this.repeatClick().set(document.getElementById('appointment'), '1');
                this.repeatClick().set(document.getElementById('second'), '1');
                this.repeatClick().set(document.getElementById('open'), '1');

            },
            methods: {
                appointment: function (event) {
                    var status = this.baseVerify();
                    if (status && this.repeatClick().get(event.target) == '1') {
                        if (this.isAppointment) {
                            this.toastCenter('您已完成预约，记得周年庆期间来抽奖哦！')
                        } else {
                            vm.repeatClick().set(event.target, '0');
                            $.ajax({
                                type: "GET",
                                url: "/ctcf/p180528/appointment",
                                success: function (data) {
                                    if (data.code == 0) {
                                        vm.isAppointmentSuccess = !vm.isAppointmentSuccess;
                                        vm.isAppointmentMsg = data.message;
                                    } else {
                                        vm.toastCenter(data.message);
                                    }
                                    vm.repeatClick().set(event.target, '1');
                                },
                                error: function (error) {
                                    if (error.responseJSON.code == 23000) {
                                        vm.toastCenter("您已完成预约，记得周年庆期间来抽奖哦！");
                                    } else {
                                        vm.toastCenter(error.responseJSON.message);
                                    }
                                    vm.repeatClick().set(event.target, '1');
                                }
                            });
                        }
                    }
                },
                second: function (event) {
                    var status = this.baseVerify();
                    if (status && this.repeatClick().get(event.target) == '1') {
                        if (this.isSecond) {
                            this.toastCenter('今日神券已被领完，请明天再来哦！')
                        } else {
                            this.repeatClick().set(event.target, '0');
                            $.ajax({
                                type: "GET",
                                url: "/ctcf/p180528/second",
                                success: function (data) {
                                    if (data.code == 0) {
                                        vm.isSecondSuccess = !vm.isSecondSuccess;
                                        vm.isSecondMsg = data.message;
                                    } else {
                                        vm.toastCenter(data.message);

                                    }
                                    vm.repeatClick().set(event.target, '1');
                                },
                                error: function (error) {
                                    if (error.responseJSON.code == 23000) {
                                        vm.toastCenter("今日神券已被领完，请明天再来哦！");
                                    } else {
                                        vm.toastCenter(error.responseJSON.message);
                                    }
                                    vm.repeatClick().set(event.target, '1');
                                }
                            });
                        }
                    }
                },
                open: function (event) {
                    var status = this.baseVerify();
                    if (status && this.repeatClick().get(event.target) == '1') {
                        var vm = this;
                        this.repeatClick().set(event.target, '0');
                        $.ajax({
                            type: "GET",
                            url: "/ctcf/p180528/open",
                            success: function (data) {
                                if (data.code == 0) {
                                    //进行中 进入游戏
                                    startRainRedPacket();
                                    vm.redPacketStatus.message = data.message;
                                } else {
                                    if(data.code != 24 && data.code != 25){
                                        vm.redPacketStatus.message = data.message;
                                    }
                                    vm.toastCenter(data.message);
                                }
                                vm.repeatClick().set(event.target, '1');
                            },
                            error: function (error) {
                                vm.redPacketStatus.message = data.message;
                                vm.toastCenter(error.responseJSON.message);
                                vm.repeatClick().set(event.target, '1');
                            },
                        });
                    }
                },
                showRegular: function (value) {
                    switch (value) {
                        case 1:
                            this.isShowRegular = !this.isShowRegular;
                            break;
                        case 2:
                            this.isAppointmentSuccess = !this.isAppointmentSuccess;
                            break;
                        case 3:
                            this.isSecondSuccess = !this.isSecondSuccess;
                            break;
                    }
                },
                giftList: function (url) {
                    var status;
                    switch (vm.promoStatus) {
                        case 0:
                            if (!vm.isLogin) {
                                window.location.href = "/site/login";
                                return;
                            }
                            break;
                        case 1:
                            vm.toastCenter('活动未开始');
                            return;
                            break;
                    }
                    $.ajax({
                        type: "GET",
                        url: url,
                        data: {key: "promo_180528"},
                        success: function (data) {
                            if (data.length != 0) {
                                giftsList({
                                    isGifts: true,//有奖品，无奖品为false
                                    closeImg: '<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/close.png',
                                    list: data
                                });
                            } else if (data.length === 0) {
                                giftsList({
                                    isGifts: false,//无奖品为false
                                    closeImg: '<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/images/close.png',
                                    list: []
                                });
                            }
                        },
                        error: function (error) {
                            vm.toastCenter(error.responseJSON.message);
                        }
                    })
                },
                getCoupon: function () {
                    var vm = this;
                    $.ajax({
                        type: "GET",
                        url: "/ctcf/p180528/red-packet",
                        data: "",
                        success: function (data) {
                            if (data.code) {
                                switch (data.ticket) {
                                    case "180528_C15":
                                        couponLevel = "S";
                                        break;
                                    case "180528_C10":
                                        couponLevel = "A";
                                        break;
                                    case "180528_C8":
                                        couponLevel = "B";
                                        break;
                                    case "180528_C5":
                                        couponLevel = "C";
                                        break;
                                    case "180528_C3":
                                        couponLevel = "D";
                                        break;
                                }
                            } else {
                                vm.toastCenter(data.message);
                            }
                        },
                        error: function (error) {
                            vm.toastCenter(error.responseJSON.message);
                        },
                    });
                },
                baseVerify: function () {
                    switch (this.promoStatus) {
                        case 0:
                            if (this.isLogin) {
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
                repeatClick: function () {
                    return {
                        set: function (target, value) {
                            target.setAttribute('data-click', value)
                        },
                        get: function (target) {
                            return target.getAttribute('data-click')
                        }
                    }
                },
                scrollToTop:function(){
                    $('body').scrollTop(1000);
                }
            }
        });
    });
</script>
<script src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180528/js/index.js?v=2018052810"></script>