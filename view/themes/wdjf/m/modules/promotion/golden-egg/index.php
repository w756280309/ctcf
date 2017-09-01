<?php

use common\models\adv\Share;

$this->title = '30亿狂欢盛宴';
$this->share = new Share([
    'title' => '温都金服交易额突破30亿，全民砸金蛋，100%中奖！快来加入狂欢盛宴吧！',
    'description' => '温都金服，温州报业旗下理财平台，累计交易额突破30亿！',
    'imgUrl' => FE_BASE_URI.'wap/campaigns/active20170830/images/wx_share.jpg',
    'url' => Yii::$app->request->hostInfo.'/promotion/golden-egg/',
]);
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/popover.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170830/css/index.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/popover.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<style type="text/css">
    [v-cloak] {
        display: none
    }
</style>
<div class="flex-content" id="active">
    <div class="banner">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/banner.png" alt="">
    </div>
    <div class="section-one">
        <img class="section-title" src="<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/section_01.png" alt="">
        <p class="rule-tip">活动期间进行1次任意投资，即可免费参与 砸金蛋！手机、超市卡、红包…应有尽有！</p>
        <img class="my-gifts" v-on:click="giftsShow" src="<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/my-gifts.png" alt="">
        <div class="smashing-egg">
            <transition name="fade">
                <div v-if="!isOpen" v-cloak class="open-before">
                    <img v-on:click="goldOpen" class="gold-egg" src="<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/gold-egg.png" alt="">
                    <img v-on:click="goldOpen" class="hammer" src="<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/hammer.png" alt="">
                </div>
            </transition>
            <transition name="fade">
                <div v-if="isOpen" v-cloak>
                    <img class="open-egg" src="<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/open-egg.png" alt="">
                </div>
            </transition>
        </div>
        <div class="shareAll clearfix">
            <a class="lf" href="/deal/deal/index">投资砸金蛋</a>
            <a class="rg share" href="javascript:void(0);" style="margin-top: 0;">分享好友玩</a>
        </div>
    </div>
    <div class="section-two">
        <img class="section-title" src="<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/section_02.png" alt="">
        <ul class="clearfix">
            <li class="cumulative" v-cloak>已累计年化：{{totalMoney}}万元</li>
            <li class="lf m-b"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/gift_01.png" alt=""></li>
            <li class="rg m-b"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/gift_02.png" alt=""></li>
            <li class="lf"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/gift_03.png" alt=""></li>
            <li class="rg"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/gift_04.png" alt=""></li>
        </ul>
    </div>
    <div id="toTop"></div>
    <div class="regular">
        <div class="regular-head">
            <a class="goInvest" href="/deal/deal/index">去理财</a>
            <p>活动规则</p>
            <a href="#toTop"><img v-on:click="packUp" v-bind:class="{ rotate: isRotate }" src="<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/pointer.png" alt=""></a>
        </div>
        <div class="regular-detail">
            <ol v-cloak v-show="isShow">
                <li>活动时间2017年9月4日-9月8日；</li>
                <li>活动期间完成1次任意投资，获得免费砸金蛋的机会 （最多1次），活动结束后失效；</li>
                <li>
                    <div>活动期间投资温都金服平台理财产品累计年化金额达到指定额度，即可获得相应礼品（不含转让产品）；</div>
                    <table>
                        <tr>
                            <td>累计年化投资金额</td>
                            <td>礼品</td>
                            <td>对应积分</td>
                        </tr>
                        <tr>
                            <td>60,000元</td>
                            <td>旅行胶囊</td>
                            <td>288积分</td>
                        </tr>
                        <tr>
                            <td>360,000元</td>
                            <td>小米充电宝</td>
                            <td>1580积分</td>
                        </tr>
                        <tr>
                            <td>600,000元</td>
                            <td>小米手环2代</td>
                            <td>2980积分</td>
                        </tr>
                        <tr>
                            <td>1,000,000元</td>
                            <td>骆驼户外登山包</td>
                            <td>5188积分</td>
                        </tr>

                    </table>
                </li>
                <li>本次活动中虚拟礼品会立即发放到您的账户，实物礼品将于7个工作日内联系发放，详询客服电话400-101-5151。</li>
            </ol>
            <div class="tips">
                <p>注：年化投资金额=投资金额*项目期限/365</p>
                <p>本活动最终解释权归温都金服所有</p>
            </div>
        </div>
    </div>

</div>

<script>
    var vm = new Vue({
        el: '#active',
        data: {
            isOpen: false,//砸蛋效果
            totalMoney: <?= $totalMoney ?>,//年化投资额
            isRotate: false,//规则按钮翻转
            isShow: false,//活动规则显示数据
            promoStatus:$('input[name=promoStatus]').val(),//活动状态
            isLoggedin:$('input[name=isLoggedin]').val(),//是否登录
        },
        methods: {
            packUp: function () {
                this.isShow = !this.isShow;
                this.isRotate = !this.isRotate;
            },
            goldOpen: function () {
                if(!this.statusDeal()) return;
                //ajax回调
                var _this = this;
                $.ajax({
                    type:"GET",
                    url:"/promotion/golden-egg/draw?key=promo_170903",
                    dataType:"json",
                    success:function(data){
                        if(data.code == 0){
                            poptpl.popComponent({
                                popBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/pop_bg.png) no-repeat',
                                popBorder: 0,
                                closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/pop_close.png",
                                btnMsg: "收下礼品",
                                popTopColor: "#fff",
                                bgSize: "100% 100%",
                                title: '<p style="font-size:0.72rem;">恭喜您获得了</p><p style="font-size:0.5066667rem;">'+data.ticket.name+'</p>',
                                popBtmBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/pop_btn_01.png) no-repeat',
                                popMiddleHasDiv: true,
                                popBtmColor: '#e01021',
                                contentMsg: "<img style='margin:0.3rem auto 0rem;display: block;width: 4.2666667rem;' src='<?= FE_BASE_URI ?>"+data.ticket.path+"' alt=''/>",
                                popBtmBorderRadius: 0,
                                popBtmFontSize: ".50666667rem"
                            }, 'close');
                            _this.isOpen = !_this.isOpen;
                        }
                    },
                    error:function(data){
                        var code = data.responseJSON.code;
                        if(code == 7){
                            //未获得抽奖机会
                            var opt = {
                                btnMsg:'去投资',
                                msg:'任意投资可以获得1次砸金蛋的机会哦，快去投资吧！',
                                btnHref:'/deal/deal/index'
                            };
                            _this.haveNoChange(opt);
                            return;
                        }
                        if(code == 5 || code ==4){
                            //没有抽奖机会
                            var opt = {
                                btnMsg:'我知道了',
                                msg:'您已经砸过金蛋了，去看看其他活动吧！',
                                btnHref:'javascript:poptpl.confirmBtn();'
                            };
                            _this.haveNoChange(opt);
                            return;
                        }
                        _this.errorMsg(data.responseJSON.message);
                    }
                })
            },
            giftsShow: function () {
                if(!this.statusDeal()) return;
                //调接口获取对应的数据信息
                $.ajax({
                    type:"GET",
                    url:"/promotion/golden-egg/award-list?key=promo_170903",
                    dataType:"json",
                    success:function(data){
                        if(data.length==0){
                            poptpl.popComponent({
                                popBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/pop_bg_01.png) no-repeat',
                                popBorder: 0,
                                closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/pop_close.png",
                                popBtmHas:false,
                                popTopColor: "#fff",
                                bgSize: "100% 100%",
                                title: '<p style="font-size:0.72rem;">我的奖品</p>',
                                popBtmBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/pop_btn_01.png) no-repeat',
                                popMiddleHasDiv: true,
                                contentMsg: "<p style='color:#fff;margin-top: 2.5rem'>您还没有获得奖品哦！</p>",
                            }, 'close');
                        } else {
                            poptpl.popComponent({
                                popBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/pop_bg.png) no-repeat',
                                popBorder: 0,
                                closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/pop_close.png",
                                popBtmHas:false,
                                popTopColor: "#fff",
                                bgSize: "100% 100%",
                                title: '<p style="font-size:0.72rem;">我的奖品</p>',
                                popBtmBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/pop_btn_01.png) no-repeat',
                                popMiddleHasDiv: true,
                                contentMsg: "<img style='margin:0.3rem auto 0.5rem;display: block;width: 4.2666667rem;' src='<?= FE_BASE_URI ?>"+data[0].path+"' alt=''/><p style='color:#fff;margin-top: 1rem;font-size:0.7rem;'>"+data[0].name+"</p>",
                            }, 'close');
                        }
                    }
                })
            },
            statusDeal:function(){
                if(this.promoStatus == 1) {
                    this.errorMsg('活动未开始');
                    return false;
                } else if(this.promoStatus == 2){
                    this.errorMsg('活动已结束');
                    return false;
                }
                if(this.isLoggedin == 'false'){
                    var opt = {
                        btnMsg:'马上登录',
                        msg:'您还没有登录哦！',
                        btnHref:'/site/login?next=/promotion/golden-egg/'
                    };
                    this.haveNoChange(opt);
                    return false;
                }
                return true;
            },
            errorMsg:function(msg){
                poptpl.popComponent({
                    popBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/pop_bg_01.png) no-repeat',
                    popBorder: 0,
                    closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/pop_close.png",
                    title: '<p style="font-size:0.62rem;margin: 3.5rem 0 2rem;color: #fff;">'+msg+'</p>',
                    popBtmHas: false,
                });
            },
            haveNoChange:function(opt){
                poptpl.popComponent({
                    popBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/pop_bg_01.png) no-repeat',
                    popBorder: 0,
                    closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/pop_close.png",
                    btnMsg: opt.btnMsg,
                    title: '<p style="font-size:0.72rem;margin: 2rem 0;color: #fff;">'+opt.msg+'</p>',
                    popBtmBackground: 'url(<?= FE_BASE_URI ?>wap/campaigns/active20170830/images/pop_btn_01.png) no-repeat',
                    popBtmBorderRadius: 0,
                    popBtmColor: '#e01021',
                    popBtmFontSize: ".50666667rem",
                    btnHref: opt.btnHref//默认是javascript:void(0);
                });
            }
        }
    });
</script>