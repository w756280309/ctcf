<?php

$this->title = '圣诞抽奖活动';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20171218/css/gifts-list.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/popover.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20171218/css/index.css?v=1.1">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/iscroll.js"></script>
<script src="<?= FE_BASE_URI ?>libs/handlebars-v4.0.10.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/js/popover.js"></script>
<script src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/js/gifts-list.js?v=1.0"></script>
<style>
    [v-cloak]{display: none}
</style>
<div class="flex-content animatedSpecial" id="app" v-cloak>
    <div class="part_01">
        <img class="bg" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/bg_01.png" alt="">
        <img v-cloak v-if="isShowFog" v-bind:class="{dissipate:dissipate.fog1}"  class="fog animatedSpecial" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/fog_04.png" alt="">
        <div class="balloon">100万元</div>
        <div v-cloak v-if="lock.lock1" @click="deblock(5)" class="lock animated shake delay4"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/lock.png" alt=""></div>
        <img class="moneyBag5  animated delay4" v-bind:class="{swing:dissipate.fog1}" @click="clickMoneyBag(1)" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/money_bag_05.png" alt="">
        <img v-cloak class="hand1 animated delay4" v-if="hand.hand1" v-bind:class="{point:dissipate.fog1}" @click="clickMoneyBag(1)" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/hand.png" alt="">
    </div>
    <div class="part_02">
        <img class="bg" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/bg_02.png" alt="">
        <img v-cloak v-if="isShowFog" v-bind:class="{dissipate:dissipate.fog2}"  class="fog animatedSpecial" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/fog_03.png" alt="">
        <div class="balloon">50万元</div>
        <div v-cloak v-if="lock.lock2" @click="deblock(4)" class="lock animated shake delay3"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/lock.png" alt=""></div>
        <img class="moneyBag4 animated delay2" v-bind:class="{swing:dissipate.fog2}" @click="clickMoneyBag(2)" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/money_bag_04.png" alt="">
        <img v-cloak class="hand2 animated delay3" v-if="hand.hand2" v-bind:class="{point:dissipate.fog2}" @click="clickMoneyBag(2)" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/hand.png" alt="">
    </div>
    <div class="part_03">
        <img class="bg" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/bg_03.png" alt="">
        <img v-cloak v-if="isShowFog" v-bind:class="{dissipate:dissipate.fog3}" class="fog animatedSpecial" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/fog_02.png" alt="">
        <div class="balloon">20万元</div>
        <div v-cloak v-if="lock.lock3" @click="deblock(3)" class="lock  animated shake delay2"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/lock.png" alt=""></div>
        <img class="moneyBag3 animated delay2" v-bind:class="{swing:dissipate.fog3}" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/money_bag_03.png" @click="clickMoneyBag(3)" alt="">
        <img v-cloak class="hand3 animated delay3" v-if="hand.hand3" v-bind:class="{point:dissipate.fog3}" @click="clickMoneyBag(3)" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/hand.png" alt="">
    </div>
    <div class="part_04">
        <img class="bg" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/bg_04.png" alt="">
        <img v-cloak v-if="isShowFog" v-bind:class="{dissipate:dissipate.fog4}" class="fog animatedSpecial" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/fog_01.png" alt="">
        <div class="balloon">5万元</div>
        <div v-cloak v-if="lock.lock4" @click="deblock(2)" class="lock animated shake delay1"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/lock.png" alt=""></div>
        <img class="moneyBag2 animated delay2" v-bind:class="{swing:dissipate.fog4}" @click="clickMoneyBag(4)" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/money_bag_02.png" alt="">
        <img v-cloak class="hand4 animated delay2" v-if="hand.hand4" v-bind:class="{point:dissipate.fog4}" @click="clickMoneyBag(4)" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/hand.png" alt="">
    </div>
    <div class="part_05">
        <img class="bg" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/bg_05.png" alt="">
        <img class="moneyBag1 delay1 animated swing" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/money_bag_01.png" @click="clickMoneyBag(5)" alt="">
        <img v-cloak class="hand5 animated point" v-if="hand.hand5" @click="clickMoneyBag(5)" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/hand.png" alt="">
    </div>
    <div class="part_06">
        <img class="bg" src="<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/bg_06.png" alt="">
    </div>
    <div class="fixedBar">
        <p @click="giftListExamine" class="giftList">我的奖品</p>
        <p @click="regular" class="activeRegular">活动规则</p>
        <div class="times"><p>剩余抽奖次数<br><i v-html="activeDrawnCount" v-cloak>0</i>次</p></div>
        <div class="money"><p>已累计年化<br><i v-html="annualInvest" v-cloak>0</i>万元</p></div>
    </div>
    <span class="pst1"></span>
    <span class="pst2"></span>
    <span class="pst3"></span>
</div>
<div id="Myanchor"></div>
<script>
    $(function(){
        FastClick.attach(document.body);
        var btm = $("#Myanchor")[0].offsetTop;
        $("body,html").animate({scrollTop:btm},10);
        var isLoggedin = $("input[name='isLoggedin']").val()=="true"?true:false;
        var app = new Vue({
            el: '#app',
            data: {
                activeStatus:$("input[name='promoStatus']").val(),
                isLogin:isLoggedin,
                activeDrawnCount:<?= $data['activeDrawnCount'] ?>,
                annualInvest:<?= $data['annualInvest'] ?>,
                isShowFog: true,
                isShowLock: true,
                isFirstShow:true,
                isShowStrategy:<?= $data['isShowRegular'] ?>,
                grade:<?= $data['grade'] ?>,/**5顶级 0最低级*/
                dissipate:{
                    fog4:false,
                    fog3:false,
                    fog2:false,
                    fog1:false,
                },
                lock:{
                    lock4:true,
                    lock3:true,
                    lock2:true,
                    lock1:true,
                },
                hand:{
                    hand1:false,
                    hand2:false,
                    hand3:false,
                    hand4:false,
                    hand5:true,
                }
            },
            methods: {
                deblock:function(index){
                    if(!this.promoStatus()) return;
                    switch (index) {
                        case 2:
                            this.debLockingPort(2);
                            break;
                        case 3:
                            this.debLockingPort(3);
                            break;
                        case 4:
                            this.debLockingPort(4);
                            break;
                        case 5:
                            this.debLockingPort(5);
                            break;
                        default:break;
                    };

                },
                clickMoneyBag:function(index){
                    if(!this.promoStatus()) return;
                    /**有没有抽奖次数 调礼包接口*/
                    this.moneyBagPort(index);
                },
                initPage:function(){
                    if(this.isShowStrategy){
                        poptpl.popComponent({
                            closeTop:"-1.15rem",
                            title:"任务攻略",
                            popTopColor:"#3b3b3b",
                            popTopFontSize:"0.74666667rem",
                            popBackground:"#fff",
                            popBorder:"0.0933333rem solid #945a2d",
                            popMiddleHasDiv:true,
                            closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/close.png",
                            contentMsg:"<ul><li style='list-style: decimal;font-size:0.56rem;color:#3b3b3b; text-align:left;'>任意出借一次，即可抽1次奖； 每年化出借10万元可再抽1次。 <i style='color: #e85050;font-style: normal;'>比如：<br>年化出借100万元=11次哦！</i></li><li style='list-style: decimal;font-size:0.56rem;color:#3b3b3b; text-align:left;margin: 0.3rem 0 0.4rem;'>活动期间出借额越高，奖池越 豪华(5个档次)！升级奖池后的 首次抽奖必中大奖！</li></ul>",
                            btnMsg: "去出借",
                            popBtmFontSize:"0.666667rem",
                            popBtmBorderRadius:"0.14rem",
                            popBtmBackground:"#c0926d",
                            btnHref:"/deal/deal/index"
                        },this.pageAnimation());
                    }
                    this.pageAnimation(this.grade);
                    this.bindEvent();
                },
                regular:function(){
                    poptpl.popComponent({
                        closeTop:"-1.15rem",
                        title:"活动规则",
                        popTopColor:"#3b3b3b",
                        popTopFontSize:"0.666667rem",
                        popBackground:"#fff",
                        popBorder:"0.0933333rem solid #945a2d",
                        popMiddleHasDiv:true,
                        closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/close.png",
                        contentMsg:"" +
                        "<ul class='regularStyle'><li>活动时间：2017.12.25-12.30；</li>" +
                        "<li>活动期间首次出借任意金额，可获得1次抽奖次数；每年化出借10万元，可再累加1次抽奖次数；</li>" +
                        "<li>活动期间奖池分为5档，分别为：年化出借≤5万、5万＜年化出借≤20万、20万＜年化出借≤50万、50万＜年化出借≤100万、年化出借＞100万；</li>" +
                        "<li>升级奖池后的首次抽奖必中对应奖池内大奖之一；</li>" +
                        "<li>奖品将在活动结束后7个工作日内联系发放，请保持通讯畅通。</li></ul><p class='regularTips'>本活动最终解释权归楚天财富所有</p>",
                        popBtmHas:false,
                    });
                },
                giftListExamine:function(){
                    if(!this.promoStatus()) return;
                    var that = this;
                    var xhr = $.get('/promotion/p171225/award-list?key=promo_171225');
                    xhr.done(function(res){
                        if(res.length!=0){
                            res.map(function(item,index){
                               item["path"] = "<?= FE_BASE_URI ?>"+item["path"]
                            });
                            giftsList({
                                isGifts:true,//有奖品，无奖品为false
                                closeImg:'<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/close.png',
                                list:res
                            });
                        } else {
                            giftsList({
                                isGifts:false,//有奖品，无奖品为false
                                closeImg:'<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/close.png',
                            });
                        }
                    });
                    xhr.fail(function(jqXHR){
                        var resp = $.parseJSON(jqXHR.responseText);
                        that.portFailCallBack(resp);
                    });
                },
                haveNoDrawChance:function(){
                    poptpl.popComponent({
                        closeTop:"-1.15rem",
                        popTopHas:false,
                        popBackground:"#fff",
                        popBorder:"0.0933333rem solid #945a2d",
                        popMiddleHasDiv:true,
                        closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/close.png",
                        contentMsg:"<div class='noDrawChance'>您没有抽奖次数了哦！</div><p style='padding-right:7%;line-height:0.8266667rem;font-size:0.5333333333rem;color:#3b3b3b; text-align:left;'>活动期间任意出借一次，获得1次抽奖机会；每年化出借10万元，可再获得1次！</br><i style='color: #e85050;font-style: normal;'>例：年化出借100万元=11次</i></p>",
                        btnMsg: "获取次数",
                        popBtmFontSize:"0.666667rem",
                        popBtmBorderRadius:"0.14rem",
                        popBtmBackground:"#c0926d",
                        btnHref:"/deal/deal/index"
                    })
                },
                giftsProp:function(data){
                    poptpl.popComponent({
                        closeTop:"-1.15rem",
                        popTopHas:false,
                        popBackground:"#fff",
                        popBorder:"0.0933333rem solid #945a2d",
                        popMiddleHasDiv:true,
                        closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/close.png",
                        contentMsg:"<div class='giftsTitle'><p>恭喜您获得</p><img src='<?= FE_BASE_URI ?>"+data.path+"' alt=''><i>"+data.name+"</i></div>",
                        btnMsg: "收下礼品",
                        popBtmFontSize:"0.666667rem",
                        popBtmBorderRadius:"0.14rem",
                        popBtmBackground:"#c0926d",
                    },"close");
                },
                lockPond:function(data){
                    poptpl.popComponent({
                        closeTop:"-1.15rem",
                        popTopHas:false,
                        popBackground:"#fff",
                        popBorder:"0.0933333rem solid #945a2d",
                        popMiddleHasDiv:true,
                        closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/close.png",
                        contentMsg:"<div class='lockPond'><p class='title'>当前奖池未解锁哦！还差<i>"+data.deficiencyAnnual+"</i>万元年化即可解锁！</p><p class='showGifts'>本级奖池首抽<br>必中以下奖品之一</p>" +
                        "<div class='giftLists clearfix'>" +
                        "<dl class='lf'><dt><img src='<?= FE_BASE_URI ?>"+data.currentPool[0].path+"' alt=''></dt><dd>"+data.currentPool[0].name+"</dd></dl>" +
                        "<dl class='lf'><dt><img src='<?= FE_BASE_URI ?>"+data.currentPool[1].path+"' alt=''></dt><dd>"+data.currentPool[1].name+"</dd></dl>" +
                        "<dl class='lf'><dt><img src='<?= FE_BASE_URI ?>"+data.currentPool[2].path+"' alt=''></dt><dd>"+data.currentPool[2].name+"</dd></dl>" +
                        "</div>" +
                        "</div>",
                        btnMsg: "解锁奖池",
                        popBtmFontSize:"0.666667rem",
                        popBtmBorderRadius:"0.14rem",
                        popBtmBackground:"#c0926d",
                        btnHref:"/deal/deal/index"
                    })
                },
                pond:function(data){
                    poptpl.popComponent({
                        closeTop:"-1.15rem",
                        popTopHas:false,
                        popBackground:"#fff",
                        popBorder:"0.0933333rem solid #945a2d",
                        popMiddleHasDiv:true,
                        closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/close.png",
                        contentMsg:"<div class='pond'><p class='title'>当前大奖</p>" +
                        "<div class='giftLists clearfix'>" +
                        "<dl class='lf'><dt><img src='<?= FE_BASE_URI ?>"+data.currentPool[0].path+"' alt=''></dt><dd>"+data.currentPool[0].name+"</dd></dl>" +
                        "<dl class='lf'><dt><img src='<?= FE_BASE_URI ?>"+data.currentPool[1].path+"' alt=''></dt><dd>"+data.currentPool[1].name+"</dd></dl>" +
                        "<dl class='lf'><dt><img src='<?= FE_BASE_URI ?>"+data.currentPool[2].path+"' alt=''></dt><dd>"+data.currentPool[2].name+"</dd></dl>" +
                        "</div>" +
                        "<a id='lottery' class='draw' href='javascript:void(0)'>现在就抽</a>"+
                        "<p class='title'>下级新增(还需要年化: "+data.deficiencyAnnual+"万元)</p><div class='giftLists clearfix'>" +
                        "<dl class='lf'><dt><img src='<?= FE_BASE_URI ?>"+data.nextPool[0].path+"' alt=''></dt><dd>"+data.nextPool[0].name+"</dd></dl>" +
                        "<dl class='lf'><dt><img src='<?= FE_BASE_URI ?>"+data.nextPool[1].path+"' alt=''></dt><dd>"+data.nextPool[1].name+"</dd></dl>" +
                        "<dl class='lf'><dt><img src='<?= FE_BASE_URI ?>"+data.nextPool[2].path+"' alt=''></dt><dd>"+data.nextPool[2].name+"</dd></dl>" +
                        "</div>" +
                        "<p class='tips'>升级奖池后，以上新增礼品必中其一！</p>"+
                        "<a class='draw' href='/deal/deal/index'>升级奖池</a>"+
                        "</div>",
                        popBtmHas:false,
                    })
                },
                lastPond:function(data){
                    poptpl.popComponent({
                        closeTop:"-1.15rem",
                        popTopHas:false,
                        popBackground:"#fff",
                        popBorder:"0.0933333rem solid #945a2d",
                        popMiddleHasDiv:true,
                        closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20171218/images/close.png",
                        contentMsg:"<div class='pond'><p class='title'>当前大奖</p>" +
                        "<div class='giftLists clearfix'>" +
                        "<dl class='lf'><dt><img src='<?= FE_BASE_URI ?>"+data.currentPool[0].path+"' alt=''></dt><dd>"+data.currentPool[0].name+"</dd></dl>" +
                        "<dl class='lf'><dt><img src='<?= FE_BASE_URI ?>"+data.currentPool[1].path+"' alt=''></dt><dd>"+data.currentPool[1].name+"</dd></dl>" +
                        "<dl class='lf'><dt><img src='<?= FE_BASE_URI ?>"+data.currentPool[2].path+"' alt=''></dt><dd>"+data.currentPool[2].name+"</dd></dl>" +
                        "</div>" +
                        "<a id='lottery' class='draw' href='javascript:void(0)'>现在就抽</a>"+
                        "</div>",
                        popBtmHas:false,
                    })
                },
                promoStatus:function(){
                    switch (this.activeStatus){
                        case '0':
                            if(!this.isLogin){
                                window.location.href = "/site/login";
                                return false;
                            } else {
                                return true;
                            }
                            break;
                        case '1':
                            this.toastCenter("活动未开始");
                            return false;
                            break;
                        case '2':
                            this.toastCenter("活动已结束");
                            return false;
                            break;
                        default:
                            break;
                    }
                },
                toastCenter:function (val, active) {
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
                moneyBagPort:function(index){
                    var that = this;
                    var xhr = $.get('/promotion/p171225/unpack');
                    xhr.done(function(res){
                        if(res.code == 4){
                            that.haveNoDrawChance();
                        } else {
                            if(res.activeDrawnCount == 0){
                                that.haveNoDrawChance();
                            }  else {
                                if(res.page.grade == 5){
                                    that.lastPond(res.page);
                                } else {
                                    that.pond(res.page);
                                }
                            }
                        }
                    });
                    xhr.fail(function(jqXHR){
                        var resp = $.parseJSON(jqXHR.responseText);
                        that.portFailCallBack(resp);
                    });
                },
                debLockingPort:function(grade){
                    var that = this;
                    var xhr = $.get('/promotion/p171225/unlock?grade='+grade);
                    xhr.done(function(res){
                        if(res.code==4){
                            that.lockPond(res.page);
                        } else {
                            that.lockFog(res.page.grade);
                        }
                    });
                    xhr.fail(function(jqXHR){
                        var resp = $.parseJSON(jqXHR.responseText);
                        that.portFailCallBack(resp);
                    });
                },
                bindEvent:function(){
                    var that= this;
                    $("body").on("click","#lottery",function(){
                        /**调抽奖接口*/
                        var xhr = $.get('/promotion/p171225/draw?key=promo_171225');
                        xhr.done(function(res){
                            $(".mask,.pop").remove();
                            that.giftsProp(res.ticket);
                            if(that.activeDrawnCount<=0)return;
                            that.activeDrawnCount--;
                        });
                        xhr.fail(function(jqXHR){
                            var resp = $.parseJSON(jqXHR.responseText);
                            that.portFailCallBack(resp);
                        });
                    })
                },
                lockFog:function(grade){
                    switch (grade) {
                        case 1:
                            this.dissipate.fog1 = true;
                            this.lock.lock1 = false;
                            this.dissipate.fog2 = true;
                            this.lock.lock2 = false;
                            this.dissipate.fog3 = true;
                            this.lock.lock3 = false;
                            this.dissipate.fog4 = true;
                            this.lock.lock4 = false;
                            this.hand.hand5 = false;
                            this.hand.hand4 = false;
                            this.hand.hand3 = false;
                            this.hand.hand2 = false;
                            this.hand.hand1 = true;
                            break;
                        case 2:
                            this.dissipate.fog2 = true;
                            this.lock.lock2 = false;
                            this.dissipate.fog3 = true;
                            this.lock.lock3 = false;
                            this.dissipate.fog4 = true;
                            this.lock.lock4 = false;
                            this.hand.hand5 = false;
                            this.hand.hand4 = false;
                            this.hand.hand3 = false;
                            this.hand.hand2 = true;
                            break;
                        case 3:
                            this.dissipate.fog3 = true;
                            this.lock.lock3 = false;
                            this.dissipate.fog4 = true;
                            this.lock.lock4 = false;
                            this.hand.hand5 = false;
                            this.hand.hand4 = false;
                            this.hand.hand3 = true;
                            break;
                        case 4:
                            this.dissipate.fog4 = true;
                            this.lock.lock4 = false;
                            this.hand.hand5 = false;
                            this.hand.hand4 = true;
                            break;
                        default:break;
                    };
                    this.forbidTouchMove(false);
                },
                pageAnimation:function (grade) {
                    var b1 = 0;
                    var b2 = $(".pst1")[0].offsetTop;
                    var b3 = $(".pst2")[0].offsetTop;
//                    var b4 = $(".pst3")[0].offsetTop;
                    switch (grade){
                        case 5:
                            this.scrollTOTop(b1);
                            break;
                        case 4:
                            this.scrollTOTop(b2);
                            break;
                        case 3:
                            this.scrollTOTop(b3);
                            break;
                        case 2:
                            this.lockFog(4);
                            break;
                        default:
                            break;
                    }

                },
                scrollTOTop:function(toTop){
                    var that = this;
                    var mygrade;
                    switch (this.grade){
                        case 5:
                            mygrade = 1;
                            break;
                        case 4:
                            mygrade = 2;
                            break;
                        case 3:
                            mygrade = 3;
                            break;
                        case 2:
                            mygrade = 4;
                            break;
                        case 1:
                            mygrade = 5;
                            break;
                    };
                    this.forbidTouchMove(true);
                    $("body,html").animate({scrollTop:toTop},3000,function(){
                        that.lockFog(mygrade);
                    })
                },
                forbidTouchMove:function(flag){
                    var that =this;
                    if(flag){
                        document.querySelector("#app").addEventListener("touchmove",that.eventHandler);
                    } else {
                        document.querySelector("#app").removeEventListener("touchmove",that.eventHandler);
                    }
                },
                eventHandler:function(){
                    var event = event || window.event;
                    event.preventDefault();
                    event.stopPropagation();
                },
                portFailCallBack:function(data){
                    var that = this;
                    if(data.code == 3){
                        window.location.href = "/site/login";
                    } else {
                        that.toastCenter(data.message);
                    }
                }
            }
        });
        app.initPage();
    })

</script>
