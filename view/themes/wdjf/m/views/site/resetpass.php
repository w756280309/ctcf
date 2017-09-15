<?php
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = '密码找回';
$this->params['breadcrumbs'][] = $this->title;
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/loginsign.css?v=20170906">

<div class="row kongxi">
    <?php $form = ActiveForm::begin(['id' => 'resetpass_form', 'action' => '/site/resetpass', 'options' => ['data-to' => '1']]); ?>
    <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
    <input id="iphone" class="login-info" name="SignupForm[phone]" maxlength="11" type="tel"
           placeholder="请输入手机号" AUTOCOMPLETE="off">
    <div class="row sm-height border-bottom">
        <div class="col-xs-8 col">
        <input id="captchaCode" class="login-info" type="text" name="SignupForm[captchaCode]" maxlength="4"
               placeholder="请输入图形验证码" AUTOCOMPLETE="off">
        </div>
        <div class="col-xs-4 yz-code text-align-rg col" style="height:51px;background: #fff; overflow: hidden;">
        <?= $form->field($model, 'captchaCode', ['inputOptions' => ['style' => 'height: 40px']])->label(false)->widget(Captcha::className(), [
                                        'template' => '{image}', 'captchaAction' => '/site/captcha',
                                        ]) ?>
        </div>
    </div>
    <div class=" position">
        <input id="yanzhengma" class="login-info" name="SignupForm[sms]" maxlength="6" type="tel"
               placeholder="请输入短信验证码" AUTOCOMPLETE="off">
        <input id="yzm" class="yzm yzm-normal" name="yzm" value="获取验证码" type="button">
    </div>
    <div class="col-xs-9 col">
        <input id="pass" class="login-info" name="SignupForm[password]" maxlength="20" type="password"
               placeholder="请输入6到20位的密码" AUTOCOMPLETE="off">
    </div>
    <div class="col-xs-3 col border-bottom login-eye">
        <img src="<?= ASSETS_BASE_URI ?>images/eye-close.png" width="26" height="20" alt=" 闭眼">
    </div>
    <div class="clear form-bottom">&nbsp;</div>
    <div class="col-xs-3"></div>
    <div class="col-xs-6 login-sign-btn">
        <input id="signup-btn" class="btn-common btn-normal" name="signUp" type="button" value="确认重置"
               onclick="subsignup()">
    </div>
    <div class="col-xs-3"></div>
    <?php $form->end(); ?>
</div>
<!-- 注册页 end  -->

<script>
    function subsignup()
    {
        $("#signup-btn").addClass("btn-press").removeClass("btn-normal");

        if ($('#iphone').val() === '') {
            toast('手机号不能为空');
            $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
            return false;
        }

        var tel = $('#iphone').val();
        reg = /^0?1[3|4|5|6|7|8][0-9]\d{8}$/;
        if (!reg.test(tel)) {
            toast('手机号格式错误');
            $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
            return false;
        }

        if ($('#pass').val() === '') {
            toast('密码不能为空');
            $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
            return false;
        }

        if ($('#pass').val().length < 6) {
            toast('密码长度最少6位');
            $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
            return false;
        }

        var reg = /(?!^\d+$)(?!^[a-zA-Z]+$)^[0-9a-zA-Z]{6,20}$/;
        if (!reg.test($('#pass').val())) {
            toast('新密码必须为数字和字母的组合');
            $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
            return false;
        }

        if ($("#captchaCode").val() === '') {
            toast('图形验证码不能为空');
            $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
            return false;
        }

        if ($("#captchaCode").val().length !== 4) {
            toast('图形验证码必须为4位字符');
            $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
            return false;
        }

        if ($('#yanzhengma').val() === '') {
            toast('手机验证码不能为空');
            $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
            return false;
        }

        if ($('#yanzhengma').val().length !== 6) {
            toast('手机验证码必须为6位字符');
            $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
            return false;
        }

        subForm("#resetpass_form", "#signup-btn");

        $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
    }

    $(function () {
        $('input.login-info').focus(function () {
            $(this).css("color", "#000");
        });

        $('input.login-info').blur(function () {
            $(this).css("color", "");
        });

        $(".login-eye img").on("click", function () {
            if ($("#pass").attr("type") == "password") {
                $("#pass").attr("type", "text");
                $(this).removeAttr("src", "<?= ASSETS_BASE_URI ?>images/eye-close.png");
                $(this).attr({src: "<?= ASSETS_BASE_URI ?>images/eye-open.png", alt: "eye-open"});
            } else {
                $("#pass").attr("type", "password");
                $(this).removeAttr("src", "<?= ASSETS_BASE_URI ?>images/eye-open.png");
                $(this).attr({src: "<?= ASSETS_BASE_URI ?>images/eye-close.png", alt: "eye-close"});
            }
        });

        //60秒倒计时
        var InterValObj; //timer变量，控制时间
        var curCount;//当前剩余秒数
        var count = 60; //间隔函数，1秒执行

        $('#yzm').bind('click', function () {
            if ($("#captchaCode").val() === '') {
                toast('图形验证码不能为空');
                $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
                return false;
            }
            if ($("#captchaCode").val().length !== 4) {
                toast('图形验证码必须为4位字符');
                $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
                return false;
            }
            createSms("#iphone", 2, "#captchaCode", function () {
                fun_timedown();
            });
        });

        function SetRemainTime() {
            if (curCount == 0) {
                window.clearInterval(InterValObj);//停止计时器
                $('#yzm').removeAttr("disabled");//启用按钮
                $('#yzm').removeClass("yzm-disabled");
                $("#yzm").val("重新发送");
            } else {
                $('#yzm').addClass('yzm-disabled');
                $('#yzm').attr('disabled', 'true');
                curCount--;
                $("#yzm").val(curCount + "s后重发");
            }
        }

        function fun_timedown() {
            curCount = count;
            $('#yzm').addClass("yzm-disabled");
            $("#yzm").attr("disabled", "true");
            $("#yzm").val(curCount + "s后重发");
            InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
        }
    });
</script>
