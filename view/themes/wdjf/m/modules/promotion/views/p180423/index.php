<?php

$this->title = '限时积分抽奖活动';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180417/css/gifts-list.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180417/css/index.min.css?v=1.5">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/iscroll.js"></script>
<script src="<?= FE_BASE_URI ?>libs/handlebars-v4.0.10.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<!--<script src="--><?//= FE_BASE_URI ?><!--wap/campaigns/active20180417/js/gifts-list.js"></script>-->
<div class="flex-content" id="active">
    <div class="banner">
        <img onclick="return false;" src="<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/banner.png" alt="">
        <dl class="clearfix">
            <dt class="lf">我的积分：<a :href="url"><strong v-cloak>{{points}}</strong></a></dt>
            <dd @click="giftList" class="rg">奖品列表></dd>
        </dl>
    </div>
    <div class="draw-bg" id="prz_pool">
        <table class="clearfix" id="prize_pool">
            <tr>
                <td class="lf lottery-unit-1 lottery-unit mg">
                    <div class="top">
                        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/gifts_01.png" alt="">
                        <p>意大利公鸡头皂</p>
                    </div>
                    <div class="bottom"></div>
                </td>
                <td class="lf lottery-unit-2 lottery-unit mg">
                    <div class="top">
                        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/gifts_02.png" alt="">
                        <p>天堂伞</p>
                    </div>
                    <div class="bottom"></div>
                </td>
                <td class="lf lottery-unit-3 lottery-unit mg">
                    <div class="top">
                        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/gifts_03.png" alt="">
                        <p>50元超市卡</p>
                    </div>
                    <div class="bottom"></div>
                </td>
            </tr>
            <tr>
                <td class="lf lottery-unit-8 lottery-unit mg">
                    <div class="top">
                        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/gifts_08.png" alt="">
                        <p>小米恒温电水壶</p>
                    </div>
                    <div class="bottom"></div>
                </td>
                <td class="lf lottery-unit"><img @click="draw" id="draw" data-click="true"
                                                 src="<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/btn.png"
                                                 alt="">
                </td>
                <td class="lf lottery-unit-4 lottery-unit mg" style="margin-left: 0.15rem">
                    <div class="top">
                        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/gifts_04.png" alt="">
                        <p>五谷杂粮礼盒</p>
                    </div>
                    <div class="bottom"></div>
                </td>
            </tr>
            <tr>
                <td class="lf lottery-unit-7 lottery-unit mg">
                    <div class="top">
                        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/gifts_07.png" alt="">
                        <p>200积分</p>
                    </div>
                    <div class="bottom"></div>
                </td>
                <td class="lf lottery-unit-6 lottery-unit mg">
                    <div class="top">
                        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/gifts_06.png" alt="">
                        <p>20元代金券</p>
                    </div>
                    <div class="bottom"></div>
                </td>
                <td class="lf lottery-unit-5 lottery-unit mg">
                    <div class="top">
                        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/gifts_05.png" alt="">
                        <p>维达抽纸6连包</p>
                    </div>
                    <div class="bottom"></div>
                </td>
            </tr>
        </table>
    </div>
    <div class="regular">
        <p class="get-more">积分不足?点击 <a href="/deal/deal/index">立即投资</a></p>
        <div class="title">活动规则</div>
        <div class="list">
            <p>活动时间2018年4月23日-4月28日；</p>
            <p>点击“立即抽奖”，积分将立即扣除，同时获得1件随机奖品；</p>
            <p>每次抽奖需消耗<i class="special">200</i>积分，积分可通过投资、签到等赚取；</p>
            <p>已消耗的积分不可退回；</p>
            <p>代金券、积分等虚拟奖品将立即到账，请至账户中心查看；</p>
            <p>抽到实物奖品的客户，客服会在7个工作日内联系您确认领奖事宜，请保持通信畅通。</p>
        </div>
        <div class="tips">
            本次活动最终解释权归温都金服所有<br>
            理财非存款 产品有风险 投资须谨慎
        </div>
    </div>

    <!--中奖弹框-->
    <div v-show="isClose" class="mask" v-cloak>
        <div class="pop">
            <img @click="closePop" class="close" src="<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/close.png"
                 alt="">
            <p>{{result.name}}</p>
            <img  onclick="return false;" class="gift" :src="result.path" alt="">
            <img  onclick="return false;" class="pop-img" src="<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/pop.png" alt="">
            <img @click="closePop" class="confirm"
                 src="<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/confirm.png" alt="">
        </div>
    </div>

    <!--isDraw-->
    <div v-show="isDraw" class="mask" v-cloak>
        <div class="pop">
            <img  onclick="return false;" class="is-draw" src="<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/isdraw.png" alt="">
            <img @click="closeDraw(0)" class="close-special"
                 src="<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/close.png" alt="">
            <div @click="closeDraw(1)" class="btn confirm-special">是</div>
            <div @click="closeDraw(0)" class="btn cancel">否</div>
        </div>
    </div>
</div>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?= FE_BASE_URI ?>libs/wxShare.js?v=3.0"></script>
<script>
    /*此方法依赖jquery-1.11.1.min.js&&iscroll.js&&handlebars-v4.0.10.js*/
    function giftsList(opt){
        var tpl = '<script  id="gifts-list-template" type="text/x-handlebars-template">\n' +
            '    <!--对应的奖品列表-->\n' +
            '    <div class="prizes-box">\n' +
            '        <div class="outer-box">\n' +
            '            <img class="pop_close" src="'+opt.closeImg+'" alt="">\n' +
            '\n' +
            '            <div class="prizes-pomp">\n' +
            '                <p class="prizes-title">奖品列表</p>\n' +
            '                <div id="wrapper">\n' +
            '                    <ul>\n' +
            '                        {{#if isGifts}}\n' +
            '                        {{#each list}}\n' +
            '                        <li class="clearfix">\n' +
            '                            <div class="lf"><img src="<?= FE_BASE_URI ?>{{path}}" alt="礼品"></div>\n' +
            '                            <div class="lf">\n' +
            '                                <p>{{name}}</p>\n' +
            '                                <p>{{awardTime}}</p>\n' +
            '                            </div>\n' +
            '                        </li>\n' +
            '                        {{/each}}\n' +
            '                        {{else}}\n' +
            '                        <li class="no-prizes">您还没有获得奖品哦！</li>\n' +
            '                        {{/if}}\n' +
            '                    </ul>\n' +
            '                </div>\n' +
            '            </div>\n' +
            '        </div>\n' +
            '    </div>\n' +
            '<\/script>';
$('body').append(tpl);
var defaults = {
isGifts : false,
list : []
},
options = $.extend(defaults, opt);
var data = {
isGifts : options.isGifts,
list : options.list
};
var source = $("#gifts-list-template").html();
var template = Handlebars.compile(source);
var html = template(data);
$('body').append(html);
$(".prizes-box").on('touchmove', eventTarget, false);
$(".pop_close").on("click",function(){
$("#gifts-list-template,.prizes-box").remove();
$(".prizes-box").off("touchmove",eventTarget,false);
});
setTimeout(function(){
var myScroll = new iScroll('wrapper',{
vScrollbar:false,
hScrollbar:false
});
},500)
}
function eventTarget(event) {
var event = event || window.event;
event.preventDefault();
}


</script>
<script>
    $(function () {
        FastClick.attach(document.body);
        //抽奖配置代码
        var lottery = {
            index: -1,	//当前转动到哪个位置，起点位置
            count: 0,	//总共有多少个位置
            timer: 0,	//setTimeout的ID，用clearTimeout清除
            speed: 100,	//初始转动速度
            times: 0,	//转动次数
            cycle: 30,	//转动基本次数：即至少需要转动多少次再进入抽奖环节
            prize: -1,	//中奖位置
            jiangpin: 0,
            init: function (id) {
                if ($("#" + id).find(".lottery-unit").length > 0) {
                    $lottery = $("#" + id);
                    $units = $lottery.find(".lottery-unit");
                    this.obj = $lottery;
                    this.count = $units.length;
                }
            },
            roll: function () {
                var index = this.index;
                var count = this.count;
                var lottery = this.obj;
                $(lottery).find(".lottery-unit-" + index + " .top").removeClass("active-top");
                $(lottery).find(".lottery-unit-" + index + " .bottom").removeClass("active-bottom");
                index += 1;
                if (index > count - 1) {
                    index = 0;
                }
                $(lottery).find(".lottery-unit-" + index + " .top").addClass("active-top");
                $(lottery).find(".lottery-unit-" + index + " .bottom").addClass("active-bottom");
                this.index = index;
                return false;
            },
            stop: function (index) {
                this.prize = index;
                return false;
            }
        };

        function roll() {
            if (lottery.times > lottery.cycle + 5 && lottery.prize == lottery.index) {
                clearTimeout(lottery.timer);
                lottery.prize = -1;
                lottery.times = 0;
                setTimeout(active.award, 800);
            } else {
                if (lottery.times < lottery.cycle) {
                    lottery.speed -= 10;
                } else if (lottery.times == lottery.cycle) {
                    var index = lottery.jiangpin;
                    lottery.prize = index; //此处定义最后是哪个奖品，可通过给lottery.jiangpin赋值改变
                } else {
                    if (lottery.times > lottery.cycle + 10 && ((lottery.prize == 0 && lottery.index == 5) || lottery.prize == lottery.index + 4)) {
                        lottery.speed += 110;
                    } else {
                        lottery.speed += 20;
                    }
                }
                if (lottery.speed < 65) {
                    lottery.speed = 65;
                }
                lottery.timer = setTimeout(roll, lottery.speed);
            }
            lottery.times += 1;
            lottery.roll();
        }

        var active = new Vue({
            el: "#active",
            data: {
                promoStatus: dataJson.promoStatus,
                isLogin: dataJson.isLoggedIn,
                url: "javascript:void(0);",
                points: '',
                isClose: false,
                isDraw: false,
                isDrawn:dataJson.isDrawn,
                result: {
                    name: '',
                    path: ''
                }
            },
            created: function () {
                this.points = this.isLogin ? this.getPoints() : "未登录";
                if (!this.isLogin) {
                    this.url = "/site/login"
                }
                /*微信分享*/
                wxShare.setParams("温都金服积分抽奖限时开启，好礼百分百！", "点击链接，立即参与~", "<?= Yii::$app->params['clientOption']['host']['wap'] ?>/promotion/p180423/index", "https://static.wenjf.com/upload/link/link1521546018709563.jpg", "<?= Yii::$app->params['weixin']['appId'] ?>", "/promotion/p180432/add-share");
            },
            methods: {
                draw: function () {
                    var status = this.baseVerify();
                    if(status){
                        if (!this.isDrawn) {
                            this.isDraw = !this.isDraw;
                        } else {
                            if ($("#draw").data("click")) {
                                this.getGifts();
                            }
                        }
                    }
                },
                getGifts: function () {
                    var _this = this;
                    $.ajax({
                        type: "GET",
                        url: "/promotion/p180423/get-draw",
                        data: "",
                        success: function (data) {
                            if (data.code === 0) {
                                _this.getPoints();
                                switch (data.ticket) {
                                    case "180423_DSH":
                                        _this.result.name = "小米恒温电水壶";
                                        _this.result.path = "<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/gifts_08.png";
                                        //这里设置中的奖品
                                        lottery.jiangpin = 7;
                                        break;
                                    case "180423_G50":
                                        _this.result.name = "50元超市卡";
                                        _this.result.path = "<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/gifts_03.png";
                                        //这里设置中的奖品
                                        lottery.jiangpin = 2;
                                        break;
                                    case "180423_LH":
                                        _this.result.name = "五谷杂粮礼盒";
                                        _this.result.path = "<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/gifts_04.png";
                                        //这里设置中的奖品
                                        lottery.jiangpin = 3;
                                        break;
                                    case "180423_TTS":
                                        _this.result.name = "天堂伞";
                                        _this.result.path = "<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/gifts_02.png";
                                        //这里设置中的奖品
                                        lottery.jiangpin = 1;
                                        break;
                                    case "180423_GJTZ":
                                        _this.result.name = "意大利公鸡头皂";
                                        _this.result.path = "<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/gifts_01.png";
                                        //这里设置中的奖品
                                        lottery.jiangpin = 0;
                                        break;
                                    case "180423_CZ":
                                        _this.result.name = "维达抽纸六连包";
                                        _this.result.path = "<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/gifts_05.png";
                                        //这里设置中的奖品
                                        lottery.jiangpin = 4;
                                        break;
                                    case "180423_P200":
                                        _this.result.name = "200积分";
                                        _this.result.path = "<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/gifts_07.png";
                                        //这里设置中的奖品
                                        lottery.jiangpin = 6;
                                        break;
                                    case "180423_C20":
                                        _this.result.name = "20元代金券";
                                        _this.result.path = "<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/gifts_06.png";
                                        //这里设置中的奖品
                                        lottery.jiangpin = 5;
                                        break;
                                    default:
                                        break;
                                }
                                //初始化抽奖部分
                                lottery.init('prz_pool');
                                $("#draw").data("click", false);
                                //调用接口 改变lottery.jiangpin来决定最终哪个奖品 改变award函数来决定函数的回掉
                                lottery.speed = 90;
                                roll();
                            } else {
                                _this.toastCenter(data.message);
                            }
                        },
                        error: function (error) {
                            _this.toastCenter(error.responseJSON.message);
                        }
                    });
                },
                award: function () {
                    this.isClose = !this.isClose;
                    $("#draw").data("click", true);
                },
                getPoints: function () {
                    var _this = this;
                    $.ajax({
                        type: "GET",
                        url: "/promotion/p180423/get-points",
                        data: "",
                        success: function (data) {
                            if (data.code === 0) {
                                _this.points = data.points+"分";
                            } else {
                                _this.toastCenter(data.message);
                            }
                        },
                        error: function (error) {
                            _this.toastCenter(error.responseJSON.message)
                        }
                    })
                },
                closePop: function () {
                    this.isClose = !this.isClose;
                },
                closeDraw: function (val) {
                    this.isDraw = !this.isDraw;
                    if (val) {
                        this.isDrawn = true;
                        this.getGifts();
                    }
                },
                giftList: function () {
                    var _this = this;
                    var status;
                    switch (_this.promoStatus) {
                        case 0:
                            if (_this.isLogin) {
                                status = true;
                            } else {
                                window.location.href = "/site/login";
                            }
                            break;
                        case 1:
                            _this.toastCenter('活动未开始');
                            break;
                        case 2:
                            if (_this.isLogin) {
                                status = true;
                            } else {
                                window.location.href = "/site/login";
                            }
                            break;
                        default:
                            break;
                    }
                    if (status) {
                        $.ajax({
                            type: "GET",
                            url: "/promotion/p180423/award-list",
                            data: {key: "promo_180423"},
                            success: function (data) {
                                if (data.length === 0) {
                                    giftsList({
                                        isGifts: false,//无奖品为false
                                        closeImg: '<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/close.png',
                                        list: data
                                    });
                                } else {
                                    giftsList({
                                        isGifts: true,//有奖品，无奖品为false
                                        closeImg: '<?= FE_BASE_URI ?>wap/campaigns/active20180417/images/close.png',
                                        list: data
                                    });
                                }
                            },
                            error: function (error) {
                                _this.toastCenter(error.responseJSON.message)
                            }
                        })
                    }
                },
                baseVerify: function () {
                    var _this = this;
                    switch (_this.promoStatus) {
                        case 0:
                            if (_this.isLogin) {
                                return true;
                            } else {
                                window.location.href = "/site/login";
                                return false;
                            }
                            break;
                        case 1:
                            _this.toastCenter('活动未开始');
                            return false;
                            break;
                        case 2:
                            _this.toastCenter('活动已结束');
                            return false;
                            break;
                        default:
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
                },
            }
        });
    });
</script>
