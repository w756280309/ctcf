﻿<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
frontend\assets\WapAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport"
              content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
        <meta name="renderer" content="webkit">
        <title><?= Html::encode($this->title) ?></title>
        <?= Html::csrfMetaTags() ?>
        <?php $this->head() ?>
        <script type="text/javascript" src="/js/jquery.js"></script>
        <script type="text/javascript" src="/js/common.js"></script>
        <link rel="stylesheet" href="/css/base.css">
        <link rel="stylesheet" href="/css/loginsign.css">
    </head>
    <body>
    <?php $this->beginBody() ?>
    <!--  注册页  start-->
    <div class="container">
        <div class="row nav-height">
            <div class="col-xs-2 back"><img src="/images/back.png" alt=""/></div>
            <div class="col-xs-8 title">注册</div>
            <div class="col-xs-2"></div>
        </div>
        <div class="row">
            <?php $form = ActiveForm::begin(['id' => 'signup_form', 'action' => '/site/signup', 'options' => ['data-to' => '1']]); ?>
                <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                <input id="iphone" class="login-info" name="SignupForm[phone]" maxlength="11" type="tel"
                       placeholder="请输入手机号">
                <div class="row sm-height border-bottom">
                    <div class="col-xs-8 col">
                    <input id="captchaCode" class="login-info" name="SignupForm[captchaCode]" maxlength="4"
                           placeholder="输入图形验证码" AUTOCOMPLETE="off">
                    </div>
                    <div class="col-xs-4 yz-code text-align-rg col" style="height:51px;background: #fff; overflow: hidden;" >
                    <?= $form->field($model, 'captchaCode', ['inputOptions' => ['style' => 'height: 40px']])->label(false)->widget(Captcha::className(), [
                                                    'template' => '{image}', 'captchaAction' => '/site/captcha',
                                                    ]) ?>
                    </div>
                </div>

                <div class=" position">
                    <input id="yanzhengma" class="login-info" name="SignupForm[sms]" maxlength="6" type="tel"
                           placeholder="输入短信验证码" AUTOCOMPLETE="off">
                    <input id="yzm" class="yzm yzm-normal" name="yzm" value="获取验证码" type="button">
                </div>
                <div class="col-xs-9 col">
                    <input id="pass" class="login-info" name="SignupForm[password]" maxlength="20" type="password"
                           placeholder="6到20字母与数字组合或符号" AUTOCOMPLETE="off">
                </div>
                <div class="col-xs-3 col border-bottom login-eye">
                    <img src="/images/eye-close.png" width="26" height="20" alt=" 闭眼">
                </div>
                <div class="col-xs-12 div-xieyi">
                    <input id="xieyi" class="xieyi lf" type="checkbox" checked="checked"/> 我已经阅读并同意
                    <a href="/site/xieyi" class="xieyi">《用户注册协议》</a>
                </div>
                <div class="col-xs-3"></div>
                <div class="col-xs-6 login-sign-btn">
                    <input id="signup-btn" class="btn-common btn-normal" name="signUp" type="button" value="注册"
                           onclick="subsignup()">
                </div>
                <div class="col-xs-3"></div>
            <?php $form->end(); ?>
        </div>
        <!-- 注册页 end  -->
        <!-- 输入弹出框 start  -->
        <div class="error-info hide">您输入的密码不正确</div>
        <!-- 输入弹出框 end  -->
    </div>
    <script>
        function subsignup() {
            $("#signup-btn").addClass("btn-press").removeClass("btn-normal");
            if ($('#iphone').val() == '') {
                toast(this, '手机号不能为空');
                $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
                return false;
            }
            var reg = /[a-zA-Z]/;
            var reg2 = /[0-9]/;
            if (!(-1 === $('#pass').val().indexOf(' ') && reg.test($('#pass').val()) && reg2.test($('#pass').val()))) {
                toast(this, '请至少输入字母与数字组合');
                $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
                return false;
            }
            if ($('#pass').val() == '') {
                toast(this, '密码不能为空');
                $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
                return false;
            }
            if ($("#captchaCode").val() === '') {
                toast(this, '图形验证码不能为空');
                $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
                return false;
            }
            if ($("#captchaCode").val().length !== 4) {
                toast(this, '图形验证码必须为4位字符');
                $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
                return false;
            }
            if ($('#yanzhengma').val() == '') {
                toast(this, '手机验证码不能为空');
                $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
                return false;
            }
            if ($('#yanzhengma').val().length != 6) {
                toast(this, '手机验证码必须为6位字符');
                $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
                return false;
            }
            var tel = $('#iphone').val();
            reg = /^0?1[3|4|5|6|8][0-9]\d{8}$/;
            if (!reg.test(tel)) {
                toast(this, '手机号格式错误');
                $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
                return false;
            }
            if ($('#pass').val().length < 6) {
                toast(this, '密码长度最少6位');
                $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
                return false;
            }
            if ($('#xieyi').attr('checked') != 'checked') {
                toast(this, '请查看用户注册协议');
                $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
                return false;
            }
            subForm("#signup_form", "#signup-btn");
            $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
        }
        $(function () {
            $('.back img').bind('click', function () {
                history.go(-1);
            });
            $('input.login-info').focus(function () {
                $(this).css("color", "#000");
            });
            $('input.login-info').blur(function () {
                $(this).css("color", "");
                var loginInfo = $(this).val();
                if (loginInfo == '') {
                }
            });

            $(".login-eye img").on("click", function () {
                if ($("#pass").attr("type") == "password") {
                    $("#pass").attr("type", "text");
                    $(this).removeAttr("src", "/images/eye-close.png");
                    $(this).attr({src: "/images/eye-open.png", alt: "eye-open"});
                } else {
                    $("#pass").attr("type", "password");
                    $(this).removeAttr("src", "/images/eye-open.png");
                    $(this).attr({src: "/images/eye-close.png", alt: "eye-close"});
                }
            });
            $('#xieyi').bind('click', function ()
            {
                if ($(this).attr('checked') == 'checked') {
                    $(this).attr('checked', false);
                } else {
                    $(this).attr('checked', true);
                }
            });
            //60秒倒计时
            var InterValObj; //timer变量，控制时间
            var curCount;//当前剩余秒数
            var count = 60; //间隔函数，1秒执行

            $('#yzm').bind('click', function ()
            {
                if ($("#captchaCode").val() === '') {
                    toast(this, '图形验证码不能为空');
                    $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
                    return false;
                }
                if ($("#captchaCode").val().length !== 4) {
                    toast(this, '图形验证码必须为4位字符');
                    $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
                    return false;
                }
                createSms("#iphone", false, "#captchaCode", function ()
                {
                    fun_timedown();
                });
            });
            function SetRemainTime()
            {
                if (curCount == 0) {
                    window.clearInterval(InterValObj);//停止计时器
                    $('#yzm').removeAttr("disabled");//启用按钮
                    $('#yzm').removeClass("yzm-disabled");
                    $("#yzm").val("重新发送");
                } else {
                    curCount--;
                    $("#yzm").val(curCount + "s后重发");
                }
            }
            function fun_timedown()
            {
                curCount = count;
                $('#yzm').addClass("yzm-disabled");
                $("#yzm").attr("disabled", "true");
                $("#yzm").val(curCount + "s后重发");
                InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
            }
        });
    </script>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>
