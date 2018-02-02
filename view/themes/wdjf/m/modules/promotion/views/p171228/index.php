<?php

$this->title = '好友召集令';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20171221/css/index.css?v=1.212">
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/layer_mobile/layer.js"></script>
<script src="<?= FE_BASE_URI ?>libs/handlebars-v4.0.10.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<style>
    [v-cloak]{display: none}
</style>
<div class="flex-content" ref="flexContent" id="app">
    <div class="box-top" :class="{'active':Active}">
        <div class="box-top-mid" style="background: url(<?= FE_BASE_URI ?>wap/campaigns/active20171221/images/shade-contain2-new.png) 0 0 no-repeat;background-size: 100% 100%;height: 10.48rem;">
            <a class="cue-login"></a>
            <div @click="closeBox" class="cue-close">
            </div>
            <p class="active-state">本次活动最终解释权归温都金服所有</p>
            <div class="mid-box-contain">
                <ol>
                    <li class="clearfix"><span>1、</span><p>活动时间：2018年1月8日至2018年1月20日；</p></li>
                    <li class="clearfix"><span>2、</span><p>活动期间每邀请1位好友通过微信端注册并完成首投（不含新手标及转让），即可获得18.8元奖励金；</p></li>
                    <li class="clearfix"><span>3、</span><p>活动期间奖励金限量1000份，单个用户最多获得6份奖励金；</p></li>
                    <li class="clearfix"><span>4、</span><p>每笔奖励金将立即发放到账户余额，请注意查收；</p></li>
                    <li class="clearfix"><span>5、</span><p>本活动仅限投资用户（不含新手标及转让）参与。</p></li>
                </ol>
            </div>
        </div>
    </div>
    <!--用户充值为0的弹窗-->
    <div class="box-top box-top2" :class="{'active':Invest}">
        <div class="box-top-mid">
            <a class="cue-login"></a>
            <div @click="closeBox" class="cue-close">
            </div>
            <div class="mid-box-contain" style="padding-right: .38666667rem;">
                <p style="text-align: center;padding-left:0.16rem" class="mid-box-hint">投资后才能开启活动哦！快去投资吧！</p>
                <span style="display:block;color:#ff3939;padding-left:0.6rem;margin-top: .2rem;">注：不含新手标及转让产品</span>
                <a class="get-qualification" href="/deal/deal/index">获取资格</a>
            </div>
        </div>
    </div>
    <a @click="callOrder" class="a-bitton-convene">生成我的召集令</a>
    <a @click="showBg" class="a-nav"><span>活动规则</span></a>

</div>
<script>
    window.onload=function(){
        FastClick.attach(document.body);
    }
    //    弹窗组件
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
    var that=this;

    var app=new Vue({
        el:'#app',
        data:{
            Active:false,
            Invest:false,
            flag:true,
        },
        methods:{
//          点击生成召唤令
            callOrder:function(){
                if(this.flag){
                    this.flag=false;
                    var vues=this;
                    var xhr = $.get('/promotion/p171228/do-call');
                    xhr.done(function(res){
                        vues.flag=true;
                        console.log(res);
                        if(res.code === 0){
                            location.href='/promotion/p171228/calling';
                        }
                    });
                    xhr.fail(function(jqXHR){
                        vues.flag=true;
                        var resp = $.parseJSON(jqXHR.responseText);
                        if(resp.code===1){
                            that.toastCenter("活动未开始");
                        }else if(resp.code===2){
                            that.toastCenter("活动已结束");
                        }else if(resp.code===3){
                             location.href="/site/login"
                        }else if(resp.code===5){
                            location.href='/promotion/p171228/calling';
                        }else if(resp.code===6){
                            that.toastCenter("系统错误");
                        }else if(resp.code===7){
                            vues.Invest=true;
                            vues.$refs.flexContent.addEventListener('touchmove',vues.bodyScroll,false);
                        }
                    })
                }
            },
//          点击活动详情显示弹窗
            showBg:function(){
                this.Active=true;
                this.$refs.flexContent.addEventListener('touchmove',this.bodyScroll,false);
            },
            //          点击关闭按钮关闭弹窗
            closeBox:function(){
                this.Active=false;
                this.Invest=false;
                this.$refs.flexContent.removeEventListener('touchmove',this.bodyScroll,false);
            },
            bodyScroll: function(e){
                var e=e||window.event;
                e.preventDefault();
            },
//
        }
    })
</script>