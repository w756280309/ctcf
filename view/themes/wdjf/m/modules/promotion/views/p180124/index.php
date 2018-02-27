<?php
$this->title = '砸金蛋赢好礼';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180110/css/index.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/popover.css">
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/layer_mobile/layer.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/popover.js"></script>
<script src="<?= FE_BASE_URI ?>libs/layer_mobile/layer.js"></script>
<style type="text/css">
    [v-cloak] {
        display: none
    }

    .popBtm {
        width: 100%;
        padding: 0;
        border-top: 1px solid #ddd;
        line-height: 1.6rem;
    }

    .popMiddle {
        padding: 0.33333333rem 0.4rem;
    }
</style>

<div id="active">
    <div class="flex-content">
        <div class="part-one" style="background: url(<?= FE_BASE_URI ?>wap/campaigns/active20180110/images/bg_new_one.png) no-repeat;background-size: 100% 100%;"></div>
        <div class="part-two">
            <div @click="showAward()" class="my-reward"></div>
            <div v-cloak class="before-egg" v-if="!ifEggbroken">
                <img @click="smashEgg" src="<?= FE_BASE_URI ?>wap/campaigns/active20180110/images/pic_egg_before.png" alt=""
                     class="golden-egg">
                <img @click="smashEgg" src="<?= FE_BASE_URI ?>wap/campaigns/active20180110/images/hammer.png" alt=""
                     class="hammer">
            </div>
            <div v-cloak class="opened-egg " v-else>
                <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180110/images/pic_egg_open.png" alt="">
            </div>
            <a @click="goInvest" class="go-invest"></a>
        </div>
        <div class="part-four">
            <a @click="goInvest" class="go-invest"></a>
        </div>
    </div>
    <div class="mask" v-cloak v-show="!ifShowgift"></div>
    <div class="mask1" style="display: none"></div>
    <!--去投资-->
    <div class="pop-goinvest" style="display: none">
        <p class="pop-goinvest-txt">您还没有投资哦，快去完成<br>投资任务吧！</p>
        <a class="pop-goinvest-btn" href="/deal/deal/index">去投资</a>
        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180110/images/pop_close.png" alt="" class="pop-goinvest-close">
    </div>
    <!--获奖列表-->
    <div v-cloak v-show="!ifShowgift" class="pop-rewardList">
        <p class="pop-rewardList-title">奖品列表</p>
        <ul class="pop-rewardList-list">
            <li class="clearfix" v-for="award in awardList">
                <img v-bind:src="award.path" alt="" class="lf">
                <div class="rg">
                    <p class="gift-name">{{award.name}}</p>
                    <p class="gift-date">中奖时间 {{award.awardTime}}</p>
                </div>
            </li>

        </ul>
        <img @click="closeAward" src="<?= FE_BASE_URI ?>wap/campaigns/active20180110/images/pop_close.png" alt=""
             class="pop-rewardList-close">
    </div>
</div>

<script>

    $(function () {
        var ifLogin = $("input[name=isLoggedin]").val() == "true" ? true : false;
        var promoStatus = $("input[name=promoStatus]").val();

        function remind(msg) {
            layer.open({
                content: msg
                , skin: 'msg'
                , time: 2 //2秒后自动关闭
            });
        }

        var vm = new Vue({
            el: '#active',
            data: {
                ifLogin: ifLogin,
                promoStatus: promoStatus,
                ifShowgift: true,
                ifEggbroken: false,
                awardList: []
            },
            methods: {
                //去投资按钮方法
                goInvest: function () {
                    if (this.promoStatus == 1) {
                        remind("活动未开始");
                        return false;
                    } else if (this.promoStatus == 2) {
                        remind("活动已结束");
                        return false;
                    } else {
                        location.href = "/deal/deal/index"
                    }
                },
                //砸蛋的方法
                smashEgg: function () {
                    var that = this;
                    if (that.promoStatus == 1) {
                        remind("活动未开始");
                        return false;
                    } else if (that.promoStatus == 2) {
                        remind("活动已结束");
                        return false;
                    } else {
                        if (that.ifLogin == false) {
                            location.href = "/site/login"
                        } else {
                            //请求接口
                            $.ajax({
                                url: "/promotion/p180124/draw?key=promo_180124",
                                type: "get",
                                dataType: "json",
                                success: function (response) {
                                    if (response.code == 0) {
                                        //    获奖弹窗
                                        that.ifEggbroken = !that.ifEggbroken;
                                        setTimeout(function () {
                                            poptpl.popComponent({
                                                popBackground: '#fff',
                                                popBorder: 0,
                                                closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20180110/images/pop_close.png",
                                                btnMsg: "收下礼品",
                                                popTopColor: "#333",
                                                bgSize: "100% 100%",
                                                title: '<p style="font-size:0.72rem;">恭喜您获得了</p><p style="font-size:0.5066667rem;">' + response.ticket.name + '</p>',
                                                popBtmBackground: '#fff',
                                                popMiddleHasDiv: true,
                                                popBtmColor: '#e01021',
                                                contentMsg: "<img style='margin:0.3rem auto 0.5rem;display: block;width: 5rem;' src='" + "<?= FE_BASE_URI ?>wap/campaigns/active20180110/images/pic_giftget_" + response.ticket.path + ".png" + "' alt=''/>",
                                                popBtmBorderRadius: 0,
                                                popBtmFontSize: ".50666667rem"
                                            }, 'close');
                                            that.ifEggbroken = !that.ifEggbroken;
                                        }, 500)

                                    }
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    var response = jqXHR.responseJSON;

                                    if(response.allTicketCount == 3){
                                        poptpl.popComponent({
                                            popBackground: '#fff',
                                            popBorder: 0,
                                            closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20180110/images/pop_close.png",
                                            btnMsg: "我知道了",
                                            popTopColor: "#fff",
                                            popMiddleHasDiv: true,
                                            popBtmColor: '#e01021',
                                            popBtmBackground: '#fff',
                                            contentMsg: "<p style='text-align: center;color: #999;font-size:0.36rem;line-height: 0.5333rem;'>您已经完成全部抽奖了哦<br>去看看其它活动吧！</p>",
                                            popBtmBorderRadius: 0,
                                            popBtmFontSize: ".4rem",
                                        }, 'close');
                                    } else if ((response.code == 4 || response.code == 5 || response.code == 7) && response.allTicketCount < 3) {
                                        //    去投资弹窗
                                        poptpl.popComponent({
                                            popBackground: '#fff',
                                            popBorder: 0,
                                            closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20180110/images/pop_close.png",
                                            btnMsg: "去投资",
                                            popTopColor: "#fff",
                                            popMiddleHasDiv: true,
                                            popBtmColor: '#e01021',
                                            popBtmBackground: '#fff',
                                            contentMsg: "<p style='text-align: center;color: #999;font-size:0.36rem;line-height: 0.5333rem;'>您没有抽奖机会了，快去完成<br>投资任务吧！</p>",
                                            popBtmBorderRadius: 0,
                                            popBtmFontSize: ".4rem",
                                            btnHref: '/deal/deal/index'
                                        }, 'close');
                                    } else {
                                        remind(response.message)
                                    }
                                }
                            })
                        }
                    }
                },
                //显示获奖列表
                showAward: function () {
                    var that = this;
                    if (that.promoStatus == 1) {
                        remind("活动未开始");
                        return false;
                    }
                    if (that.ifLogin == false) {
                        location.href = "/site/login"
                    }
                    $.ajax({
                        url: "/promotion/p180124/award-list?key=promo_180124",
                        type: "get",
                        dataType: "json",
                        success: function (resData) {
                            if (resData.length == 0) {
                                //    去投资弹窗
                                $('body').on('touchmove', eventTarget, false);
                                poptpl.popComponent({
                                    popBackground: '#fff',
                                    popBorder: 0,
                                    closeUrl: "<?= FE_BASE_URI ?>wap/campaigns/active20180110/images/pop_close.png",
                                    btnMsg: "去投资",
                                    popTopColor: "#fff",
                                    popMiddleHasDiv: true,
                                    popBtmColor: '#e01021',
                                    popBtmBackground: '#fff',
                                    contentMsg: "<p style='text-align: center;color: #999;font-size:0.36rem;line-height: 0.5333rem;'>您还没有抽取到奖品了，快去完成<br>投资任务吧！</p>",
                                    popBtmBorderRadius: 0,
                                    popBtmFontSize: ".4rem",
                                    btnHref: '/deal/deal/index'
                                }, 'close');
                            } else {
                                that.awardList = resData;
                                for (var i = 0; i < that.awardList.length; i++) {
                                    var savePath = that.awardList[i].path
                                    that.awardList[i].path = "<?= FE_BASE_URI ?>wap/campaigns/active20180110/images/pic_giftlist_" + savePath + ".png"
                                }
                                that.ifShowgift = !that.ifShowgift;
                                $('body').on('touchmove', eventTarget, false);
                                $("body,html").css({"overflow": "hidden"});
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            /*错误信息处理*/
                        }
                    });
                },
                //关闭获奖列表
                closeAward: function () {
                    this.ifShowgift = !this.ifShowgift;
                    $('body').unbind('touchmove');
                    $("body,html").css({"overflow": "auto"});
                }
            }
        })

        $(".pop-goinvest-close").click(function () {
            $(".mask1,.pop-goinvest").hide();
            $('body').unbind('touchmove');
            $("body,html").css({"overflow": "auto"});
        });


    });


    function eventTarget(event) {
        event.preventDefault();
    }


</script>