<?php

$this->title = '门票好礼三重奏';
?>

<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180111/css/index.css?v=1.27">
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/iscroll.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<!--<script src="--><?//= FE_BASE_URI ?><!--libs/axios.min.js"></script>-->
<script src="<?= FE_BASE_URI ?>libs/handlebars-v4.0.10.js"></script>
<style>
    [v-cloak] {
        display: none
    }
</style>
<div class="flex-contant" ref="flexContent" id="app">
    <div class="contain-part1">
        <div class="top-nav"></div>
        <div class="part1-msg">
            <p>活动期间累计年化出借：</p>
            <p>排行第1名，送价值<span>1680</span>元门票<i>2</i>张；</p>
            <p>排行第2名至第5名，送价值<span>880</span>元门票<i>2</i>张；</p>
            <p>排行第6名至第15名，送价值<span>680</span>元门票<i>2</i>张；</p>
        </div>
        <a @click="goInvite" class="go-invest go-invest1">去出借</a>
        <p class="part1-suggestion-p">累计年化出借额相同时，最先达到该金额的排在前面</p>
        <?php if (!empty($rankingList)) { ?>
        <div class="pop-ranking">
            <div class="pop-ranking-nav"></div>
            <div class="pop-ranking-contain">
                <dl>
                    <dt class="clearfix">
                        <span class="fl">用户ID</span>
                        <span class="fr">实时年化出借额(元)</span>
                    </dt>
                    <?php foreach ($rankingList as $k => $ranking) { ?>
                        <?php $num = $k + 1; ?>
                        <?php
                            if (1 === $num) {
                                $colorClass = 'pop-ranking-red';
                            } elseif ($num >= 2 && $num <= 5) {
                                $colorClass = 'pop-ranking-yellow';
                            } else {
                                $colorClass = 'pop-ranking-orange';
                            }
                        ?>
                        <dd class="clearfix <?= $colorClass ?>">
                            <?php if ($currentMobile === $ranking['mobile']) { ?>
                                <span class="fl phone-card"></span>
                            <?php } else { ?>
                                <span class="fl"></span>
                            <?php } ?>
                            <span class="fl"><?= $ranking['subMobile'] ?></span>
                            <span class="fr"><?= bcsub($ranking['annual'], 0, 2) ?></span>
                        </dd>
                    <?php } ?>
                </dl>
            </div>
            <div class="pop-ranking-bottom"></div>
        </div>
        <?php } ?>
        <p class="part1-suggestion">注：排行榜数据每5分钟更新一次。</p>
    </div>
    <div class="contain-part2">
        <div class="top-nav"></div>
        <p class="part2-msg">活动期间年化出借每累计<span>5万</span>，获得1次抽奖机会</p>
        <div class="clearfix fr-mygift">
            <span @click="showPrizeList" class="fr part-mygift">我的奖品>>
            </span>
        </div>
        <div class="rotate-prize">
            <div class="get-prize-bg">
                <div class="get-prize-rotate" ref="rotateBox">
                </div>
                <img @click="getPrizeButton" src="<?= FE_BASE_URI ?>wap/campaigns/active20180111/images/get-prize.png" alt="">
            </div>
        </div>
        <p>剩余次数：<span v-cloak>{{msg}}</span>次</p>
        <a @click="goInvite" class="go-invest">去出借</a>
    </div>
<!--     为登陆框-->
    <div v-cloak :class="{'hide-prize':noLogin}" class="goto-login">

        <div class="box-top-mid">
            <div @click="closeList" class="cue-close">
            </div>
            <p class="go-login-title">注册有礼</p>
            <div class="box-cover">
                <p class="no-login-msg">活动期间（1月15日00:00-1月19日12:00）完成注册实名，即可抽取<span style="color:red">8位</span>幸运用户获得<span style="color:red;">680元门票</span>大奖！</p>
                <p class="have-login-msg">已有账号?<a href="/site/login" class="have-login-msg-a">登录</a></p>
                <a href="/site/signup" class="go-invite">注册抽门票</a>
            </div>
        </div>
    </div>
    <div v-cloak :class="{'hide-prize':getPrize}" class="get-prizes">
        <div class="box-top-mid">
            <div @click="closeList" class="cue-close">
            </div>
            <p class="get-prize-msg">恭喜您获得了</p>
            <p class="get-prize-msg-word" v-clock>{{prizeKind}}</p>
            <div class="get-prize-kind">
                <img :src="prizeKindUrl" alt="奖品">
            </div>
            <a @click="closeList" class="prize-button">收下礼品</a>
        </div>
    </div>
    <div v-cloak :class="{'hide-prize':getPrizeList}" class="get-prizes-list">
        <div class="box-top-mid">
            <div @click="closeList" class="cue-close">
            </div>
            <p class="get-prize-msg">奖品列表</p>
            <div id="wrapper">
                <ul id="templateContant">
<!--                    <li v-for="(item, i) in PrizeLists" :key="i">-->
<!--                        <div class="fl prize-list-one">-->-->
<!--                            <img alt="奖品">-->
<!--                        </div>-->
<!--                        <div class="fr prize-list-one-msg">-->
<!--                            <span></span>-->
<!--                            <span>中奖时间2017年8月10日</span>-->
<!--                        </div>-->
<!--                    </li>-->

<!--                    <li>-->
<!--                        <div class="fl prize-list-one">-->
<!--                            <img src="--><?//= FE_BASE_URI ?><!--wap/campaigns/active20180111/images/prize-kind/supper-card.png" alt="奖品">-->
<!--                        </div>-->
<!--                        <div class="fr prize-list-one-msg">-->
<!--                            <span>50元超市卡</span>-->
<!--                            <span>中奖时间2017年8月10日</span>-->
<!--                        </div>-->
<!--                    </li>-->
                </ul>
            </div>
        </div>
    </div>
    <div class="contain-part3">
        <div class="top-nav"></div>
        <p>活动开启后，将选取<span>8位</span>注册手机号后4位最接近1月19日15:00收盘时上证指数后4位的新用户（完成<i class="name-red">注册实名</i>的时间为1月15日00:00-1月19日12:00），各获得价值680元门票1张！</p>
        <p>例：1月19日当天上证指数1234.56，则注册手机号后4位数的大小最接近3456的<span>8位</span>用户为幸运用户。</p>
        <?php if (empty($registerMobileList)) { ?>
            <div class="active-state-before">
                <div v-clock v-if="loginRegister" class="active-state clearfix">
                    <div class="fl go-register"><a href="/site/signup">去注册</a></div>
                    <div class="fr go-register"><a @click="invistFirend">邀请好友参加</a></div>
                </div>
                <a v-clock v-else @click="invistFirend" class="go-invest">邀请好友参加</a>
                <p class="last-rink-msg">注：中奖名单将于1月19日17:00在本页面公布。</p>
            </div>
        <?php } else { ?>
            <div class="active-state-now">
                <h5>中奖名单：</h5>
                <div class="active-state-phone clearfix">
                    <?php foreach ($registerMobileList as $registerMobile) { ?>
                        <span><?= $registerMobile ?></span>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
    <p class="last-p-tips">本活动最终解释权归楚天财富所有</p>
</div>
<script id="table-template" type="text/x-handlebars-template">
    {{#each this}}
    <li>
    <div class="fl prize-list-one">
        <img src=<?= FE_BASE_URI ?>{{path}} alt="奖品">
        </div>
        <div class="fr prize-list-one-msg">
            <span>{{name}}</span>
            <span>{{awardTime}}</span>
         </div>
    </li>
    {{/each}}
</script>
<script>
    $(function () {
        var promoStatus = $('input[name=promoStatus]').val();
        var isLoggedin = $('input[name=isLoggedin]').val();
        FastClick.attach(document.body);
        initScroll();

        function initScroll() {
            var myScroll;
            intervalTime = setInterval(function () {
                var resultContentH = $("#wrapper ul").height();
                if (resultContentH > 0) {  //判断数据加载完成的条件随便加的
                    clearInterval(intervalTime);
                    myScroll = new iScroll('wrapper', {
                        vScrollbar: false,
                    });
                }
            }, 1);
        };

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
        };
        var myapp = new Vue({
            el: "#app",
            created: function () {
                if (this.isLoggedin == 'true') {
                    this.loginRegister = false;
                } else if (this.isLoggedin == 'false') {
                    this.loginRegister = true;
                }
            },
            mounted: function () {
                if (this.isLoggedin == 'true') {
                    this.noLogin = false;
                } else if (this.isLoggedin == 'false'&&this.promoStatus == 0) {
                    this.noLogin = true;
                    this.$refs.flexContent.addEventListener('touchmove', this.bodyScroll, false);
                }
            },
            data: {
                // 获得的奖品
                prizeKind:'',
                // 奖品链接
                prizeKindUrl:"./images/prize-kind/supper-card.png",
                // 最后5点的状态
                lastPart: true,
                // 活动状态
                promoStatus: promoStatus,

                // 是否登录
                isLoggedin: isLoggedin,
                // 每一个奖品的角度
                rotate: 0,
                // 最终角度
                rotatetotal: 0,
                // 显示获得某一个奖品弹窗
                getPrize: false,
                // 显示获得所有的奖品弹窗
                getPrizeList: false,
                //未登录弹窗
                noLogin:false,
                // 抽奖次数
                msg: '<?= $activeTicketCount ?>',
                //奖品
                PrizeLists:[],
                // 根据登陆状态的值判断下面注册邀请好友相关按钮显示隐藏
                loginRegister: !true,
                // 对于点击判断的一个参照
                flag:true,
                // 前期没有后台数据拿来模拟的一个数值，后期会删掉
                // rote:1,
            },
            methods: {
                // 点击邀请好友
                invistFirend:function(){
                    if (this.isLoggedin == 'true') {
                        location.href='/user/invite';
                    } else if (this.isLoggedin == 'false') {
                        location.href='/site/login';
                    };
                },
                //去出借
                goInvite: function () {
                    if (this.promoStatus == 1) {
                        toastCenter('活动未开始');
                    } else if (this.promoStatus == 2) {
                        toastCenter('活动已结束');
                    } else if (this.promoStatus == 0) {
                        location.href = "/deal/deal/index";
                    }
                },
                // 点击奖品列表展示
                showPrizeList: function () {
                    // this.getPrize=false;
                    if (this.promoStatus == 1) {
                        toastCenter('活动未开始');
                    }  else if (this.promoStatus == 0||this.promoStatus == 2) {
                        var xhr=$.get('/promotion/p180115/award-list?key=promo_180115');
                        $.ajax({
                            url:'/promotion/p180115/award-list',
                            data:{'key':'promo_180115'},
                            dataType:'json',
                            type:'get',
                            success:function(data){
                                //1获取到模板
                                var source= document.getElementById("table-template").innerHTML;
                                var template = Handlebars.compile(source);
                                // 2获取到AJAX取到的数据（这里是模拟的）
                                var context=data;
                                //3将数据填充到模板
                                var htmlTpl=template(context);
                                // 父容器
                                var fatherBox=document.getElementById('templateContant');
                                // 4 将填充好数据的模板放到对应的HTML文件位置
                                fatherBox.innerHTML=htmlTpl;
                                myapp.getPrizeList = true;
                                myapp.$refs.flexContent.addEventListener('touchmove', myapp.bodyScroll, false);
                            },

                            error:function(){
                                toastCenter('网络繁忙，请稍后重试');
                            }
                        });
                    }

                },
                //抽奖
                getPrizeButton: function () {

                        //真正抽奖代码
                         if(this.flag){
                             this.flag=false;
                             var xhr=$.get('/promotion/p180115/draw?key=promo_180115');
                             xhr.done(function(response){
                                 if(response.code === 0){
                                     myapp.msg=myapp.msg-1;
                                     switch (response.ticket.sn) {
                                         case '180115_G50':
                                             myapp.rotate = 22;
                                             break;
                                         case '180115_C36':
                                             myapp.rotate = 67;
                                             break;
                                         case '180115_P66':
                                             myapp.rotate = 112;
                                             break;
                                         case '180115_P88':
                                             myapp.rotate = 157;
                                             break;
                                         case '180115_R3':
                                             myapp.rotate = 202;
                                             break;
                                         case '180115_R5':
                                             myapp.rotate = 247;
                                             break;
                                         case '180115_G680':
                                             myapp.rotate = 292;
                                             break;
                                         case '180115_G880':
                                             myapp.rotate = 337;
                                             break;
                                         default:
                                             break;
                                     };
                                     // console.log(myapp.rotate);
                                     // console.log(response.ticket.sn);
                                     myapp.prizeKind=response.ticket.name;
                                     myapp.prizeKindUrl='<?= FE_BASE_URI ?>'+response.ticket.path;
                                     myapp.rotatetotal = 2160 +  myapp.rotate +(myapp.rotatetotal-(myapp.rotatetotal%2160));
                                     $('.get-prize-rotate').css({
                                         '-webkit-transform':'rotate('+(-myapp.rotatetotal)+'deg)',
                                         '-moz-transform':'rotate('+(-myapp.rotatetotal)+'deg)',
                                         '-ms-transform':'rotate('+(-myapp.rotatetotal)+'deg)',
                                         '-o-transform':'rotate('+(-myapp.rotatetotal)+'deg)',
                                         'transform':'rotate('+(-myapp.rotatetotal)+'deg)',
                                     });
                                     // myapp.$refs.rotateBox.style.-webkit-transform = 'rotate(' +  (-myapp.rotatetotal) + 'deg)';
                                     // myapp.$refs.rotateBox.style.-moz-transform = 'rotate(' +  (-myapp.rotatetotal) + 'deg)';
                                     // myapp.$refs.rotateBox.style.-ms-transform = 'rotate(' +  (-myapp.rotatetotal) + 'deg)';
                                     // myapp.$refs.rotateBox.style.-o-transform = 'rotate(' +  (-myapp.rotatetotal) + 'deg)';
                                     // myapp.$refs.rotateBox.style.transform = 'rotate(' +  (-myapp.rotatetotal) + 'deg)';
                                     // -webkit-transform: translate(-50%,-50%);
                                     // -moz-transform: translate(-50%,-50%);
                                     // -ms-transform: translate(-50%,-50%);
                                     // -o-transform: translate(-50%,-50%);
                                     // transform: translate(-50%,-50%);
                                     myapp.rotate = 0;
                                     myapp.$refs.rotateBox.addEventListener("webkitTransitionEnd",myapp.myTransEnd,false);
                                     myapp.$refs.rotateBox.addEventListener("transitionend",myapp.myTransEnd,false);
                                 };
                             });
                             xhr.fail(function(jqXHR){
                                 myapp.flag=true;
                                 var resp = $.parseJSON(jqXHR.responseText);
                                 if(resp.code===1){
                                     toastCenter("活动未开始");
                                 }else if(resp.code===2){
                                     toastCenter("活动已结束");
                                 }else if(resp.code===3){
                                     toastCenter("未登录");
                                 }else if(resp.code===4){
                                     toastCenter("您还没有抽奖机会");
                                 }else if(resp.code===5){
                                     toastCenter("您还没有抽奖机会");
                                 }else if(resp.code===6){
                                     toastCenter("系统繁忙请刷新重试");
                                 }else if(resp.code===7){
                                     toastCenter("您还没有抽奖机会");
                                 }

                             })
                            /*
                             axios.get('/promotion/p180115/draw?key=promo_180115')
                                 .then(function(response){
                                     console.log(response);
                                     if (response.code == 0) {
                                         switch (response.ticket.sn) {
                                         case '180115_G50':
                                             this.rotate = 22;
                                             break;
                                         case '180115_C36':
                                             this.rotate = 67;
                                             break;
                                         case '180115_P66':
                                             this.rotate = 112;
                                             break;
                                         case '180115_P88':
                                             this.rotate = 157;
                                             break;
                                         case '180115_R3':
                                             this.rotate = 202;
                                             break;
                                         case '180115_R5':
                                             this.rotate = 247;
                                             break;
                                         case '180115_G680':
                                             this.rotate = 292;
                                             break;
                                         case '180115_G880':
                                             this.rotate = 337;
                                             break;
                                         default:
                                             break;
                                         }
                                         this.prizeKind=response.ticket.tit;
                                         this.prizeKindUrl=response.ticket.url;
                                         this.rotatetotal = 2160 + this.rotate +(this.rotatetotal-(this.rotatetotal%2160));
                                         this.$refs.rotateBox.style.transform = 'rotate(' + this.rotatetotal + 'deg)';
                                         this.rotate = 0;
                                         this.$refs.rotateBox.addEventListener("webkitTransitionEnd",this.myTransEnd,false);
                                         this.$refs.rotateBox.addEventListener("transitionend",this.myTransEnd,false);
                                     } else {
                                         toastCenter(response.message);
                                     }
                                 }).catch(
                                     function(err){
                                         myapp.flag=true;
                                         if (1 === err.code) {
                                            alert(err.message);
                                         }
                                     }
                                 );

                                */

                         }


                },
                myTransEnd:function(){
                    this.flag=true;
                    this.getPrize=true;
                    this.$refs.flexContent.addEventListener('touchmove', this.bodyScroll, false);
                },
                // 关闭奖品列表展示
                closeList: function () {
                    this.getPrize = false;
                    this.getPrizeList = false;
                    this.noLogin=false;
                    this.$refs.flexContent.removeEventListener('touchmove', this.bodyScroll, false);
                },
                bodyScroll: function (e) {
                    var e = e || windew.event;
                    e.preventDefault();
                }
            }
        })
    })
</script>
