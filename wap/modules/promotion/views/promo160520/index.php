<?php
$this->title = '520';
?>
<link href="<?= ASSETS_BASE_URI ?>css/520.css" rel="stylesheet"  type="text/css">

<div class="container">
    <div class="icon">
        <img src="<?= ASSETS_BASE_URI ?>promo/160520/images/img_520_1.png?v=1.0" alt="广告图">
        <img src="<?= ASSETS_BASE_URI ?>promo/160520/images/img_520_2.png?v=1.0" alt="广告图">
        <img src="<?= ASSETS_BASE_URI ?>promo/160520/images/img_520_3.png?v=1.0" alt="广告图">
        <img src="<?= ASSETS_BASE_URI ?>promo/160520/images/img_520_4.png?v=1.0" alt="广告图">
        <p class="clear"></p>
    </div>
    <div class="title_box row">
        <div class="col-xs-2"></div>
        <div class="col-xs-8 title_cn">
            温都金服正式上线有金喜,理财代金券注册即抽
        </div>
        <div class="col-xs-2"></div>
    </div>
    <div class="icon">
        <img src="<?= ASSETS_BASE_URI ?>promo/160520/images/img_520_6.png?v=1.0" alt="广告图">
        <p class="clear"></p>
    </div>

    <div class="center_box row">
        <div class="col-xs-2"></div>
        <div class="col-xs-8 center_box_tip">
            <?php if (!$endFlag) { ?>
                <div id="no1">
                    <p class="tip1">温都金服起航有金喜，看看你能得多少？</p>
                    <input type="text" class="input_txt" id="phone" placeholder="请输人手机号" maxlength="11">
                    <input type="button" class="input_btn draw" value="立即抽奖">
                </div>

                <div id="no2">
                    <p class="tip2">恭喜您，豪中<span class="tip3">248元</span>！</p>
                    <p class="tip2">即刻起航财富新高度，我们的鼓励是真金白银</p>
                    <input type="button" class="input_btn blue_btn draw" value="再抽一次">
                    <input type="button" class="input_btn register" value="立即注册赚起来">
                </div>

                <div id="no3">
                    <p class="tip2">真出彩，抽中<span class="tip3">268元</span>！</p>
                    <p class="tip2">金光闪闪的红包,尽然这么多</p>
                    <input type="button" class="input_btn blue_btn draw" value="再抽一次">
                    <input type="button" class="input_btn register" value="立即注册赚起来">
                </div>

                <div id="no4">
                    <p class="tip2">棒极啦，<span class="tip3">288元</span>已擒获</p>
                    <p class="tip2">努力没白费,登顶最高奖</p>
                    <p class="tip2 tip4">已经记录您领取的最高奖,请立即注册</p>
                    <input type="button" class="input_btn register" value="立即注册赚起来">
                </div>

                <div id="no5">
                    <p class="tip2">真情回馈老用户</p>
                    <p class="tip2">惊喜奖励不含糊</p>
                    <p class="tip2 tip4">最高奖券<span class="tip3">288元</span>,已发放到您的账户</p>
                    <input type="button" class="input_btn login" value="登录查看">
                </div>

                <div id="no6">
                    <p class="tip2"></p>
                    <p class="tip2">您已领过,请用本手机号登录账户中心查看</p>
                    <p class="tip2"></p>
                    <input type="button" class="input_btn login" value="登录查看">
                </div>
            <?php } else { ?>
                <div id="no7">
                    <p class="tip2 tip5">我们等的花儿都谢了,您却在活动结束后才看</p>
                    <p class="tip2 tip5 ">请您取看看其他活动吧</p>
                    <input type="button" class="input_btn" id="index" value="回到首页">
                </div>
            <?php } ?>
        </div>
        <div class="col-xs-2"></div>
    </div>
    <div class="icon">
        <img src="<?= ASSETS_BASE_URI ?>promo/160520/images/img_520_8.png?v=1.0" alt="广告图">
        <img src="<?= ASSETS_BASE_URI ?>promo/160520/images/img_520_81.png?v=1.0" alt="广告图">
        <p class="clear"></p>
    </div>
    <div class="p_box row">
        <div class="col-xs-12">
            <p class="informations"><span class="one">1</span>活动时间：2016年5月20日至2016年6月10日;</p>
            <p class="informations"><span class="two">2</span>新老用户均可参与,领券和注册时手机号须一致；</p>
            <p class="informations"><span class="three">3</span>每笔投资限用一张券,使用时请参看代金券规则；</p>
            <p class="informations"><span class="four">4</span>领券后6月20日前未使用,代金券失效；</p>
            <p class="informations"><span class="five">5</span>代金券等同于现金,与投资本金一并计息，随项目还款时返还；</p>
            <p class="informations"><span class="six">6</span>法律许可范围内,本活动最终解释权归温都金服所有。</p>
        </div>
        <p class="clear"></p>
    </div>
    <p class="clear"></p>
    <div class="icon">
        <img src="<?= ASSETS_BASE_URI ?>promo/160520/images/img_520_83.png?v=1.0" alt="广告图">

        <div class="foot">理财非存款，产品有风险，投资需谨慎</div>
    </div>
</div>
<form></form>

<script>
    $(function() {
        $('#no2,#no3,#no4,#no5,#no6').hide();
        $('#no1').show();

        $('.draw').on('click',function() {
            var tel = $('#phone').val();

            //手机号不能为空
            if (tel === '') {
                toast('手机号不能为空');
                return;
            }

            //验证手机号
            var regphone = /^0?1[3|4|5|6|7|8][0-9]\d{8}$/;
            if (!regphone.test(tel)) {
                toast('手机号格式错误');
                return;
            }

            var xhr = $.get("/promotion/promo160520/draw", {mobile: tel});

            xhr.done(function(data) {
                if ('' === data.message) {
                    $('#no1, #no2, #no3, #no4').hide();
                    if (data.isNewUser) {
                        if (1 === data.prizeId) {
                            $('#no2').show();
                        } else if (2 === data.prizeId) {
                            $('#no3').show();
                        } else if (3 === data.prizeId) {
                            $('#no4').show();
                        }
                    } else {
                        $('#no5').show();
                    }
                } else {
                    toast(data.message);
                }
            });

            xhr.fail(function(jqXHR) {
                var errMsg = jqXHR.responseJSON && jqXHR.responseJSON.message
                    ? jqXHR.responseJSON.message
                    : '未知错误，请刷新重试或联系客服';

                if (400 === jqXHR.status) {
                    toast(errMsg);
                }

                if (500 === jqXHR.status) {
                    $('#no1').hide();
                    $('#no6').show();
                }
            });
        });

        //立即注册赚起来
        $('.register').on('click', function() {
            location.href = '/site/signup';
        });

        //登录查看按钮
        $('.login').on('click', function() {
            location.href = '/site/login';
        });

        //再抽一次(#blue_btn2)
        $('#blue_btn2').on('click',function(){
            $('#no2').hide();
            $('#no3').show();
        });

        //再抽一次(#blue_btn3)
        $('#blue_btn3').on('click',function(){
            $('#no3').hide();
            $('#no5').show();
        });

        //返回首页按钮
        $('#index').on('click',function(){
            location.href = '/';
        });
    })
</script>
