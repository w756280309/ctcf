<?php

$this->title = '小微贷';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180619/css/list.min.css?v=1.1">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
    <style>
        .hide{
            display:none;
        }
        .show{
            display:block;
        }
        .credit-status{
            background: #aaa;
            color:#fff;
        }
        [v-clock]{
            display: none;
        }
        .flex-content{
            background-color: #f6f6f6;
        }
        div.flex-content ul.bid-list{
            padding-bottom: 0;
        }
        div.flex-content ul.bid-list li:last-child{
            padding-bottom:0.26666667rem;
        }
        .flex-content ul.bid-list li a .weal-sign span{
            line-height: 0.54666667rem;
        }
    </style>
<div class="flex-content wrapper" id="app">
    <ul class="bid-list">
        <li v-clock v-if="list.length&&item.dealStatus!=4" v-for="(item, index) in list" :key="index">
            <a :href="item.url">
                <div v-if="item.isActive==true" class="title-msg">
                    <!--售罄 收益中 还清-->
                    <div v-if="item.dealStatus==3" class="icon-rg-sell"></div>
                    <div v-if="item.dealStatus==5" class="icon-rg-earning"></div>
                    <div v-if="item.dealStatus==6" class="icon-rg-pay"></div>
                    <div v-if="item.dealStatus==7||item.dealStatus==1||item.dealStatus==2" class="icon-rg-end"></div>
                    <h4 class="list_title">
                        <span>{{item.title}}</span>
                        <i v-if="item.isXin==1" class="rg">新手</i>
                    </h4>
                </div>
                <div v-else class="title-msg">
                    <!--售罄 收益中 还清-->
                    <div v-if="item.dealStatus==3" class="icon-rg-sell"></div>
                    <div v-if="item.dealStatus==5" class="icon-rg-earning"></div>
                    <div v-if="item.dealStatus==6" class="icon-rg-pay"></div>
                    <div v-if="item.dealStatus==7" class="icon-rg-end"></div>
                    <h4 class="list_title">
                        <span>{{item.title}}</span>
                        <i v-if="item.isXin==1" class="rg">新手</i>
                        <i v-if="item.dealStatus==1" class="advance_msg rg">预告期</i>
                    </h4>
                </div>
                <div class="rate-box clearfix">
                    <div class="rate-box-lf lf">
                        <p>{{item.rate}}<u>%</u><span v-if="item.rateAdd.length">+{{item.rateAdd}}%</span></p>
                        <i v-if="item.cid==3" class="color9">约定利率</i>
                        <i v-else class="color9">预期年化率</i>
                    </div>
                    <div class="rate-box-rg color4 lf">
                        <p>
                            <span>{{item.duration}}</span><span>{{item.durationUnit}}</span>
                        </p>
                        <p>
                            <span>{{item.startMoney}}</span><span>元起投</span>
                        </p>
                    </div>
                </div>
                <div v-if="item.progress!=100" class="rate-copies">
                    <!--<u style="width:'{{item.progress}}'%" class="move-u"></u>-->

                    <u :style="{width:item.progress+'%'}" class="move-u"></u>
                    <i></i>
                    <span>{{item.progress}}%</span>
                </div>
                <div v-else class="rate-copies end-invest">
                    <span class="end-invest-span">{{item.progress}}%</span>
                </div>
                <div class="weal-sign clearfix">
                    <span v-for="(tag,i) in item.tags" class="lf" :key="i">{{tag}}</span>
                    <span v-if="item.pointsMultiple>=2" class="lf rate-color">{{item.pointsMultiple}}倍积分</span>
                    <i class="rg color9">{{item.refundMethod}}</i>
                </div>
            </a>
        </li>
    </ul>
    <p class="last-msg hide" style="height: 1rem;line-height: 1rem;font-size: 0.4rem;text-align: center">加载中······</p>
</div>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?= FE_BASE_URI ?>libs/wxShare.js"></script>
<script>
    var app=new Vue({
        el:"#app",
        data:{
            list:[],
            page:1,
            bidTime:24,
            stop:true,
            maxPage:1,
        },
        created(){
            wxShare.setParams("温都金服小微贷项目——等额本息全新还款计划上线！", "点击链接，了解详情~", "<?= Yii::$app->params['clientOption']['host']['wap'] ?>promotion/p180620/index", "https://static.wenjf.com/upload/link/link1529465517828870.jpg", "<?= Yii::$app->params['weixin']['appId'] ?>", "/promotion/p180620/index/add-share");
        },
        mounted(){
            var that=this;
            this.bidTime=parseInt(this.getUrlParam('bidTime'));
            switch(this.bidTime){
                case 36:
                    $('title').html("小微贷-36个月");
                    this.getPage(this.bidTime,this.page);
                    break;
                default:
                    this.bidTime = 24;
                    $('title').html("小微贷-24个月");
                    this.getPage(this.bidTime,this.page);
                    break;
            };
            window.onscroll=function(e){
                if ($(this).scrollTop() + $(window).height() + 20 >= $(document).height() && $(this).scrollTop() > 20) {
                    //当前要加载的页码
                    that.getPage(that.bidTime,that.page);
                }
            }
        },
        methods:{
            getUrlParam(name) {
                var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); // 构造一个含有目标参数的正则表达式对象
                var r = window.location.search.substr(1).match(reg);  // 匹配目标参数
                if (r != null) return unescape(r[2]); return null; // 返回参数值
            },
            getPage(x,y){
                var that=this;
                var partArr=[];
                if(that.stop==true&&that.page<=that.maxPage){
                    $('.last-msg').removeClass('hide');
                    that.stop=false;
                    $.ajax({
                        url:'/promotion/p180620/recommend-product',
                        data:{'expires':x,
                            'page':y,
                            'size':5},
                        type:'get',
                        dataType:'json',
                        success:function(data){
                            that.maxPage=data.relateInfo.totalPage;
                            if(data.data.length>0){
                                for(key in data.data){
                                    if(data.data[key].tags.length>0){
                                        partArr=data.data[key].tags.split('，');
                                        data.data[key].tags=partArr;
                                    };
                                    app.list.push(data.data[key]);
                                    partArr=[];
                                }
                            }
                            that.page++;
                            $('.last-msg').addClass('hide');
                            that.stop=true;
                        },
                        error:function(){
                            $('.last-msg').addClass('hide');
                            that.stop=true;
                        }
                    })
                }
            }
        },
    })
</script>
