<?php

$this->title = '植树节种好礼';
?>

<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180228/css/index.min.css?v=1.0">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<div class="flex-content" id="app" v-cloak>
    <div :class="{bottleFadeIn:isWatering}" class="bottle" alt="a-watering"></div>
    <div class="banner">
<!--        <img class="bg" src="--><?//= FE_BASE_URI ?><!--wap/campaigns/active20180228/images/banner.png" alt="">-->
        <ul class="ctn-area">
            <li class="times rg">已浇水次数：<span v-html="times"></span>次</li>
            <li @click="draw(3)" class="gifts_01 shake1">
                <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180228/images/gifts_01.png" alt="">
                <span v-show="!giftsStatus.gift3" class="draw">领取</span>
                <span v-show="giftsStatus.gift3" class="received">已领取</span>
            </li>
            <li @click="draw(5)" class="gifts_02 shake2">
                <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180228/images/gifts_02.png" alt="">
                <span v-show="!giftsStatus.gift5" class="draw">领取</span>
                <span v-show="giftsStatus.gift5" class="received">已领取</span>
            </li>
            <li @click="draw(1)" class="gifts_03 shake3">
                <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180228/images/gifts_03.png" alt="">
                <span v-show="!giftsStatus.gift1" class="draw">领取</span>
                <span v-show="giftsStatus.gift1" class="received">已领取</span>
            </li>
            <li @click="draw(7)" class="gifts_04 shake4">
                <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180228/images/gifts_04.png" alt="">
                <span v-show="!giftsStatus.gift7" class="draw">领取</span>
                <span v-show="giftsStatus.gift7" class="received">已领取</span>
            </li>
            <li @click="draw(2)" class="gifts_05 shake1">
                <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180228/images/gifts_05.png" alt="">
                <span v-show="!giftsStatus.gift2" class="draw">领取</span>
                <span v-show="giftsStatus.gift2" class="received">已领取</span>
            </li>
            <li @click="draw(6)" class="gifts_06 shake2">
                <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180228/images/gifts_06.png" alt="">
                <span v-show="!giftsStatus.gift6" class="draw">领取</span>
                <span v-show="giftsStatus.gift6" class="received">已领取</span>
            </li>
            <li @click="draw(4)" class="gifts_07 shake3">
                <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180228/images/gifts_07.png" alt="">
                <span v-show="!giftsStatus.gift4" class="draw">领取</span>
                <span v-show="giftsStatus.gift4" class="received">已领取</span>
            </li>
        </ul>
    </div>

    <div class="operate">
        <ul>
            <li class="clearfix">
                <dl class="lf">
                    <dt>
                        <img @click="getTimes" v-show="!isGet" src="<?= FE_BASE_URI ?>wap/campaigns/active20180228/images/get.png" alt="">
                        <div v-show="isGet" class="obtained">已领取</div>
                    </dt>
                    <dd>每日免费浇水</dd>
                </dl>
                <dl class="lf">
                    <dt>
                        <img class="weixinShare" @click="weixinShare" v-show="!isShare" src="<?= FE_BASE_URI ?>wap/campaigns/active20180228/images/share.png" alt="">
                        <div  v-show="isShare" class="obtained">已分享</div>
                    </dt>
                    <dd>分享增加次数</dd>
                </dl>
                <dl class="lf">
                    <dt><img @click="invest" src="<?= FE_BASE_URI ?>wap/campaigns/active20180228/images/invest.png" alt=""></dt>
                    <dd>投资增加次数</dd>
                </dl>
            </li>
            <li @click="watering">全部浇水(剩余: <span v-html="residueTimes"></span>次)</li>
            <li>
                <p>提示：<br>
                    1. 分享到朋友圈才能增加1次浇水次数；<br>
                    2. 年化投资每1万元增加1次浇水次数。</p>
            </li>
        </ul>
    </div>

    <div class="regular">
        <p class="title" @click="showRegular">收起活动规则 <img :class="{rotate:!isShowRegular}" src="<?= FE_BASE_URI ?>wap/campaigns/active20180228/images/arrows.png" alt=""></p>
        <ol v-show="isShowRegular">
            <li>活动时间：2018.3.12至3.18；</li>
            <li>
                <p>活动奖池：</p>
                <table cellspacing="0" cellpadding="0">
                    <tr>
                        <td>完成浇水次数</td>
                        <td>奖品名称</td>
                        <td>奖品图片</td>
                    </tr>
                    <tr>
                        <td>2次</td>
                        <td>3元红包</td>
                        <td><img src="<?= FE_BASE_URI ?>wap/campaigns/active20180228/images/sg_1.png" alt=""></td>
                    </tr>
                    <tr>
                        <td>3次</td>
                        <td>8元红包</td>
                        <td><img src="<?= FE_BASE_URI ?>wap/campaigns/active20180228/images/sg_2.png" alt=""></td>
                    </tr>
                    <tr>
                        <td>5次</td>
                        <td>10元红包</td>
                        <td><img src="<?= FE_BASE_URI ?>wap/campaigns/active20180228/images/sg_3.png" alt=""></td>
                    </tr>
                    <tr>
                        <td>8次</td>
                        <td>20元红包</td>
                        <td><img src="<?= FE_BASE_URI ?>wap/campaigns/active20180228/images/sg_4.png" alt=""></td>
                    </tr>
                    <tr>
                        <td>28次</td>
                        <td>旺年吉祥物</td>
                        <td><img src="<?= FE_BASE_URI ?>wap/campaigns/active20180228/images/sg_5.png" alt=""></td>
                    </tr>
                    <tr>
                        <td>60次</td>
                        <td>旺旺大礼包</td>
                        <td><img src="<?= FE_BASE_URI ?>wap/campaigns/active20180228/images/sg_6.png" alt=""></td>
                    </tr>
                    <tr>
                        <td>80次</td>
                        <td>50元超市卡</td>
                        <td><img src="<?= FE_BASE_URI ?>wap/campaigns/active20180228/images/sg_7.png" alt=""></td>
                    </tr>
                </table>
            </li>
            <li>活动期间，给树苗浇水达到一定次数，可领取对应礼品，奖励可叠加（每件礼品只能获得一次）。<br>比如：活动期间完成浇水80次，即可领走全部礼品。</li>
            <li>浇水次数怎么获得：<br>
                ①每天免费领取1次；<br>
                ②分享到朋友圈自动增加1次；<br>
                ③年化投资每1万元自动增加1次。
            </li>
            <li>获得浇水机会后，需要<i>主动点击浇水按钮</i>，才能增加已浇水次数！！！</li>
            <li>完成对应浇水次数后，需要<i>主动点击领取礼品</i>，才能获得奖励！！！</li>
            <li>活动期间才能进行浇水；活动结束后将不能再领取礼品。礼品将在领取后的7个工作日内联系发放。大家抓紧时间参与哦！</li>
        </ol>
        <p class="tips">本活动最终解释权归温都金服所有</p>
    </div>
</div>

<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?= FE_BASE_URI ?>libs/wxShare.js?v=3"></script>
<script>
    //dataJson初始化数据
    console.log(dataJson);
    FastClick.attach(document.body);
    var app = new Vue({
        el: '#app',
        data: {
            //获取活动状态
            promoStatus: dataJson.promoStatus,
            isLogin:dataJson.isLoggedIn,
            times: dataJson.wateredCount,
            residueTimes:dataJson.unWateredCount,
            addWateredCount:dataJson.addWateredCount,
            isShowRegular: true,
            isWatering: false,
            giftsStatus: {
                gift1:dataJson.rewards[0].status,
                gift2:dataJson.rewards[1].status,
                gift3:dataJson.rewards[2].status,
                gift4:dataJson.rewards[3].status,
                gift5:dataJson.rewards[4].status,
                gift6:dataJson.rewards[5].status,
                gift7:dataJson.rewards[6].status,
            },
            //是否已经领取
            isGet:dataJson.isReceived,
            isShare:dataJson.isShared,
            /*防止重复点击*/
            flag:true,
        },
        created: function () {
            var _this = this;
            wxShare.setParams("我正在玩【浇水植树种好礼】，海量超市卡、限量礼包等你领，快来一起玩吧！","点击链接，马上参与~","<?= Yii::$app->params['clientOption']['host']['wap'] ?>/promotion/p180312/index","https://static.wenjf.com/upload/link/link1520562638471499.png","<?= Yii::$app->params['weixin']['appId'] ?>","/promotion/p180312/get-share",_this);
            wxShare.TimelineSuccessCallBack = function(){
                $.get("/promotion/p180312/get-share?scene=timeline&shareUrl="+encodeURIComponent(location.href),function(){
                    _this.isShare = !_this.isShare;
                    _this.residueTimes++;
                    _this.toastCenter("增加浇水次数：1次");
                })
            };
            if(this.addWateredCount != 0){
                this.toastCenter("增加浇水次数："+this.addWateredCount+"次")
            }
        },
        methods: {
            showRegular: function () {
                this.isShowRegular = !this.isShowRegular;
            },
            draw: function (target) {
                var status = this.baseVerify();
                var _this = this;
                if(status && this.giftsStatus["gift"+target]==false){
                    $.ajax({
                        type:"GET",
                        url:"/promotion/p180312/get-award",
                        data:{id:dataJson.rewards[target-1].id},
                        success:function(data){
                            if(data.code == 0){
                                _this.toastCenter("领取成功");
                                _this.giftsStatus["gift"+target] = true;
                            } else {
                                _this.toastCenter(data.message,function(){_this.flag = true;});
                            }
                        },
                        error:function(err){_this.toastCenter(err);_this.flag = true;}
                    })
                } else {
                    this.flag = true;
                }
            },
            watering: function () {
                var _this = this;
                var status = this.baseVerify();
                if(status){
                    $.ajax({
                        type:"GET",
                        url:"/promotion/p180312/all-watered",
                        data:'',
                        success:function(data){
                            if(data.code == 0){
                                $("html,body").animate({scrollTop:0},500,function(){
                                    _this.isWatering = true;
                                    $('.bottle').on('webkitAnimationEnd', function () {
                                        $(this).removeClass('bottleFadeIn');
                                        _this.isWatering = false;
                                        _this.toastCenter("完成浇水："+data.count+"次",function(){_this.flag = true;})
                                    });
                                });
                                _this.times +=  data.count;
                                _this.residueTimes = 0;
                            } else {
                                _this.toastCenter(data.message,function(){_this.flag = true;});
                            }
                        },
                        error:function(err){_this.toastCenter(err);_this.flag = true;}
                    });
                }
            },
            getTimes:function(){
                var status = this.baseVerify();
                var _this = this;
                if(status){
                    $.ajax({
                        type:"GET",
                        url:"/promotion/p180312/get-free",
                        data:'',
                        success:function(data){
                            if(data.code == 0){
                                _this.toastCenter("增加浇水次数：1次");
                                _this.isGet = !_this.isGet;
                                _this.residueTimes++;
                            } else {
                                _this.toastCenter(data.message);
                            }
                            _this.flag = true;
                        },
                        error:function(err){_this.toastCenter(err);_this.flag = true;}
                    })
                }
                console.log(status);
            },
            weixinShare:function(){
                var status = this.baseVerify();
                if(status){$('.weixinShare').addClass('share-btn');this.flag = true;}
            },
            invest:function(){
                var status = this.baseVerify();
                if(status){
                    window.location.href = "/deal/deal/index";
                    this.flag = true;
                }
            },
            baseVerify:function(){
                var _this = this;
                if(this.flag){
                    this.flag = false;
                    if(this.promoStatus == 1){
                        this.toastCenter("活动未开始");
                        return false;
                    } else if(this.promoStatus == 2){
                        this.toastCenter("活动已结束");
                        return false;
                    } else if(this.promoStatus == 0){
                        if(!this.isLogin){
                            this.toastCenter("未登录",function(){
                                window.location.href = "/site/login";
                                _this.flag = true;
                            })
                        } else {
                            return true;
                        }
                    }
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
                }, 500);
            }
        }
    });
</script>
