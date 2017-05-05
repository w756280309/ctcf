<?php

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
$this->title = '抽奖活动';
$this->share = $share;
$this->headerNavOn = true;

?>


<link rel="stylesheet" href="<?= FE_BASE_URI ?>libs/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=20170412">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/luodiye/css/index.css?v=2.7">
<script  src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script  src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/common.js"></script>
<script  src="<?= FE_BASE_URI ?>libs/jquery.lazyload.min.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/com.js"></script>


<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/Youth-day/css/index.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<style>
    #mobile {
        border: solid #8c8c8c 1px;
        padding: 0.2rem 0.2rem;
        line-height: 0.5rem;
    }
</style>

<div class="flex-content">
    <div class="part-one"></div>
    <div class="part-three">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/Youth-day/images/rules.png" alt="">
        <div class="rules-box">
            <ol class="rules-content">
                <?php
                if (!Yii::$app->user->isGuest) {
                ?>
                    <li>用户已登录：<?= $user->isIdVerified() ? "已经认证" : "未认证"?>；</li>
                <?php }?>
                <li>活动进度：<?= $jindu?>%；</li>
                <li>活动时间：2017年5月4日-2017年5月7日；</li>
                <li>本次活动面向所有温都金服注册用户；</li>
                <li>活动期间累计年化投资（不含转让产品）达到指定额度，即可获得相应礼品；</li>
            </ol>

            <ul class="prize-content">
                <li>
                    <div class="prize-value lf">累计年化金额（元）</div>
                    <div class="prize-name lf">礼品</div>
                    <div class="prize-point lf">对应积分</div>
                </li>
                <li>
                    <div class="prize-value lf">1,540,000</div>
                    <div class="prize-name lf">携程礼品卡1500元</div>
                    <div class="prize-point lf">30000</div>
                </li>
                <li>
                    <div class="prize-value lf">540,000</div>
                    <div class="prize-name lf">亚马逊kindle平板</div>
                    <div class="prize-point lf">10580</div>
                </li>
                <li>
                    <div class="prize-value lf">54,000</div>
                    <div class="prize-name lf">酷畅运动水杯</div>
                    <div class="prize-point lf">980</div>
                </li>
                <li>
                    <div class="prize-value lf">20,000</div>
                    <div class="prize-name lf">苹果安卓二合一数据线</div>
                    <div class="prize-point lf">198</div>
                </li>
            </ul>
            <ol class="rules-content" start="4" style="margin-top: 0.373rem;">
                <li>本次活动奖品将于3个工作日内以积分形式发放，用户可以进入积分商城进行兑换。</li>
            </ol>
        </div>
    </div>
    <div class="part-four">
        <?php
        //用户已经登录
        if (!Yii::$app->user->isGuest) {
        ?>
            <!--老用户显示邀请好友-->
            <?php
            //用户已经登录
            if ('new_user' !== $source) {
            ?>

                <p class="attention">老用户：判断老用户是否参与该活动；提示您已经参与该活动</p>

                <?php if (!$isZJ) { ?>
                     <a>非浙江号,弹框</a>
                <?php } else { ?>
                    <a href="/user/invite" class="button-invest">邀请好友</a>
                <?php } ?>

                <?php if ('inviter' === $source) { ?>
                    <a href="javascript:void(0)" onclick="subForm();">参与</a>
                <?php } ?>

            <?php } else { ?>

                <p class="attention">
                    新用户：<?= 1 == 1 ? "已经参与活动" : "为参与活动" ?>
                    <br/>判断新用户是否参与该活动；
                    <?= $user->isIdVerified() ? "已经认证" : "未认证"?>
                </p>

            <?php }?>
        <?php } else { ?>
            <a href="javascript:void(0)" onclick="subForm();">注册</a>
        <?php }?>
      </div>

</div>
<!--抽奖手机号-->
<div class="mask pop hide"></div>
<div class="bing-info pop hide" style="top: 30%;">
    <div class="bing-tishi">请输入领取的手机号码</div>
    <p class="tishi-p" style="margin: 20px auto !important;"><input class="f15" type="tel" maxlength="11" id="mobile"><span style="color: red;"></span></p>
    <div class="bind-btn"><span class="true">确定</span></div>
</div>
<script>
    function eventTarget(event) {
        event.preventDefault();
    }

    //只有确定按钮的弹窗
    function alertTrueVal() {
        $('.pop').removeClass('hide').addClass('show');
        $('body').on('touchmove', eventTarget, false);
        $('.bind-btn').on('click', function () {

        });
    }

    $(function() {
        FastClick.attach(document.body);
        $("img").lazyload({
            threshold : 200
        });

        $('#mobile').on('keyup',function() {
            var num = moduleFn.clearNonum($(this));
        });

       alertTrueVal();

        var allowClick = true;
        var key = '<?= $promo->key ?>';
        $('a.phonenum').on('click',function(e) {
            e.preventDefault();

            if (!allowClick) {
                return;
            }

            allowClick = false;

            if (!validateMobile()) {
                allowClick = true;
                return;
            }

            var phonenum =  $('#mobile').val();
            var xhr = $.get('/promotion/p1705/duobao');

            xhr.done(function(data) {
                var toUrl = data.toUrl;

                if (data.code) {
                    toastCenter(data.message, function() {
                        if ('' !== toUrl) {
                            location.href = toUrl;
                        }

                        allowClick = true;
                    });
                } else {
                    location.href = toUrl;
                    allowClick = true;
                }
            });

            xhr.fail(function () {
                toastCenter('系统繁忙,请稍后重试!', function() {
                    allowClick = true;
                });
            })
        })
    });

    function subForm() {

    }
</script>