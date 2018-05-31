<?php

$this->title = '童话故事知多少';
$user = Yii::$app->user->getIdentity();
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180517/css/index.min.css?v=1.7">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/iscroll.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/handlebars-v4.0.10.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<style>
    [v-cloak]{
        display: none;
    }
    .check-books-bottom{
        position: relative;
    }
    div.flex-content .answer-test-page .test-content .top_time_hint {
        margin: 1.06666667rem auto 0;
        width: 8.53333333rem;
    }
    div.flex-content .check-box-father .check-box div.alery-over-book p:first-child{
        font-weight:400;
    }
</style>
<div class="flex-content" ref="flexContent" id="app">
    <!--弹窗1 开始答题时的弹框-->
    <div v-cloak :class="{'block-book' : checkBookFather}" class="check-box-father">
        <div v-if="haveChance" class="check-box">
            <!--<check-book-vue v-cloak :book-kinds="bookKind"></check-book-vue>-->
            <div v-if="notOverBook" class="not-over-book">
                <p>您选的书是</p>
                <p v-cloak>《&nbsp;{{bookKind}}&nbsp;》</p>
            </div>
            <div v-else class="alery-over-book">
                <p v-cloak>《&nbsp;{{bookKind}}&nbsp;》</p>
                <p>再次答题，没有奖励哦！</p>
            </div>
            <!--是否通关-->
            <img v-cloak src="<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/over_book_mark@3x.png" :class="{'block-book':checkOver}" class="overBook" alt="">
            <img class="checkBookImg" onclick="return false;" :src=checkBookUrl alt="">
            <div class="check-btn-box clearfix">
                <span @click="changeBook" class="lf">去换本书</span>
                <span @click="goTest" class="rg">{{startAnswer}}</span>
            </div>
        </div>
        <div v-else class="getChance">
            <div v-if="shareGetChance" class="shareGetChance">
                <p>您没有答题次数了</p>
                <p>分享到朋友圈，还能再玩一次哦！</p>
                <p>提示：活动期间每天都能来答题</p>
            </div>
            <div v-else>
                <p>您今天答题次数已经用完了</p>
                <p>明天再来玩吧！</p>
                <p>提示：活动期间每天都能来答题</p>
            </div>
            <i @click="closeShareChanceBtn"></i>
            <span @click="knowActive" :class="{'share-btn':addShareBtn}">{{shareChanceMsg}}</span>
        </div>
    </div>
    <!--答题结束的弹框-->
    <div :class="{'block-book' : endAnswerFather}" class="end-answer-father">
        <div class="answer-box">
            <div class="true-answer-hint">本轮答对{{testTrueNum}}题</div>
            <p>{{endPrizeMsg1}}</p>
            <p>{{endPrizeMsg2}}</p>
            <i @click="closePrize"></i>
            <img v-if="successOverTest" src="<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/prize_1@3x.png" alt="">
            <div v-if="notOverTest" class="prize-img">
                <img :src="myGiftUrl" alt="">
                <!--                <h5>{{ticketNum}}</h5>-->
            </div>
            <!--book-->
            <u></u>
            <div class="end-answer-btn clearfix">
                <span class="share-btn lf">去分享</span>
                <span @click="closePrize" class="rg">再玩一次</span>
            </div>
        </div>
    </div>
    <!--奖品列表-->
    <div :class="{'block-book':myGift}" class="my-gift">
        <div class="gift-bg">
            <i @click="closeGiftList" class="close-gift"></i>
            <div class="wrapper-father">
                <div id="wrapper">
                    <ul ref="giftContent" class="gift-list">
                        <!--                        <prize-prom :prize-lists="prizeLists"></prize-prom>-->

                        <!--                        <li class="clearfix">-->
                        <!--                            <img class="lf" src="--><?//= FE_BASE_URI ?><!--wap/campaigns/active20180517/images/gift_all@3x.png" alt="">-->
                        <!--                            <div class="gift-right-box">-->
                        <!--                                <p>20元代金券＋20积分</p>-->
                        <!--                                <p>2万元起投</p>-->
                        <!--                                <p>2018-6-2  14:08:51</p>-->
                        <!--                            </div>-->
                        <!--                        </li>-->
                        <!--                        <li class="clearfix">-->
                        <!--                            <img class="lf" src="--><?//= FE_BASE_URI ?><!--wap/campaigns/active20180517/images/gift_all@3x.png" alt="">-->
                        <!--                            <div class="gift-right-box">-->
                        <!--                                <p>20元代金券＋20积分</p>-->
                        <!--                                <p>2万元起投</p>-->
                        <!--                                <p>2018-6-2  14:08:51</p>-->
                        <!--                            </div>-->
                        <!--                        </li>-->
                        <!--                        <li class="clearfix">-->
                        <!--                            <img class="lf" src="--><?//= FE_BASE_URI ?><!--wap/campaigns/active20180517/images/gift_3@3x.png" alt="">-->
                        <!--                            <div class="gift-right-box">-->
                        <!--                                <p>20元代金券＋20积分</p>-->
                        <!--                                <p>2万元起投</p>-->
                        <!--                                <p>2018-6-2  14:08:51</p>-->
                        <!--                            </div>-->
                        <!--                        </li>-->
                        <!--                        <li class="clearfix">-->
                        <!--                            <img class="lf" src="--><?//= FE_BASE_URI ?><!--wap/campaigns/active20180517/images/gift_8@3x.png" alt="">-->
                        <!--                            <div class="gift-right-box">-->
                        <!--                                <p>20元代金券＋20积分</p>-->
                        <!--                                <p>2万元起投</p>-->
                        <!--                                <p>2018-6-2  14:08:51</p>-->
                        <!--                            </div>-->
                        <!--                        </li>-->
                        <!--                        <li class="clearfix">-->
                        <!--                            <img class="lf" src="--><?//= FE_BASE_URI ?><!--wap/campaigns/active20180517/images/gift_8@3x.png" alt="">-->
                        <!--                            <div class="gift-right-box">-->
                        <!--                                <p>20元代金券＋20积分</p>-->
                        <!--                                <p>2万元起投</p>-->
                        <!--                                <p>2018-6-2  14:08:51</p>-->
                        <!--                            </div>-->
                        <!--                        </li>-->
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--选书页-->
    <div v-show="checkBookPage" class="check-book-page">
        <!--banner-->

        <div src="images/banner_bg@2x.png" class="top-banner">
            <div class="active-time">
                <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/active_time@3x.png" alt="">
                <i>2018.06.01-06.07</i>
            </div>
        </div>
        <!--选书区-->
        <div class="check-books-box">
            <span @click="chatPrizeList" class="my-prize-list">我的奖品</span>
            <div class="inner-box"></div>
            <div class="check-books-top">
                <p></p>
            </div>
            <div class="check-books-middle">
                <span @click="checkTestKind(0)" ref="span1"></span>
                <span @click="checkTestKind(1)" ref="span2"></span>
                <span @click="checkTestKind(2)" ref="span3"></span>
                <span @click="checkTestKind(3)" ref="span4"></span>
                <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/over_book_mark@3x.png" :class="{'block-book':blockBook1}" class="over-book1" alt="">
                <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/over_book_mark@3x.png" :class="{'block-book':blockBook2}" class="over-book2" alt="">
                <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/over_book_mark@3x.png" :class="{'block-book':blockBook3}" class="over-book3" alt="">
                <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/over_book_mark@3x.png" :class="{'block-book':blockBook4}" class="over-book4" alt="">
            </div>
            <div class="check-books-bottom"></div>
        </div>
        <div class="active-rules">
            <div class="inner-box"></div>
            <div class="check-books-top">
                <p class="active-rules-title2"></p>
            </div>
            <ol class="active-rules-contain">
                <li>1.活动时间：2018年6月1日至6月7日；</li>
                <li>2.消耗1次答题机会，可以从4本童话书中任选一本进入答题；</li>
                <li>3.每轮答对4~5题，将会通关选中书籍，并获得通关礼包；选择已经通关的书进行答题，将不再获得奖励；</li>
                <li>4.活动期间，每天免费获得1次答题机会；分享活动到朋友圈后，可再次获得1次答题机会；</li>
                <li>5.答题机会每日0点重置，每天记得叫上朋友一起来玩哦！</li>
                <li>6.本次活动获得的奖品将立即发放到账。</li>
            </ol>
            <div class="check-books-bottom">
                <p>本活动最终解释权归温都金服所有</p>
            </div>
        </div>
    </div>
    <!--答题页-->
    <div v-show="!checkBookPage" class="answer-test-page">
        <i answer-top-bg></i>
        <i answer-bottom-bg></i>
        <div class="test-content">
            <div class="top_time_hint">
                <p><u ref="topTimeHint"></u></p>
                <p>答题剩余：<span>{{surplusTime|myFilter}}s</span></p>
            </div>
            <p class="book-test-title">《&nbsp;{{bookKind}}&nbsp;》</p>
            <div class="title-box">
                <p>{{testNum}}.{{questionTitle}}</p>
            </div>
            <ul class="answer-box">
                <li :class="{'active-style':activeStyle1,'true-style':trueStyle1,'error-style':errorStyle1}" @click="selectAnswer" ref="answerBg1">
                    {{answerA}}
                    <img :class="{'true-style':(trueStyle1&&aleryAnswer)}" src="<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/true_answer@3x.png" alt="">
                    <img :class="{'error-style':(errorStyle1&&aleryAnswer)}" src="<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/error_answer@3x.png" alt="">
                    <!--<img :src="btnSrc1" alt="">-->
                    <!--<i>A</i>-->
                    <!--<img :src="answerMarkSrc" alt="">-->
                </li>
                <li :class="{'active-style':activeStyle2,'true-style':trueStyle2,'error-style':errorStyle2}" @click="selectAnswer" ref="answerBg2">
                    {{answerB}}
                    <!--<img :src="btnSrc2" alt="">-->
                    <!--<i>B</i>-->
                    <!--<img :src="answerMarkSrc" alt="">-->
                    <img :class="{'true-style':(trueStyle2&&aleryAnswer)}" src="<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/true_answer@3x.png" alt="">
                    <img :class="{'error-style':(errorStyle2&&aleryAnswer)}" src="<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/error_answer@3x.png" alt="">
                </li>
                <li :class="{'active-style':activeStyle3,'true-style':trueStyle3,'error-style':errorStyle3}" @click="selectAnswer" ref="answerBg3">
                    {{answerC}}
                    <!--<img :src="btnSrc3" alt="">-->
                    <!--<i>C</i>-->
                    <!--<img :src="answerMarkSrc" alt="">-->
                    <img :class="{'true-style':(trueStyle3&&aleryAnswer)}" src="<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/true_answer@3x.png" alt="">
                    <img :class="{'error-style':(errorStyle3&&aleryAnswer)}" src="<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/error_answer@3x.png" alt="">
                </li>
            </ul>
            <p ref="submitContent" @click="submitEnd" class="submitAnswer">{{submitWord}}</p>
            <span>第{{testNum}}题/共5题</span>
        </div>
        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/answer_test_book.png" class="bottom-books">
    </div>
</div>
<script type="text/template" id="giftHtmlTemplate">
    {{#each this}}
    <li class="clearfix">
        <img class="lf" src="<?= FE_BASE_URI ?>{{path}}" alt="">
        <div class="gift-right-box">
            <p>{{name}}</p>
            <p>满{{minInvest}}元可用</p>
            <p>{{awardTime}}</p>
        </div>
    </li>
    {{/each}}
</script>
<!--<script type="text/x-template" id="prizeTemplate">-->
<!---->
<!--        <li v-cloak v-for="(ele,index) in prizeLists" :key="index" class="clearfix">-->
<!--            <img class="lf" src="{{ele.path}}" alt="">-->
<!--            <div class="gift-right-box">-->
<!--                <p>{{ele.name}}</p>-->
<!--                <p>{{ele.minInvest|myFilter2}}万元起投</p>-->
<!--                <p>{{ele.awardTime}}</p>-->
<!--            </div>-->
<!--        </li>-->
<!---->
<!--</script>-->
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?= FE_BASE_URI ?>libs/wxShare.js?v=3.0"></script>
<script>
    dataJson&&(dataJson=dataJson);
    console.log(dataJson);
    $(function(){
        var timer=null;
        FastClick.attach(document.body);
        // var checkBookVue={
        //     template:`<div>
        //                     <p>您选的书是</p>
        //                     <p v-cloak>《{{bookKinds}}》</p>
        //              </div>`,
        //     // data:function(){
        //     //     return{
        //     //
        //     //     }
        //     // },
        //     props:['bookKinds'],
        // };
        var prizeTemplateList={
            template:"#prizeTemplate",
            props:['prizeLists'],
        };
        var myApp=new Vue({
            el:"#app",
            created:function(){
                // this.shareGetChance=false;
                // this.cutDown();
                // this.btnSrc3="./images/check_active_stack@3x.png";
                // console.log(this.checkBookPage);

                //分享内容设置
                wxShare.setParams("测测你有没有“童年”？顺便给孩子讲个童话故事吧！", "点击链接，马上参与~", "<?= Yii::$app->params['clientOption']['host']['wap'] ?>/promotion/p180601/index?code=<?= $user !== null ? $user->usercode : '' ?>", "https://static.wenjf.com/upload/link/link1527583700481069.png", "<?= Yii::$app->params['weixin']['appId'] ?>", "/promotion/p180601/add-share");
                wxShare.TimelineSuccessCallBack = function () {
                    $.get("/promotion/p180601/add-share?scene=timeline&shareUrl=" + encodeURIComponent(location.href))
                };
            },
            mounted:function(){
                this.$refs.submitContent.style.background="url(<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/not_click@3x.png) 0 0 no-repeat/100% 100%";
                this.$refs.submitContent.style.color="#989794";
            },
            data:{
                startAnswer:"开始答题",
                csrf:dataJson.csrf,
                // 登陆状态
                isLoggedIn:dataJson.isLoggedIn,
                // 活动状态
                promoStatus:dataJson.promoStatus,
                // 奖品
                prizeLists:'',
                // promations:dataJson,
                // 我的奖品弹窗
                myGift:false,
                // 未通关的奖品图
                myGiftUrl:'',
                //1234本书 是否通关的标志
                blockBook1:dataJson.result.GLTales.isPassed,
                blockBook2:dataJson.result.andersonTales.isPassed,
                blockBook3:dataJson.result.aesopFables.isPassed,
                blockBook4:dataJson.result.chineseFairyTales.isPassed,
                // 答题状态
                nowAnswerStates:'',
                // 选项的传值
                nowAnswerData:'',
                // 第几本书
                nowIndex:'',
                // 点击完未响应，不让点
                nowFlag:true,
                // 选书的时候的弹框
                checkBookFather:false,
                // 选择的书的内容
                bookKind:'格林童话',
                // 选择的书的图标
                checkBookUrl:'<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/check_book1@3x.png',
                // 是否有机会
                haveChance:true,
                // 是否已通关显示的弹窗内容
                notOverBook:true,
                // 该书是否答题通关 图标
                checkOver:false,
                // 可以通过分享获取游戏次数
                shareGetChance:true,
                // 获取次数的提示按钮
                shareChanceMsg:'立即分享',
                // 添加分享功能的按钮
                addShareBtn:true,
                // 是否是选书页  答题页
                checkBookPage:true,
                // 问题
                questionTitle:'',
                // 倒计时
                surplusTime:20,
                // 倒计时计数点
                nums:0,
                // 回答那本书  eg:格林童话 即bookKind
                // checkKindTest:"",
                // 问题
                questions:'',
                // btnSrc1:"./images/not_check_stack@3x.png",
                // btnSrc2:"./images/not_check_stack@3x.png",
                // btnSrc3:"./images/not_check_stack@3x.png",
                // // 正确还是错误
                // answerMarkSrc:"./images/true_answer@3x.png",
                // 答题时是提交答案还是查看结果
                submitWord:'提交答案',
                // 第几题
                testNum:'1',
                // 答案内容
                answerA:'',
                answerB:'',
                answerC:'',
                // 3个答案的状态
                activeStyle1:false,
                trueStyle1:false,
                errorStyle1:false,
                activeStyle2:false,
                trueStyle2:false,
                errorStyle2:false,
                activeStyle3:false,
                trueStyle3:false,
                errorStyle3:false,
                // 是否已经答题
                aleryAnswer:false,
                // 选项是否可以点击
                mayClick:true,
                // 选项已经答了
                clickYet:false,
                // 答案
                endAnswer:'',
                //正确答案
                trueAnswer:'',
                //所有答案
                answerSheet:{},
                // 答题结束的弹窗
                endAnswerFather:false,
                // 答对的题数
                testTrueNum:0,
                //最终是否通关
                endCheckOver:false,
                // 成功通关的奖品图片
                successOverTest:false,
                // 未通关显示的奖品图片
                notOverTest:false,
                // 奖励值
                ticketNum:'',
                // 最终结果提示
                endPrizeMsg1:'',
                endPrizeMsg2:'',
            },
            methods:{
                //查看奖品列表
                chatPrizeList:function(){
                    var that=this;
                    if(this.nowFlag){
                        this.nowFlag=false;
                        switch(this.promoStatus){
                            case 1:
                                this.toastCenter('活动未开始');
                                this.nowFlag=true;
                                break;
                            default:
                                if(this.isLoggedIn==false){
                                    location.href='/site/login';
                                }else{
                                    $.ajax({
                                        type:"get",
                                        url:"/promotion/p180601/award-list",
                                        dataType:'json',
                                        success:function(data){

                                            // that.prizeLists=JSON.parse(that.prizeLists);
                                            if(data.length>0){
//                                                that.prizeLists=data;
//                                                for(var i=0;i<that.prizeLists.length;i++){
//                                                    if(that.prizeLists[i]["ref_amount"]==20){
//                                                        that.prizeLists[i].name="20元代金券+20积分";
//                                                    }
//                                                };

                                                var source= document.getElementById("giftHtmlTemplate").innerHTML;
                                                var template = Handlebars.compile(source);
                                                for(var j=0;j<data.length;j++){
                                                    if(data[j].minInvest>10000){
                                                        data[j].minInvest=data[j].minInvest/10000+"万";
                                                    }else{
                                                        data[j].minInvest=data[j].minInvest-0;
                                                    }
                                                    if(data[j]["ref_amount"]==20){
                                                        data[j]["name"]="20元代金券+20积分";
                                                    }
                                                };
                                                var context=data;
                                                var htmlTpl=template(context);
                                                var fatherBox=document.getElementsByClassName("gift-list")[0];
                                                fatherBox.innerHTML=htmlTpl;

                                                that.$refs.flexContent.addEventListener('touchmove',that.bodyScroll,false);
                                                setTimeout(function(){
                                                    var myScroll = new iScroll('wrapper',{
                                                        vScrollbar:false,
                                                        hScrollbar:false,
                                                        // vScroll:true,
                                                        // checkDOMChanges:true,
                                                        // bounce:true,
                                                    });
                                                },500);
                                            }else{
                                                that.$refs.flexContent.addEventListener('touchmove',that.bodyScroll,false);
                                            }
                                            that.myGift=true;
                                            that.nowFlag=true;
                                        },
                                        error:function(jqXHR){
                                            var res=$.parseJSON(jqXHR.responseText);
                                            switch(res.code){
                                                case 1:
                                                    that.toastCenter('活动未开始');
                                                    break;
                                                case 3:
                                                    that.toastCenter('未登录',function(){location.href="/site/login"});
                                                    break;
                                            }
                                            that.nowFlag=true;
                                        }
                                    })
                                }
                                break;
                        }
                    }
                },
                // 关闭奖品列表
                closeGiftList:function(){
                    this.$refs.flexContent.removeEventListener('touchmove',this.bodyScroll,false);
                    this.myGift=false;
                },
                // 选择某本书回答
                checkTestKind: function(t){
                    this.nowIndex=t;
                    var that=this;
                    switch(this.promoStatus){
                        case 0:
                            if(this.isLoggedIn==false){
                                location.href='/site/login';
                            }else{
                                if(this.nowFlag){
                                    this.nowFlag=false;
                                    switch(this.nowIndex){
                                        case 0:
                                            this.nowAnswerData="GLTales";
                                            this.bookKind="格林童话";
                                            this.checkBookUrl='<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/check_book1@3x.png';
                                            break;
                                        case 1:
                                            this.nowAnswerData='andersonTales';
                                            this.bookKind="安徒生童话";
                                            this.checkBookUrl='<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/check_book2@3x.png';
                                            break;
                                        case 2:
                                            this.nowAnswerData='aesopFables';
                                            this.bookKind="伊索寓言";
                                            this.checkBookUrl='<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/check_book4@3x.png';
                                            break;
                                        case 3:
                                            this.nowAnswerData="chineseFairyTales";
                                            this.bookKind="中国经典童话";
                                            this.checkBookUrl='<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/check_book3@3x.png';
                                            break;
                                    };
                                    $.ajax({
                                        type:'get',
                                        url:'/promotion/p180601/begin',
                                        data:{"sn":that.nowAnswerData},
                                        dataType:"json",
                                        success:function(data){
                                            if(data.code==0){
                                                //dailyAnswerCount= 0 1 2  对应 还没答过题    需要分享   没有次数啦
                                                // if(data.dailyAnswerCount==0){
                                                //     if(!data.isPassed){
                                                //         // 是否已通关显示的弹窗内容
                                                //         that.notOverBook=true;
                                                //         // 该书是否答题通关 图标
                                                //         that.checkOver=false;
                                                //     }else{
                                                //         // 是否已通关显示的弹窗内容
                                                //         that.notOverBook=false;
                                                //         // 该书是否答题通关 图标
                                                //         that.checkOver=true;
                                                //     };
                                                //     that.questions=data.options;
                                                //     that.haveChance=true;
                                                //     that.checkBookFather=true;
                                                // }else if(data.dailyAnswerCount==1){
                                                //     that.shareGetChance=true;
                                                //     that.haveChance=false;
                                                //     that.checkBookFather=true;
                                                // }else if(data.dailyAnswerCount==2){
                                                //     that.shareGetChance=false;
                                                //     that.haveChance=false;
                                                //     that.checkBookFather=true;
                                                // }

                                                if(!data.isPassed){
                                                    // 是否已通关显示的弹窗内容
                                                    that.notOverBook=true;
                                                    // 该书是否答题通关 图标
                                                    that.checkOver=false;
                                                    that.startAnswer="开始答题"
                                                }else{
                                                    // 是否已通关显示的弹窗内容
                                                    that.notOverBook=false;
                                                    // 该书是否答题通关 图标
                                                    that.checkOver=true;
                                                    that.startAnswer="就选这本"
                                                };
                                                that.questions=data.questions;
                                                that.haveChance=true;
                                                that.checkBookFather=true;
                                                that.$refs.flexContent.addEventListener('touchmove',that.bodyScroll,false);
                                            }
                                            that.nowFlag=true;
                                        },
                                        error:function(jqXHR){
                                            var res=$.parseJSON(jqXHR.responseText);
                                            switch(res.code){
                                                case 1:
                                                    that.toastCenter("活动未开始");
                                                    break;
                                                case 2:
                                                    that.toastCenter("活动已结束");
                                                    break;
                                                case 3:
                                                    that.toastCenter("未登录",function(){location.href="/site/login"});
                                                    break;
                                                // 分享以后可再次答题
                                                case 4:
                                                    that.shareGetChance=true;
                                                    that.haveChance=false;
                                                    that.checkBookFather=true;
                                                    that.$refs.flexContent.addEventListener('touchmove',that.bodyScroll,false);
                                                    break;
                                                // 答题机会已用完
                                                case 5:
                                                    that.shareGetChance=false;
                                                    that.haveChance=false;
                                                    that.checkBookFather=true;
                                                    that.$refs.flexContent.addEventListener('touchmove',that.bodyScroll,false);
                                                    break;
                                                // 已通关
                                                case 7:
                                                    // 是否已通关显示的弹窗内容
                                                    that.notOverBook=false;
                                                    // 该书是否答题通关 图标
                                                    that.checkOver=true;
                                                    that.questions=data.options;
                                                    that.haveChance=true;
                                                    that.checkBookFather=true;
                                                    that.$refs.flexContent.addEventListener('touchmove',that.bodyScroll,false);q
                                            }
                                            that.nowFlag=true;
                                        },
                                    })
                                }
                            }
                            break;
                        case 1:
                            this.toastCenter('活动未开始');
                            break;
                        case 2:
                            this.toastCenter('活动已结束');
                            break;
                    }
                },
                // 去换本书
                changeBook: function(){
                    this.$refs.flexContent.removeEventListener('touchmove',this.bodyScroll,false);
                    this.checkBookFather=false;
                },
                // 开始答题
                goTest: function(){
                    // 题目
                    this.questionTitle=this.questions[0].title;
                    // 答案
                    // this.answerA=this.questions[0].A;
                    // this.answerB=this.questions[0].B;
                    // this.answerC=this.questions[0].C;
                    // 将不规范格式的答案存在一个数组内
                    var array=[];
                    for(var key in this.questions[0].options){
                        array.push(this.questions[0].options[key]);
                    }

                    // 遍历 从第二个字符串位置开始截取
                    this.answerA=array[0].substring(2);

                    this.answerB=array[1].substring(2);
                    this.answerC=array[2].substring(2);

                    this.$refs.flexContent.removeEventListener('touchmove',this.bodyScroll,false);
                    this.checkBookFather=false;
                    this.answerNormoal();
                    this.cutDown();
                    this.checkBookPage=false;
                    var theTimeout=setTimeout(function(){ $('body,html').animate({scrollTop:0},100); clearTimeout(theTimeout)},20);
                },
                // 选择答案
                selectAnswer:function(e){
                    var e=e||window.event;
                    if(this.mayClick){
                        this.answerNormoal();
                        this.aleryAnswer=true;
                        this.clickYet=true;
                        if(e.target==this.$refs.answerBg1){
                            this.activeStyle1=true;
                            this.endAnswer="A";
                        }else if(e.target==this.$refs.answerBg2){
                            this.activeStyle2=true;
                            this.endAnswer="B";
                        }else if(e.target==this.$refs.answerBg3){
                            this.activeStyle3=true;
                            this.endAnswer="C";
                        }
                    }else{
                        return false;
                    }
                },
                // 正确答案
                trueStyle:function(){
                    this.answerNormoal();
                    this.aleryAnswer=true;
                    if(this.trueAnswer=="A"){
                        this.trueStyle1=true;
                    }else if(this.trueAnswer=="B"){
                        this.trueStyle2=true;
                    }else if(this.trueAnswer=="C"){
                        this.trueStyle3=true;
                    }
                },
                // 有错误答案
                haveErrorStyle:function(){
                    this.answerNormoal();
                    this.aleryAnswer=true;
                    if(this.endAnswer=='A'){
                        this.errorStyle1=true;
                    }else if(this.endAnswer=='B'){
                        this.errorStyle2=true;
                    }else if(this.endAnswer=='C'){
                        this.errorStyle3=true;
                    };
                    if(this.trueAnswer=="A"){
                        if(this.endAnswer==''){
                            this.aleryAnswer=false;
                            this.errorStyle2=true;
                            this.errorStyle3=true;
                        }
                        this.trueStyle1=true;
                    }else if(this.trueAnswer=="B"){
                        if(this.endAnswer==''){
                            this.aleryAnswer=false;
                            this.errorStyle1=true;
                            this.errorStyle3=true;
                        }
                        this.trueStyle2=true;
                    }else if(this.trueAnswer=="C"){
                        if(this.endAnswer==''){
                            this.aleryAnswer=false;
                            this.errorStyle1=true;
                            this.errorStyle2=true;
                        }
                        this.trueStyle3=true;
                    };
                },
                //默认状态
                answerNormoal:function(){
                    this.activeStyle1=false;
                    this.trueStyle1=false;
                    this.errorStyle1=false;
                    this.activeStyle2=false;
                    this.trueStyle2=false;
                    this.errorStyle2=false;
                    this.activeStyle3=false;
                    this.trueStyle3=false;
                    this.errorStyle3=false;
                    this.aleryAnswer=false;
                },
                // 关闭分享提示窗口按钮
                closeShareChanceBtn:function(){
                    this.$refs.flexContent.removeEventListener('touchmove',this.bodyScroll,false);
                    this.checkBookFather=false;
                },
                // 我知道了
                knowActive:function(){
                    if(this.shareChanceMsg=="我知道了"){
                        this.$refs.flexContent.removeEventListener('touchmove',this.bodyScroll,false);
                        this.checkBookFather=false;
                    }else{
                        return false;
                    }
                },
                // 关闭奖励列表
                closePrize:function(){
                    this.$refs.flexContent.removeEventListener('touchmove',this.bodyScroll,false);
                    this.endAnswerFather=false;
                    this.checkBookPage=true;
                    this.endAnswer='';
                    this.trueAnswer='';
                    this.testNum=1;
                    this.submitWord='提交答案';
                    this.testTrueNum=0;
                    this.clickYet=false;
                    this.mayClick=true;
                },
                // 提交答案还是查看结果
                submitEnd:function(){
                    var that=this;
                    if(this.submitWord=='提交答案'){
                        if(this.nowFlag&&this.clickYet){
                            this.nowFlag=false;
                            this.mayClick=false;
                            var qid=that.questions[that.testNum-1].id;
                            // 提交答案的参数
                            var datas={"qid":qid,"opt":that.endAnswer};
                            // 查看结果的问题答案参数
                            that.answerSheet[qid]=that.endAnswer;
                            clearInterval(timer);
                            $.ajax({
                                url:'/promotion/p180601/answer',
                                type:'get',
                                data:datas,
                                dataType:'json',
                                success:function(data){
                                    if(data.code==0){
                                        that.trueAnswer=data.rightAnswer;

                                        // 显示正确错误结果
                                        if(that.endAnswer==that.trueAnswer){
                                            that.trueStyle();
                                            that.testTrueNum++;
                                        }else{
                                            that.haveErrorStyle();
                                        };
                                        //下面的按钮变化
                                        if(that.testNum==5){
                                            that.submitWord="查看结果"
                                        }else{
                                            that.submitWord="下一题";
                                        }
                                    }
                                    that.nowFlag=true;
                                },
                                error:function(jqXHR){
                                    var res=$.parseJSON(jqXHR.responseText);
                                    that.nowFlag=true;
                                    switch (res.code){
                                        case 1:
                                            that.toastCenter('活动未开始');
                                            break;
                                        case 2:
                                            that.toastCenter("活动已结束");
                                            break;
                                        case 3:
                                            that.toastCenter("未登录",function(){location.href='/site/login'});
                                            break;
                                    }
                                }
                            })
                        }
                    }else if(this.submitWord=="下一题"){
                        // 所有状态置空
                        this.answerNormoal();
                        this.mayClick=true;
                        this.trueAnswer='';
                        this.endAnswer='';
                        this.submitWord="提交答案";
                        this.testNum++;
                        this.clickYet=false;
                        // 题目和答案更换
                        // this.questionTitle=this.questions[this.testNum-1].title;
                        // this.answerA=this.questions[this.testNum-1].A;
                        // this.answerB=this.questions[this.testNum-1].B;
                        // this.answerC=this.questions[this.testNum-1].C;

                        this.questionTitle=this.questions[this.testNum-1].title;
                        // 将不规范格式的答案存在一个数组内
                        var arr=[];
                        for(var key in this.questions[this.testNum-1].options){
                            arr.push(this.questions[this.testNum-1].options[key]);
                        }
                        console.log(arr);
                        // 遍历 从第二个字符串位置开始截取
                        this.answerA=arr[0].substring(2);
                        this.answerB=arr[1].substring(2);
                        this.answerC=arr[2].substring(2);
                        // 倒计时
                        this.cutDown();
                    }else if(this.submitWord=="查看结果"){
                        // endAnswerFather:false,
                        //     // 答对的题数
                        //     testTrueNum:'',
                        this.mayClick=true;
                        if(this.nowFlag){
//                            console.log(that.answerSheet);
                            this.nowFlag=false;
                            $.ajax({
                                url:'/promotion/p180601/open',
                                type:'post',
                                data:{"sn":that.nowAnswerData,"_csrf":that.csrf,"results":that.answerSheet},
                                dataType:'json',
                                success:function(data){
                                    // //最终是否通关
                                    // endCheckOver:false,
                                    //     // 成功通关的图片
                                    //     successOverTest:false,
                                    //     // 未通关的图片
                                    //     notOverTest:false,
                                    // if(that.checkOver==true){
                                    //     that.endPrizeMsg1="您已领取过本书的通关礼包";
                                    //     that.endPrizeMsg2="答题领取其他书的红包";
                                    // }else{
                                    //
                                    // }
                                    if(data.code==0){
                                        that.endCheckOver=data.isPassed;
                                        if(that.endCheckOver){
                                            that.endPrizeMsg1="恭喜您已通关，获得通关礼包";
                                            that.endPrizeMsg2="20元代金券+20积分";
                                            that["blockBook"+((that.nowIndex)+1)]=true;
                                            that.notOverTest=false;
                                            that.successOverTest=true;
                                        }else{
                                            // 未通关 有奖  和 无奖
//                                            if(data.result.name){
//
//                                            }
                                            that.ticketNum=parseInt(data.result['ref_amount']);
                                            console.log(that.ticketNum);
                                            that.endPrizeMsg1="遗憾，您差一点通关本书";
                                            that.endPrizeMsg2="加油！"+that.ticketNum+"元代金券鼓励一下您～";
                                            switch(that.ticketNum){
                                                case 0:
                                                    that.endPrizeMsg1="遗憾，您差一点通关本书";
                                                    that.endPrizeMsg2="哎呀，红包与您擦肩而过";
                                                    that.successOverTest=false;
                                                    that.notOverTest=false;
                                                    break;
                                                case 3:
                                                    that.myGiftUrl="<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/ticket_3yuan@3x.png";
                                                    that.successOverTest=false;
                                                    that.notOverTest=true;
                                                    break;
                                                case 5:
                                                    that.myGiftUrl="<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/ticket_5yuan@3x.png";
                                                    that.successOverTest=false;
                                                    that.notOverTest=true;
                                                    break;
                                                case 8:
                                                    that.myGiftUrl="<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/ticket_8yuan@3x.png";
                                                    that.successOverTest=false;
                                                    that.notOverTest=true;
                                                    break;
                                                case 10:
                                                    that.myGiftUrl="<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/ticket_10yuan@3x.png";
                                                    that.successOverTest=false;
                                                    that.notOverTest=true;
                                                    break;
                                            }
                                        }
                                        that.$refs.flexContent.addEventListener('touchmove',that.bodyScroll,false);
                                        // 答对几题
                                        // that.testTrueNum=data.count;
                                        that.endAnswerFather=true;
                                        that.nowFlag=true;
                                    }else if(data.code==4){
                                        that.endPrizeMsg1="您已领取过本书的通关礼包";
                                        that.endPrizeMsg2="答题领取其他书的红包";
                                        that.$refs.flexContent.addEventListener('touchmove',that.bodyScroll,false);
                                        // 答对几题
                                        // that.testTrueNum=data[count];
                                        that.endAnswerFather=true;
                                        that.nowFlag=true;
                                    }

                                },
                                error:function(jqXHR){
                                    var res=$.parseJSON(jqXHR.responseText);
                                    switch(res.code){
                                        case 1:
                                            that.toastCenter("活动未开始");
                                            break;
                                        case 2:
                                            that.toastCenter("活动已结束");
                                            break;
                                        case 3:
                                            that.toastCenter("未登录",function(){
                                                location.href="/site/login";
                                            });
                                            break;
                                    }
                                    that.nowFlag=true;
                                }
                            });
                        }
                    }
                },
                // 倒计时
                cutDown:function(){
                    var that=this;
                    clearInterval(timer);
                    that.nums=0;
                    that.surplusTime=20;
                    timer=setInterval(function(){
                        that.nums=that.nums+10;
                        that.$refs.topTimeHint.style.width=(8.53333333-8.53333333*that.nums/20000)+'rem';
                        if(that.nums%1000==0){
                            if(that.surplusTime>0){
                                that.surplusTime--;
                            }else if(that.surplusTime==0){
                                // 如果没提交答案，自动提交
                                (that.submitWord=="提交答案")&&(that.clickYet=true,that.submitEnd());
                                clearInterval(timer);
                            }
                        }
                    },10);
                },
                // toast 弹窗
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
                // 禁止滑动
                bodyScroll: function(e){
                    var e=e||window.event;
                    e.preventDefault();
                },
            },
//            components:{
//                'prize-prom':prizeTemplateList,
//                // 'check-book-vue':checkBookVue,
//            },
            watch:{
                shareGetChance:function(newV,oldV){
                    if(newV==false){
                        this.addShareBtn=false;
                        this.shareChanceMsg="我知道了";
                    }else{
                        this.addShareBtn=true;
                        this.shareChanceMsg="立即分享";
                    }
                },
                clickYet:function(newV,oldV){
                    if(newV==true){
                        this.$refs.submitContent.style.background="url(<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/book_btn_check@3x.png) 0 0 no-repeat/100% 100%";
                        this.$refs.submitContent.style.color="#350d03";

                    }else{
                        this.$refs.submitContent.style.background="url(<?= FE_BASE_URI ?>wap/campaigns/active20180517/images/not_click@3x.png) 0 0 no-repeat/100% 100%";
                        this.$refs.submitContent.style.color="#989794";
                    }
                },
                questions: {
                    handler: function (val, oldVal) {

                    },
                    deep: true
                },
                prizeLists: {
                    handler: function (val, oldVal) {

                    },
                    deep: true
                },
                // testNum:function(newV){
                //     if(newV==5){
                //         this.submitWord="查看结果";
                //     }else{
                //         this.submitWord="提交答案";
                //     }
                // }
            },
            filters:{
                'myFilter':function(value){
                    if(value>9){
                        return value;
                    }else{
                        return '0'+value;
                    }
                },
                'myFilter2':function(value){
                    return value/10000;
                }
            }
        });
    });

</script>