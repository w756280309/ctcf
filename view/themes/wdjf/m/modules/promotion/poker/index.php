<?php
$this->title = '周周乐';
$this->registerJs('forceReload_V2();');
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/happy-week/css/index.css?v=20171016">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>/libs/swiper/swiper-3.4.2.min.css">
<script src="<?= FE_BASE_URI ?>/libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>/libs/iscroll.js"></script>
<script src="<?= FE_BASE_URI ?>/libs/vue.min.js"></script>
<style>
    [v-cloak] { display: none }
    #weekly-happy {
        padding-bottom: 0.5rem;
    }
</style>

<div class="flex-content" id="weekly-happy">
    <mask-list v-show="true" v-cloak></mask-list>
    <div class="banner">
        <img src="<?= FE_BASE_URI ?>wap/happy-week/images/logo_banner.png" alt="">
	    <div class="swiper-container">
		    <div class="swiper-wrapper">
			    <div class="swiper-slide swiper-one">
				    <p class="line-one">{{qishu}}期</p>
				    <p class="line-two">一等奖中奖用户</p>
				    <p class="line-three">{{userMobile}}</p>
			    </div>
			    <div class="swiper-slide swiper-two">
				    <p class="line-one">点亮卡牌赢大奖</p>
				    <p class="line-two">200元超市卡</p>
				    <p class="line-three">幸运号码送积分</p>
			    </div>
		    </div>
		    <!-- 如果需要分页器 -->
		    <div class="swiper-pagination"></div>
	    </div>
        <div class="left-timer">
            <p v-cloak v-if="!lottery">开奖时间：每周一上午10点</p>
            <p v-cloak v-if="lottery">距离开奖还剩: <span id="timer"><i>--</i>:<i>--</i>:<i>--</i></span></p>
        </div>
    </div>
    <div class="term-list">
        <p class="til clearfix"><span v-cloak>{{currentQishu}}期</span><span class="regular rg" v-on:click="regularShow">详细规则＞</span></p>
        <div class="ctn">
            <ul class="luck-Num clearfix">
                <li class="lf mr8" v-on:click="remake">
                    <div v-cloak v-if="isLoggedin" class="gift">
                        <img data-state="state1" class="lucky-num" src="<?= FE_BASE_URI ?>wap/happy-week/images/lucky-num.png" alt="">
                        <img data-state="state1" class="com-num" :src=oneCardUrl alt="">
                        <img data-state="state1" class="com-num-btm" :src=oneCardUrl alt="">
                    </div>
                    <div v-cloak class="noGift" v-if="!isLoggedin">
                        <img data-state="state1" class="type-bg" src="<?= FE_BASE_URI ?>wap/happy-week/images/type-bg_01.png" alt="">
                    </div>
                </li>
                <li class="lf mr8"  v-on:click="remake">
                    <div v-cloak v-if="isLoggedin"  class="gift">
                        <img data-state="state2" class="login-num" src="<?= FE_BASE_URI ?>wap/happy-week/images/login-num.png" alt="">
                        <img data-state="state2" class="com-num" :src=twoCardUrl alt="">
                        <img data-state="state2" class="com-num-btm" :src=twoCardUrl alt="">
                    </div>
                    <div v-cloak class="noGift" v-if="!isLoggedin">
                        <img data-state="state2" class="type-bg" src="<?= FE_BASE_URI ?>wap/happy-week/images/type-bg_02.png" alt="">
                    </div>
                </li>
                <li class="lf mr8"  v-on:click="remake">
                    <div v-cloak v-if="isLoggedin && threeCard"  class="gift">
                        <img data-state="state3" class="sigin-num" src="<?= FE_BASE_URI ?>wap/happy-week/images/sigin-num.png" alt="">
                        <img data-state="state3" class="com-num" :src=threeCardUrl alt="">
                        <img data-state="state3" class="com-num-btm" :src=threeCardUrl alt="">
                    </div>
                    <div v-cloak class="noGift" v-if="!isLoggedin || !threeCard">
                        <img data-state="state3" class="type-bg" src="<?= FE_BASE_URI ?>wap/happy-week/images/type-bg_03.png" alt="">
                    </div>
                </li>
                <li class="lf"  v-on:click="remake">
                    <div v-cloak v-if="isLoggedin && fourCard"  class="gift">
                        <img data-state="state4" class="invest-num" src="<?= FE_BASE_URI ?>wap/happy-week/images/invest-num.png" alt="">
                        <img data-state="state4" class="com-num" :src=fourCardUrl alt="">
                        <img data-state="state4" class="com-num-btm" :src=fourCardUrl alt="">
                    </div>
                    <div v-cloak class="noGift" v-if="!isLoggedin || !fourCard">
                        <img data-state="state4" class="type-bg" src="<?= FE_BASE_URI ?>wap/happy-week/images/type-bg_04.png" alt="">
                    </div>
                </li>
            </ul>
        </div>
    </div>
	<a class="record"  :href="linkTo" v-on:click="goLogin"><img src="<?= FE_BASE_URI ?>wap/happy-week/images/clock.png" alt="">历史开奖号码</a>


	<p v-if="isFirstshow"  v-cloak class="win-num clearfix" style="margin: 0.4rem 0 0.3rem 4.32277%">
        <span class="com-til lf">{{qishu}} 期开奖号码：</span>
        <img class="com-card lf" src="<?= FE_BASE_URI ?>wap/happy-week/images/type_04.png" alt=""><span class="com-fz lf grey mr8" v-html="rewardInfo.rewardCard[0]"></span>
        <img class="com-card lf mt3" src="<?= FE_BASE_URI ?>wap/happy-week/images/type_03.png" alt=""><span class="com-fz lf red mr8" v-html="rewardInfo.rewardCard[1]"></span>
        <img class="com-card lf mt3" src="<?= FE_BASE_URI ?>wap/happy-week/images/type_02.png" alt=""><span class="com-fz lf grey mr8" v-html="rewardInfo.rewardCard[2]"></span>
        <img class="com-card lf" src="<?= FE_BASE_URI ?>wap/happy-week/images/type_01.png" alt=""><span class="com-fz lf red mr8" v-html="rewardInfo.rewardCard[3]"></span>
    </p>
    <p v-if="isLoggedin && isFirstshow" v-cloak class="u-gifts clearfix" >
        <span class="com-til lf">{{qishu}} 期您的奖品：</span>
        <span class="gifts-detail red lf" v-html="rewardInfo.title">8积分</span>
    </p>

    <dl class="des clearfix">
        <dt class="lf" style="width: 50%;">
            <p>活动周期</p>
            <p>每周一上午10点至开奖前</p>
        </dt>
        <dd class="lf" style="width: 50%;">
            <ul class="instruct">
                <li><img src="<?= FE_BASE_URI ?>wap/happy-week/images/type_04.png" alt="">每周专属幸运号码</li>
                <li><img src="<?= FE_BASE_URI ?>wap/happy-week/images/type_03.png" alt="">每周首次登录时间</li>
                <li><img src="<?= FE_BASE_URI ?>wap/happy-week/images/type_02.png" alt="">每周首次签到日期</li>
                <li><img src="<?= FE_BASE_URI ?>wap/happy-week/images/type_01.png" alt="">每周首次投资时间</li>
            </ul>
        </dd>
    </dl>

    <!--规则弹框-->
    <div class="prizes-box" v-show="rulesIsShow" v-cloak>
        <div class="outer-box">
            <img v-on:click="regularShow" class="close" src="<?= FE_BASE_URI ?>wap/happy-week/images/close.png" alt="">
            <div class="prizes-pomp">
                <p class="prizes-title">活动规则</p>
                <div id="wrapper">
                    <ol>
                        <li>奖品介绍<br>一等奖：<span style="color:#ef5b4f">200元超市卡</span>（每周至少一位哦）<br>幸运奖：<span style="color:#ef5b4f">随机积分奖励</span>（不限人数）</li>
                        <li>开奖时间：每周一上午10点</li>
                        <li>中奖规则<br>一等奖：用户抽奖码与系统公布号码完全一致，即获奖（如果当期没有任何用户号码完全一致，系统将在幸运奖用户中，选取投资最早的用户作为一等奖得主）。<br>幸运奖：黑桃卡片数字和系统公布的黑桃幸运数字一致即可中奖。</li>
                        <li>卡片点亮秘诀<br>黑桃卡片：幸运卡片，进入活动页面即可点亮；<br>红桃卡片：登录即可点亮；<br>梅花卡片：签到即可点亮；<br>方块卡片：任意投资即可点亮（活动期间尽早投资可能有惊喜哦）。</li>
                        <li>积分奖励将立即发放到您的账户，实物奖励将在中奖后7个工作日内联系发放，如有疑问请联系客服电话400-101-5151。</li>

                        <div class="last_li">播种一个行动，收获一个惊喜，中奖不靠人品，因为我们是有算法的：<i v-on:click="showRegular" class="regular_tips">号码生成规则<span :class="{rot180:isShowRegular}">>></span></i></div>
                        <div class="regular_detail" v-if="isShowRegular">
                            <div class="user_code">用户抽奖码：</div>
                            <div><img src="<?= FE_BASE_URI ?>wap/happy-week/images/type_04.png" alt="">每周专属幸运号码(完全随机)</div>
                            <div><img src="<?= FE_BASE_URI ?>wap/happy-week/images/type_03.png" alt="">每周首次登录时间(秒数) T1 %13，0=K</div>
                            <div><img src="<?= FE_BASE_URI ?>wap/happy-week/images/type_02.png" alt="">每周首次签到时间(秒数) T2 %13，0=K</div>
                            <div><img src="<?= FE_BASE_URI ?>wap/happy-week/images/type_01.png" alt="">每周首次投资时间(秒数) T3 %13，0=K</div>
                            <div class="plat_code">平台中奖码：</div>
                            <div><img src="<?= FE_BASE_URI ?>wap/happy-week/images/type_04.png" alt="">每周总交易额&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;S %13，0=K</div>
                            <div><img src="<?= FE_BASE_URI ?>wap/happy-week/images/type_03.png" alt="">每周登录用户数&nbsp;&nbsp;&nbsp;N1 %13，0=K</div>
                            <div><img src="<?= FE_BASE_URI ?>wap/happy-week/images/type_02.png" alt="">每周签到用户数&nbsp;&nbsp;&nbsp;N2 %13，0=K</div>
                            <div><img src="<?= FE_BASE_URI ?>wap/happy-week/images/type_01.png" alt="">每周投资用户数&nbsp;&nbsp;&nbsp;N3 %13，0=K</div>
                        </div>
                        <p style="margin-top: 0.3rem;">本活动最终解释权归温都金服所有</p>
                    </ol>

                </div>
            </div>
        </div>
    </div>

    <!--中奖弹框-->
    <div class="mask-gift" v-cloak v-if="isShowMaskList">
        <div class="big-list">
            <img v-on:click.stop="closePopover" class="pop_close" src="<?= FE_BASE_URI ?>wap/happy-week/images/pop_close.png" alt="">
            <img class="bg-com" :src="options.state" alt="">
            <img class="lucky-num-mask" :src="options.num" alt="">
            <img class="lucky-num-reverse" :src="options.num" alt="">
            <div class="content">
                <p v-html="options.title"></p>
                <p v-html="options.content"></p>
            </div>
            <a class="go-btn" v-show="options.btnIsShow" :href="options.href"  v-html="options.btn"></a>
        </div>
    </div>

    <!--每周一上午十点对中奖用户提示-->
    <div class="mask-gift" v-cloak v-if="isShowTips">
        <div class="win-tips">
            <img v-on:click.stop="closeTips" class="pop_close"  src="<?= FE_BASE_URI ?>wap/happy-week/images/pop_close.png" alt="">
            <p class="award-term" v-html="rewardInfo.qishu"></p>
            <p class="prize-rank" v-html="rewardInfo.level"></p>
            <p class="award" v-html="rewardInfo.title"></p>
            <div class="know"  v-on:click.stop="closeTips">我知道了</div>
        </div>
    </div>

    <!--未登录-->
    <div class="mask-gift" v-cloak v-if="isShowLogin">
        <div class="win-tips">
            <img v-on:click.stop="closeLoginTips" class="pop_close"  src="<?= FE_BASE_URI ?>wap/happy-week/images/pop_close.png" alt="">
            <p class="award-term">您还没有登录哦！</p>
            <p class="prize-rank">快去登录点亮扑克牌，</p>
            <p >抽取幸运大奖吧！</p>
            <a class="know"  href="/site/login?next=/promotion/poker/">去登录</a>
        </div>
    </div>
</div>

<script>
    var myScroll;
    $(function() {
        myScroll = new iScroll('wrapper', {
            vScrollbar: false,
            hScrollbar: false
        });
      var mySwiper = new Swiper ('.swiper-container', {
        loop: true,
        autoplay : 3000,
        autoplayDisableOnInteraction : false,
        spaceBetween: 10,
        // 如果需要分页器
        pagination: '.swiper-pagination',

      })
    });

    var data = <?= $data ?>;
    var isLoggedin = $('input[name=isLoggedin]').val()=='false'?false:true;
    var vm = new Vue({
        el: '#weekly-happy',
        data: {
            oneCardUrl:"<?= FE_BASE_URI ?>wap/happy-week/images/"+data.card[0]+".png",
            twoCardUrl:"<?= FE_BASE_URI ?>wap/happy-week/images/r"+data.card[1]+".png",
            threeCardUrl:"<?= FE_BASE_URI ?>wap/happy-week/images/"+data.card[2]+".png",
            fourCardUrl:"<?= FE_BASE_URI ?>wap/happy-week/images/r"+data.card[3]+".png",
            threeCard:data.card[2],
            fourCard:data.card[3],
            rulesIsShow:false,
            isLoggedin:isLoggedin,
            isShowMaskList:false,
            currentQishu:data.qishu,
            linkTo:isLoggedin?'/promotion/poker/history':'javascript:void(0);',
            lottery:data.restSecond<86400?true:false,//离开奖时间小于24小时
            isShowTips:isLoggedin && data.requirePop,//每周一上午十点中奖用户信息提示
            isShowLogin:false,
            isFirstshow:!!data.rewardInfo.rewardCard[0],//往期中奖第一次展示
            isShowRegular:false,
            options:{
                state:"",
                num:"",
                title:"",
                content:"",
                href:"",
                btn:"",
                btnIsShow:false
            },
            qishu:data.rewardInfo.qishu,
            userMobile:data.userMobile,
            rewardInfo:{
                qishu : '恭喜您在'+data.rewardInfo.qishu+'期',
                level : '获得'+data.rewardInfo.level,
                title : data.rewardInfo.title,
                rewardCard:data.rewardInfo.rewardCard,
            }
        },
        methods: {
            showRegular:function(){
                setTimeout(function(){myScroll.refresh();},20);
                this.isShowRegular = !this.isShowRegular;
            },
            regularShow: function () {
                this.rulesIsShow = !this.rulesIsShow;
                setTimeout(function(){myScroll.refresh();},20)
            },
            goLogin:function(){
                if(!this.isLoggedin){
                    this.isShowLogin = !this.isShowLogin;
                }
            },
            closeLoginTips:function(){
                this.isShowLogin = !this.isShowLogin;
            },
            isFirstShow:function(){
                this.options.state = "<?= FE_BASE_URI ?>wap/happy-week/images/bg_01.png";
                this.options.title = "幸运号码";
                this.options.content = "您本期的个人专属幸运号码是"+data.card[0]+"。每周一上午10点记得来开奖哦！";
                this.options.num = this.oneCardUrl;
                this.options.btn = '';
                this.options.btnIsShow = false;
                this.isShowMaskList = !this.isShowMaskList;
            },
            remake: function (event,state) {
                var state = $(event.target).data('state') || state;
                switch (state) {
                    case 'state1':
                        this.options.state = "<?= FE_BASE_URI ?>wap/happy-week/images/bg_01.png";
                        this.options.title = "幸运号码";
                        if (this.isLoggedin == false) {
                            this.options.content = "登录平台即可获得本期个人专属幸运号码。有幸运号码就有机会获得幸运奖哦！";
                            this.options.num = "<?= FE_BASE_URI ?>wap/happy-week/images/doNot.png";
                            this.options.btn = '去登录';
                            this.options.href = '/site/login?next=/promotion/poker/';
                            this.options.btnIsShow = true;
                        } else if (this.isLoggedin == true) {
                            this.options.content = "您本期的个人专属幸运号码是"+data.card[0]+"。每周一上午10点记得来开奖哦！";
                            this.options.num = this.oneCardUrl;
                            this.options.btn = '';
                            this.options.btnIsShow = false;
                        }
                        this.isShowMaskList = !this.isShowMaskList;
                        break;
                    case 'state2':
                        this.options.state = "<?= FE_BASE_URI ?>wap/happy-week/images/bg_02.png";
                        this.options.title = "登录";
                        this.options.content = "登录平台即可点亮本期红桃扑克牌。点亮4张扑克可参与本期抽奖。";
                        if (this.isLoggedin == false) {
                            this.options.num = "<?= FE_BASE_URI ?>wap/happy-week/images/rdoNot.png";
                            this.options.btn = '去登录';
                            this.options.href = '/site/login?next=/promotion/poker/';
                            this.options.btnIsShow = true;
                        } else if (this.isLoggedin == true) {
                            this.options.num = this.twoCardUrl;
                            this.options.btn = '';
                            this.options.btnIsShow = false;
                        }
                        this.isShowMaskList = !this.isShowMaskList;
                        break;
                    case 'state3':
                        this.options.state = "<?= FE_BASE_URI ?>wap/happy-week/images/bg_03.png";
                        this.options.title = "签到";
                        this.options.content = "完成签到即可点亮本期梅花扑克牌。点亮4张扑克可参与本期抽奖。";
                        this.options.btnIsShow = true;
                        this.options.btn = '去签到';
                        this.options.href = "/user/checkin";
                        if (this.isLoggedin == false) {
                            this.options.num = "<?= FE_BASE_URI ?>wap/happy-week/images/doNot.png";
                        } else if (this.isLoggedin == true) {
                            if(!this.threeCard){
                                this.options.num = "<?= FE_BASE_URI ?>wap/happy-week/images/doNot.png";
                            } else {
                                this.options.num = this.threeCardUrl;
                            }
                        }
                        this.isShowMaskList = !this.isShowMaskList;
                        break;
                    case 'state4':
                        this.options.state = "<?= FE_BASE_URI ?>wap/happy-week/images/bg_04.png";
                        this.options.title = "投资理财";
                        this.options.content = "任意投资即可点亮本期方片扑克牌。点亮4张扑克可参与本期抽奖。";
                        this.options.btnIsShow = true;
                        this.options.btn = '去投资';
                        this.options.href = "/deal/deal/index";
                        if (this.isLoggedin == false) {
                            this.options.num = "<?= FE_BASE_URI ?>wap/happy-week/images/rdoNot.png";
                        } else if (this.isLoggedin == true) {
                            if(!this.fourCard){
                                this.options.num = "<?= FE_BASE_URI ?>wap/happy-week/images/rdoNot.png";
                            } else {
                                this.options.num = this.fourCardUrl;
                            }
                        }
                        this.isShowMaskList = !this.isShowMaskList;
                        break;
                    default:
                        return;
                }

            },
            closePopover: function () {
                this.isShowMaskList = !this.isShowMaskList;
            },
            closeTips: function(){
                this.isShowTips = !this.isShowTips;
                this.isTouchMove(this.isShowTips);
            },
            leftTimer: function (timer) {
                var hours = parseInt(timer / 60 / 60 % 24, 10); //计算剩余的小时
                var minutes = parseInt(timer / 60 % 60, 10);//计算剩余的分钟
                var seconds = parseInt(timer % 60, 10);//计算剩余的秒数
                hours = this.checkTime(hours);
                minutes = this.checkTime(minutes);
                seconds = this.checkTime(seconds);
                document.getElementById("timer").innerHTML = '<i>'+hours+'</i>:<i>'+minutes+'</i>:<i>'+seconds+'</i>';
            },
            checkTime: function (i){ //将0-9的数字前面加上0，例1变为01
                if (i < 10) {
                    i = "0" + i;
                }
                return i;
            },
            eventTarget : function (event) {
                var event = event || window.event;
                event.preventDefault();
            },
            isTouchMove: function(val){
                if(val===true){
                    $('body').on('touchmove',this.eventTarget,false);
                } else {
                    $('body').off('touchmove');
                }
            }
        },
        watch:{
            isShowMaskList:function(val,oldVal){
                this.isTouchMove(val);
            },
            isShowLogin:function(val,oldVal){
                this.isTouchMove(val);
            }
        }
    });
    vm.isTouchMove(vm.isShowTips);
    if(data.isFirstLogin && data.requirePop == false){
        vm.isFirstShow();
    }

    /*倒计时*/
    (function(timers){
        if(timers<86400){
            //调用倒计时方法
            var timer = timers ;//剩余时间
            var timerOrder = setInterval(function () {
                vm.leftTimer(timer);
                if(timer<0){
                    vm.lottery = false;
                    clearInterval(timerOrder);
                    if(location.href.indexOf('?') == -1){
                        window.location.href = window.location.href +"?v="+ Math.random()*10;
                    } else {
                        window.location.href = window.location.href +"&v="+ Math.random()*10;
                    }
                }
                timer--;
            }, 1000);
        }
    })(data.restSecond);
</script>
<script src="<?= FE_BASE_URI ?>/libs/swiper/swiper-3.4.2.min.js"></script>
