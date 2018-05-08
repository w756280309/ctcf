<?php

$this->title = '慈善梦想气球';
$user = Yii::$app->user->getIdentity();
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180416/css/index.min.css?v=1.6117">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>wap/campaigns/active20180416/js/drawImg.1.1.js?v=1.01111112"></script>
<div ref="flexContent" class="flex-content" id="app">
    <canvas style="display:none" id="canvasOne" width="500" height="300">
        Your brower does not support HTML5 Canvas!
    </canvas>
    <a class="hand-hint">
    </a>
    <div v-cloak :class="{'show-dream-box':showDream}" class="dream-box">
        <div ref="dreamContent" class="dream-img-box">
            <img :src="imgUrl" alt="我的梦想">
            <div v-cloak class="linshiDv">{{DV}}</div>
            <p>{{maskHintText}}</p>
            <i @click="hideDream"></i>
        </div>
    </div>
    <div class="top-bg">
        <div class="top-hint"></div>
    </div>
    <div class="dream-contant">
        <div class="commonDisplay">
            <div v-cloak v-if="firstLogin" class="envelope">
                <!--<img src="./images/envelope_to_write2.png" alt="">-->
                <span @click="toWriteMsg">去写下梦想</span>
            </div>
            <div ref="toWriteContent" v-cloak v-show="!firstLogin&&toWrite" class="envelope-write clearfix">
                <div class="envelope-write-contant rg">
                    <textarea ref="textareaContent" class="textarea" contenteditable="true" maxlength="50" @focus="startDream" @input="writingDream" @blur="leaveDream" v-model="textareaDream" :class="{'no-write':noWriteColor}"></textarea>
                    <p v-cloak>{{textareaLength}}/50</p>
                </div>
                <a @click="inviteFriend" :class="{'share-btn':shareClass}">{{btn1}}</a>
                <a @click="pullDream">{{btn2}}</a>
            </div>
            <div v-cloak v-show="!firstLogin&&!toWrite" class="envelope-check">
                <div ref="envelopeComplate" class="envelope-write-contant">
                    <div class="cloud-father-box">
                        <div class="envelope-cloud-bg">
                            <p class="cloud-bg1"></p>
                            <p class="cloud-bg2"></p>
                            <p class="cloud-bg3"></p>
                            <p class="cloud-bg4"></p>
                            <p v-if="lastCloud" class="cloud-bg5"></p>
                        </div>
                    </div>
                    <div class="pull-dream-box animated tada"></div>
                    <!--<div class="hand-hint">-->
                    <!--查看更多-->
                    <!--<i></i>-->
                    <!--</div>-->
                    <canvas id="cas" width="750" height="540"></canvas>
                </div>
                <a @click="inviteFriend" class="share-btn">邀请好友参与</a>
                <a @click="showDreamBox">查看我的梦想</a>
                <p @click="modifiersDream" class="modifiers">修改梦想</p>
            </div>
        </div>
        <div class="rule1" id="rulesMsg">
            <div class="rule1-msg">
                <div class="rule1-msg-p">
                    <p>活动时间：2018.5.8至5.16；</p>
                    <p>活动参与方式：在输入框写下自己的慈善小目标，然后放飞气球（可以查看、重写、分享）；</p>
                    <p>活动奖励：周年庆当天，将在服务号公布各种慈善愿望；同时选取3位幸运用户，助力其实现自己的慈善愿望；</p>
                    <p>公布3名幸运用户后，客服将在7个工作日内与其联系，帮助他们实现慈善梦想；</p>
                    <p>本活动最终解释权归温都金服所有。</p>
                </div>
            </div>
        </div>
        <div class="rule2">
            <div class="rule2-msg">
                <p class="get-medal">我的慈善勋章总数：<span v-cloak>{{tsk1+tsk2}}枚</span></p>
                <div class="medal-box clearfix">
                    <div class="medal-type lf">
                        <div class="img-box1">
                            <img :src="imgUrl1" alt="">
                            <span v-text="tsk1"></span>
                        </div>
                        <p>放飞梦想气球</p>
                    </div>
                    <div class="medal-type rg">
                        <div class="img-box2">
                            <img :src="imgUrl2" alt="">
                            <span v-text="tsk2"></span>
                        </div>
                        <p>邀请好友完成注册</p>
                    </div>
                </div>
                <ol>
                    <li>活动期间参与本场游戏，即可获得1枚慈善勋章（最多1枚）;</li>
                    <li>活动期间每邀请1位好友完成注册，即可获得1枚慈善勋章（无上限）；</li>
                    <li>慈善勋章可在周年庆主会场（5月20日）抽取现金红包或积分奖励，最高520元现金红包！</li>
                </ol>
            </div>
            <i></i>
        </div>
    </div>
</div>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?= FE_BASE_URI ?>libs/wxShare.js?v=3.0"></script>
<script>
    window.dataJson=dataJson;
    window.origin = document.location.origin;
    // console.log(window.dataJson);
    window.onload=function(){
        FastClick.attach(document.body);
        var myapp=new Vue({
            el:"#app",
            created:function(){
                // // 如果是点击邀请来的用户，跳转页面
                // (this.isInvite==true)&&(location.href='/luodiye/v2');
                // ua判断查看梦想提示文字

                function parseUA(){
                    var ua = navigator.userAgent.toLowerCase();
                    return {
                        isWjfAndroid:ua.indexOf('wjfandroid')>-1,
                        isWjfApple:ua.indexOf('wjfapple')>-1
                    };
                }
                this.lineApp = parseUA();
                // console.log(lineApp);
                if (this.lineApp.isWjfAndroid==true||this.lineApp.isWjfApple==true){
                    this.maskHintText="截图保存我的梦想";
                }else{
                    this.maskHintText="长按图片，保存到相册";
                };
                //分享内容设置
                wxShare.setParams("放飞慈善梦想气球，温都金服助您实现慈善小目标！", "点击链接，立即参与~", "<?= Yii::$app->params['clientOption']['host']['wap'] ?>/promotion/p180508/index?code=<?= $user !== null ? $user->usercode : '' ?>", "https://static.wenjf.com/upload/link/link1524908202540711.png", "<?= Yii::$app->params['weixin']['appId'] ?>", "/promotion/p180508/add-share");
                wxShare.TimelineSuccessCallBack = function () {
                    $.get("/promotion/p180508/add-share?scene=timeline&shareUrl=" + encodeURIComponent(location.href))
                };
                // 查看支不支持canvas
                var Cvs = document.getElementById("canvasOne");
                if (!Cvs || !Cvs.getContext){
                    this.supportCanvas=false;
                }else{
                    this.supportCanvas=true;
                };
                // this.distance=document.querySelector('.dream-contant').offsetTop;
                // console.log(this.distance);
                this.imgUrl1=(this.tsk1==0?"<?= FE_BASE_URI ?>wap/campaigns/active20180416/images/undone_tsk.png":"<?= FE_BASE_URI ?>wap/campaigns/active20180416/images/done_tsk.png");
                this.imgUrl2=(this.tsk2==0?"<?= FE_BASE_URI ?>wap/campaigns/active20180416/images/undone_tsk.png":"<?= FE_BASE_URI ?>wap/campaigns/active20180416/images/done_tsk.png");
                // 是不是有勋章判断是不是第一次来
                this.tsk1>0?(this.firstLogin=false,this.firstDream=false):(this.firstLogin=true,this.firstDream=true);
                // this.firstDream==true?(this.textareaDream='在这里写下您的慈善梦想吧！',this.noWriteColor=true,this.textareaLength=50):(this.textareaDream=this.textDream,this.noWriteColor=true,this.textareaLength=(50-this.textareaDream.length));
                this.firstDream==true?(this.textareaDream='在这里写下您的慈善梦想吧！',this.noWriteColor=true,this.textareaLength=50):(this.noWriteColor=false);
            },
            mounted:function(){
                var cas = document.getElementById('cas');
                // 获取绘图工具
                var ctx = cas.getContext('2d');// webgl
                var baseWidth=cas.width;
                var baseHeight=cas.height;
                var that=this;
                var img=new Image();
                // img.setAttribute('crossOrigin', 'anonymous');
                img.src=that.src2;
                img.onload=function(){
                    var mark={x:0,y:0};
                    setInterval(function(){
                        ctx.clearRect(0,0,baseWidth,baseHeight);
                        ctx.drawImage(img,mark.x,mark.y,750,540,0,0,cas.width,cas.height);
                        mark.x+=750;
                        (mark.x==3750)&&(mark.x=0,mark.y+=540);
                        (mark.y==2160)&&(mark.x=0,mark.y=0);
                    },100);
                };
            },
            data:{
                csrf:window.dataJson.csrf,
                DV:'',
                lineApp:null,
                // 是否是点击邀请来的用户
                isInvite:window.dataJson.isInvite,
                btn1:'邀请好友参与',
                btn2:'放飞梦想气球',
                shareClass:true,
                maskHintText:"长按图片，保存到相册",
                // 活动状态
                activeState:window.dataJson.promoStatus,
                // 登陆状态
                isLoggedIn:window.dataJson.isLoggedIn,
                // 第一次登陆点击写梦想
                firstLogin:window.dataJson.freeMedalNum==0?true:false,
                // 去写梦想还是查看梦想的页面
                toWrite:false,
                // 没有写过梦想提示文字的颜色
                noWriteColor:true,
                // 写梦想的默认提示文字及正式文字
                textareaDream:'在这里写下您的慈善梦想吧！',
                // 真实梦想
                textDream:'',
                //梦想ID
                textDreamId:window.dataJson.ticketId,
                // 之前没有写过梦想
                firstDream:window.dataJson.freeMedalNum==0?true:false,
                // 还剩多少字可以输入
                textareaLength:50,
                // 任务一和2的勋章对应的URL
                tsk1:window.dataJson.freeMedalNum,
                tsk2:window.dataJson.inviteMedalNum,
                imgUrl1:"<?= FE_BASE_URI ?>wap/campaigns/active20180416/images/undone_tsk.png",
                imgUrl2:"<?= FE_BASE_URI ?>wap/campaigns/active20180416/images/undone_tsk.png",
                // 梦想的有效字
                textareaDreamValid:'',
                animationEnd:['animationend','webkitAnimationEnd','mozAnimationEnd','MSAnimationEnd','oAnimationEnd'],
                imgUrl:window.origin+"/images/my_dream.png",
                // 是否显示梦想
                showDream:false,
                // 是否支持canvas
                supportCanvas:true,
                // 活动介绍距离顶部的距离
                distance:0,
                src2:'<?= FE_BASE_URI ?>wap/campaigns/active20180416/images/qiqius.png',
                // 最后一片云
                lastCloud:true,
            },
            methods:{
                // 邀请好友
                inviteFriend:function(e){
                    var e=e||window.event;
                    if(e.target.innerHTML=='邀请好友参与'){

                    }else if(e.target.innerHTML=='放弃修改'){
                        this.textareaDream=this.textDream;
                        this.toWrite=false;
                    }
                },
                // 首页点击写梦想
                toWriteMsg:function(){
                    $('body,html').animate({scrollTop: this.distance},300);
                    switch(this.activeState){
                        case 0:
                            // 如果是点击邀请来的用户，跳转页面
                            // (this.isInvite==true)&&(location.href='/luodiye/v2');
                            // this.isLoggedIn==true?(this.firstLogin=false,this.toWrite=true):location.href='/site/login';
                            if(this.isLoggedIn==true){
                                this.firstLogin=false;
                                this.toWrite=true;
                            }else{
                               if(this.isInvite==true){
                                   location.href='/luodiye/v2';
                               }else{
                                   location.href='/site/login';
                               }
                            };
                            break;
                        case 1:
                            this.toastCenter('活动未开始');
                            break;
                        case 2:
                            this.toastCenter('活动已结束');
                            break;

                    };
                },
                // 获取焦点梦想  focus
                startDream:function(){
                    // (this.firstDream==true&&this.noWriteColor==true)?this.textareaDream='':this.textareaDream=this.textDream;
                    if(this.firstDream==true){
                        if(this.noWriteColor==true){
                            this.textareaDream=''
                        }else{
                            return false;
                        }
                    }else{
                        if(this.noWriteColor==true){
                            this.textareaDream=this.textDream;
                            this.noWriteColor=false;
                        }else{
                            return false;
                        }
                    }
                },
                // 离开写梦想 blur
                leaveDream:function(){
                    // (this.firstDream==true&&this.textareaDream=='')&&(this.textareaDream='在这里写下您的慈善梦想吧！',this.noWriteColor=true);
                    if(this.firstDream==true){
                        if(this.textareaDream==''){
                            this.textareaDream='在这里写下您的慈善梦想吧！';
                            this.noWriteColor=true;
                        }else{
                            return false;
                        }
                    }else{
                        if(this.textareaDream==''){
                            this.textareaDream=this.textDream;
                            this.noWriteColor=true;
                            this.textareaLength=(50-this.textareaDream.length);
                        }else{
                            return false;
                        }
                    }
                },
                // 写梦想
                writingDream:function(){
                    this.textareaLength=50-this.textareaDream.length;
                    this.noWriteColor=false;
                    if(this.textareaDream.length==0||this.textareaDream==this.textDream){
                        this.noWriteColor=true;
                        return false;
                        // this.textareaDream='在这里写下您的慈善梦想吧！';
                    };

                },
                // 点击放飞梦想
                pullDream:function(e){
                    var that=this;
                    var e=e||window.event;
                    $('body,html').animate({scrollTop: this.distance},300);
                    switch(this.activeState){
                        case 0:
                            this.textareaDreamValid=this.removeAllSpace(this.textareaDream);
                            if(this.noWriteColor==true||this.textareaDreamValid.length<5){
                                if(e.target.innerHTML=="保存修改"){
                                    this.toastCenter('未修改或写下的梦想少于5个字');
                                }else{
                                    this.toastCenter('请写下不少于5个字的梦想');
                                }
                                this.textareaDreamValid='';
                            }else{
                                // 符合要求发请求记录之前写过梦想，并把梦想复制
                                $.ajax({
                                    url:"/promotion/p180508/fly",
                                    data:{"content":that.textareaDream,'_csrf':that.csrf},
                                    type:'post',
                                    dateType:"json",
                                    success:function(data){
                                        if(data.code===0){
                                            that.tsk1=data.freeMedalNum;
                                            var toWriteContent=that.$refs.toWriteContent;
                                            toWriteContent.classList.add('hideBox');
                                            for(var i=0;i<that.animationEnd.length;i++){
                                                that.$refs.toWriteContent.addEventListener(that.animationEnd[i],function(){
                                                    that.firstDream=false;
                                                    that.toWrite=false;
                                                    toWriteContent.classList.remove('hideBox');
                                                },false);
                                            }
                                        }else if(data.code===1){
                                            that.toastCenter('活动未开始');
                                        }else if(data.code===2){
                                            that.toastCenter('活动已结束');
                                        }else if(data.code===3){
                                            that.toastCenter('未登录',function(){
                                                location.href='/site/login';
                                            });
                                        }else if(data.code===10){
                                            that.toastCenter('请写下不少于5个字的梦想');
                                        }
                                    },
                                    error:function(jqXHR){
                                        var res=$.parseJSON(jqXHR.responseText);
                                        if(res.code===1){
                                            that.toastCenter('活动未开始');
                                        }else if(res.code===2){
                                            that.toastCenter('活动已结束');
                                        }else if(res.code===3){
                                            location.href='/site/login';
                                        }else if(res.code===10){
                                            that.toastCenter('请写下不少于5个字的梦想');
                                        }
                                    }
                                });
                            }
                            break;
                        case 1:
                            this.toastCenter('活动未开始');
                            break;
                        case 2:
                            this.toastCenter('活动已结束');
                            break;
                    };
                },
                // 点击修改梦想
                modifiersDream:function(){
                    this.btn1='放弃修改';
                    this.btn2="保存修改";
                    var that=this;
                    switch(this.activeState){
                        case 0:
                            $.ajax({
                                url:'/promotion/p180508/revise',
                                type:'get',
                                dataType:'json',
                                data:{'ticketId':that.textDreamId},
                                success:function(data){
                                    if(data.code===0){
                                        that.toWrite=true;
                                        that.textDream=data.content;
                                        // console.log(that.textDream,window.dataJson);
                                        that.textareaDream=that.textDream;
                                        // console.log(that.textareaDream,that.textareaDream.length);
                                        that.textareaLength=(50-that.textareaDream.length);
                                        // that.$refs.textareaContent.focus();
                                    }else if(data.code===2){
                                        that.toastCenter('活动已结束');
                                    }else if(data.code===3){
                                        location.href='/site/login';
                                    }
                                },
                                error:function(jqXHR){
                                    var res=$.parseJSON(jqXHR.responseText);
                                    if(res.code===2){
                                        that.toastCenter('活动已结束');
                                    }else if(res.code===3){
                                        location.href='/site/login';
                                    }
                                }
                            });
                            break;
                        case 1:
                            this.toastCenter('活动未开始');
                            break;
                        case 2:
                            this.toastCenter('活动已结束');
                            break;
                    };
                },
                // 点击查看梦想
                showDreamBox:function(){
                    var that=this;
                    $('body,html').animate({scrollTop: this.distance},300);
                    switch(this.activeState){
                        case 0:
                            $.ajax({
                                url:'/promotion/p180508/revise',
                                type:'get',
                                data:{'ticketId':that.textDreamId},
                                dateType:"json",
                                success:function(data){
                                    if(data.code===0){
                                        that.textDream=data.content;
                                        var obj={
                                            //素材路径
                                            src:window.origin+'/images/my_dream.png',
                                            // 素材大小
                                            size:{width:'600',height:'800'},
                                            // 文本内容
                                            text: that.textDream,
                                            textAlign:'left',
                                            textBaseline:'top',
                                            textPosition:{wx:'38',hx:'536',width:'450',maxWidth:"710"},
                                            colorPosition:{x0:'0',y0:'0',x1:'750',y1:'440'},
                                            color:[["0","yellow"],["0.3","deeppink"],["1.0","blue"]],
                                            font:"26px microsoft yahei",
                                            initHeight:0,
                                            hHeight:33,
                                            sync:'sync',
                                            times:{delay:100,interval:200},
                                            // img:'',
                                            // 随机颜色
                                            // colorRandoming:true,
                                        };
                                        if (that.lineApp.isWjfAndroid==true||that.lineApp.isWjfApple==true||!that.supportCanvas){
                                            that.DV=that.textDream;
                                        }else{
                                            var abc=new Watermark(obj);
                                            abc.init(function(){
                                                that.imgUrl=abc.img.src;
                                            });
                                        }
                                        that.showDream=true;
                                        that.$refs.flexContent.addEventListener('touchmove',that.bodyScroll,false);
                                    }else if(data.code===2){
                                        that.toastCenter('活动已结束');
                                    }else if(data.code===3){
                                        location.href='/site/login';
                                    }

                                },
                                error:function(jqXHR){
                                    var res=$.parseJSON(jqXHR.responseText);
                                    if(res.code===2){
                                        that.toastCenter('活动已结束');
                                    }else if(res.code===3){
                                        location.href='/site/login';
                                    }
                                }
                            });
                            break;
                        case 1:
                            this.toastCenter('活动未开始');
                            break;
                        case 2:
                            this.toastCenter('活动已结束');
                            break;

                    };

                },
                // 关闭按钮
                hideDream:function(){
                    this.DV='';
                    this.showDream=false;
                    this.$refs.flexContent.removeEventListener('touchmove',this.bodyScroll,false);
                },
                bodyScroll: function(e){
                    var e=e||window.event;
                    e.preventDefault();
                },
                handHint:function(){
                    $('body,html').animate({scrollTop: this.distance},300);
                    return false;
                },
                // 去除空格
                removeAllSpace:function (str) {
                    return str.replace(/\s+/g, "");
                },
                //toast
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
            },
            watch:{
                textareaDream:{
                    deep:true,
                    handler:function(newV,oldV){
                    }
                },
                // 编辑页分享按钮监听按钮的变化
                btn1:function(newV,oldV){
                   if(newV=="放弃修改"){
                       this.shareClass=false;
                   }else if(newV='邀请好友参与'){
                       this.shareClass=true;
                   }
                },
                // 监听勋章1的变化
                tsk1:function(newV,oldV){
                    newV==0?(this.imgUrl1="<?= FE_BASE_URI ?>wap/campaigns/active20180416/images/undone_tsk.png"):(this.imgUrl1="<?= FE_BASE_URI ?>wap/campaigns/active20180416/images/done_tsk.png");
                }
            }
        })
    }
</script>
