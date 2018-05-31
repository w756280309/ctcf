<?php
$this->title = '楚天财富3周年';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180601/css/index.min.css?v=1.1">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>

<div class="flex-content" id="active">
    <div class="banner">
        <img @click.prevent src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180601/images/banner.png" alt="">
        <div @click="isShowPop(2)" class="regular">活动规则</div>
    </div>
    <div class="part-one">
        <img @click.prevent class="bg" src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180601/images/part_01.png"
             alt="">
        <div class="content">
            <ul v-cloak>
                <li>
                    <img :src="boxCount>0?openUrl:closeUrl" alt="">
                    <p>1万元<br>(累计年化投资)<br>返现<i>8</i>元</p>
                </li>
                <li>
                    <img :src="boxCount>1?openUrl:closeUrl" alt="">
                    <p>5万元<br>(累计年化投资)<br>返现<i>48</i>元</p>
                </li>
                <li>
                    <img :src="boxCount>2?openUrl:closeUrl" alt="">
                    <p>20万元<br>(累计年化投资)<br>返现<i>168</i>元</p>
                </li>
                <li>
                    <div class="special">1280<br><i>元</i></div>
                </li>
                <li>
                    <img :src="boxCount>3?openUrl:closeUrl" alt="">
                    <p>50万元<br>(累计年化投资)<br>返现<i>358</i>元</p>
                </li>
                <li>
                    <img :src="boxCount>4?openUrl:closeUrl" alt="">
                    <p>100万元<br>(累计年化投资)<br>返现<i>698</i>元</p>
                </li>
            </ul>
            <p class="tips">注：年化投资额=投资金额*项目期限/365</p>
            <a @click="btnEvent(1)" class="go-invest" href="javascript:void(0);">立即投资</a>
            <p v-cloak class="total">累计年化投资：<i>{{annualInvest}}</i>万元</p>
        </div>
    </div>
    <div v-cloak class="part-two">
        <img @click.prevent src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180601/images/part_02.png?v=1.0" alt="">
        <a v-cloak @click="btnEvent(2)" class="go-invest draw" href="javascript:void(0);">{{drawMsg}}</a>
        <div class="tip specialTips">理财非存款 产品有风险 投资须谨慎</div>
        <div class="tip">本活动最终解释权归楚天财富所有</div>
    </div>

    <!--中奖弹框-->
    <div @touchmove.prevent v-if="isShowAwardPop" v-cloak class="mask">
        <div class="draw-award">
            <img @click="isShowPop(1)" class="close"
                 src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180601/images/btn_close.png" alt="">
            <img @click.prevent class="award" src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180601/images/award.png"
                 alt="">
            <p class="show-award" v-html="awardMsg"></p>
            <div @click="isShowPop(1)" class="confirm">收下奖励</div>
        </div>
    </div>

    <!--活动规则-->
    <div @touchmove.prevent v-if="isShowRegularPop" v-cloak class="mask">
        <div class="regular-pop">
            <img @click="isShowPop(2)" class="close"
                 src="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180601/images/btn_close.png" alt="">
            <p class="regular-title"><span>活动规则</span></p>
            <ul>
                <li class="special">投资开宝箱</li>
                <li>1、活动时间：2018.6.1-6.5；</li>
                <li>2、活动期间累计年化投资（不含转让产品）达到指定额度，即可开启相应宝箱，获得相应返现；</li>
                <li>3、年化投资额可累计，如：累计年化投资额达到100万，即可开启全部宝箱，共获得返现<i class="strongFont">1280</i>元；</li>
                <li>4、返现奖励将立即发放到余额。</li>
                <li class="special">抽奖赢现金</li>
                <li>1、活动时间：2018.6.1-6.5；</li>
                <li>2、参与本活动抽奖，需在预热活动中完成预约，且只能抽取一次，最高可抽取<i class="strongFont">666</i>元现金红包哦！</li>
                <li>3、本活动奖励为现金红包/积分，奖励将立即发放到您的账号（需先完成开户）。</li>
            </ul>
        </div>
    </div>
</div>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?= FE_BASE_URI ?>libs/wxShare.js"></script>
<script>
    $(function () {
        FastClick.attach(document.body);
        var vm = new Vue({
            el: "#active",
            data: {
                promoStatus: dataJson.promoStatus,
                isLogin: dataJson.isLoggedIn,
                isHaveDraw: dataJson.isHaveDraw,
                bonus: dataJson.bonus,
                bonusType: dataJson.bonusType,
                annualInvest: parseFloat(dataJson.annualInvest / 10000).toFixed(2,10),
                boxCount: dataJson.boxCount,
                closeUrl: '<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180601/images/close.png',
                openUrl: '<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180601/images/open.png',
                drawMsg: "立即抽奖",
                awardMsg: "",
                isShowAwardPop: false,
                isShowRegularPop: false,
            },
            created: function () {
                wxShare.setParams("楚天财富3周年！开宝箱，赢百万返现！", "点击链接，立即参与", "<?= Yii::$app->params['clientOption']['host']['wap'] ?>/ctcf/p180601/index", "https://admin.hbctcf.com/upload/link/link1527131410769914.png", "<?= Yii::$app->params['weixin']['appId'] ?>", "/ctcf/p180601/add-share");
                wxShare.TimelineSuccessCallBack = function () {
                    $.get("/ctcf/p180601/add-share?scene=timeline&shareUrl=" + encodeURIComponent(location.href))
                };

                if (!this.isHaveDraw && this.bonusType == 'RED_PACKET') {
                    this.drawMsg = "已抽取" + parseFloat(this.bonus) + "元"
                } else if (!this.isHaveDraw && this.bonusType == 'POINT') {
                    this.drawMsg = "已抽取" + parseFloat(this.bonus) + "积分"
                }
            },
            methods: {
                btnEvent: function (value) {
                    var status = this.baseVerify();
                    if (status) {
                        if (value === 1) {
                            location.href = "/deal/deal/index";
                        } else if (value === 2) {
                            var vm = this;
                            if (this.isHaveDraw) {
                                $.ajax({
                                    type: "GET",
                                    url: "/ctcf/p180601/get-reward",
                                    success: function (data) {
                                        if (data.code == 0) {
                                            vm.bonus = data.refAmount;
                                            vm.isHaveDraw = false;
                                            if (data.refType == "RED_PACKET") {
                                                vm.awardMsg = "<span>" + parseFloat(data.refAmount) + "</span>元";
                                                vm.drawMsg = "已抽取" + parseFloat(data.refAmount) + "元";
                                            } else {
                                                vm.awardMsg = "<span>" + parseFloat(data.refAmount) + "</span>积分";
                                                vm.drawMsg = "已抽取" + parseFloat(data.refAmount) + "积分";
                                            }
                                            vm.isShowAwardPop = !this.isShowAwardPop;
                                        } else {
                                            vm.toastCenter(data.message);
                                        }
                                    },
                                    error: function (error) {
                                        vm.toastCenter(error.responseJSON.message);
                                    },
                                });
                            } else {
                                if(this.bonus){
                                    this.toastCenter("您已完成抽奖！");
                                } else {
                                    this.toastCenter("您没有预约抽奖哦！");
                                }

                            }
                        }
                    }
                },
                isShowPop: function (value) {
                    if (value == 1) {
                        this.isShowAwardPop = !this.isShowAwardPop
                    } else if (value == 2) {
                        this.isShowRegularPop = !this.isShowRegularPop
                    }
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
                }
            }
        });
    });
</script>
</body>
</html>