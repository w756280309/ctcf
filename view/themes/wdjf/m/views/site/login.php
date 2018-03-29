<?php

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = '登录';

?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/loginsign.css?v=20160532">

<?php if (!empty($aff)) { ?>
    <div>
        <center><img class="fenxiao" src="<?= UPLOAD_BASE_URI.$aff->picPath ?>"></center>
    </div>
<?php } ?>

<div class="row kongxi">
    <?php $form = ActiveForm::begin(['id' => 'login', 'action' => "/site/login", 'options' => ['data-to'=>'1']]); ?>
    <input name="from" type="hidden" value="<?= urlencode($from) ?>">
    <input id="iphone" class="login-info" name="LoginForm[phone]" maxlength="11" type="tel" placeholder="请输入手机号" autocomplete="off" >

    <div class="row sm-height">
        <div class="col">
            <input id="pass" style="width: 75%;" class="login-info lf" name="" maxlength="16" type="password" placeholder="请输入密码" autocomplete="off" onfocus="this.type='password'"/>
            <input id="pass2" style="width: 75%;" class="login-info lf" name="LoginForm[password]" maxlength="16" type="hidden"/>
            <div class="col-xs-3 col border-bottom login-eye password lf">
                <img src="<?= ASSETS_BASE_URI ?>images/eye-close.png" width="26" height="20" alt=" 闭眼">
            </div>
        </div>
    </div>

    <div class="verify-div-box row sm-height border-bottom <?= $showCaptcha ? 'show' : 'hide' ?>">
        <div class="col-xs-8 col">
            <input class="login-info" type="text" id="verifycode" placeholder="请输入验证码" name="LoginForm[verifyCode]" maxlength="4" >
        </div>
        <div class="col-xs-4 yz-code text-align-rg col" style="height:51px;background: #fff; overflow: hidden;" >
            <?=
                $form->field($model, 'verifyCode', ['inputOptions' => ['style' => 'height: 40px']])
                    ->label(false)
                    ->widget(Captcha::className(), [
                        'template' => '{image}',
                        'captchaAction' => '/site/captcha',
                    ])
            ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <div class="form-bottom">&nbsp;</div>
    <div class="col-xs-3"></div>
    <div class="col-xs-6 login-sign-btn">
        <button id="login-btn" class="btn-common btn-normal" name="start" value="登录" >登录</button>
    </div>
    <div class="col-xs-6 login-sign-btn reg_forget_area">
        <a  href="/site/signup" align="center" >注册账号</a>
        &emsp;
        |
        &emsp;
        <a href="/site/resetpass" align="center" >忘记密码</a>
    </div>
    <div class="col-xs-3"></div>


</div>
<!-- 登录页 end  -->

<script>
    var csrf;
    var showCaptcha = '<?= $showCaptcha ?>';

    function verifyCode() {
        if ('' === $('#verifycode').val()) {
            toastCenter('验证码不能为空');
            $(this).removeClass('btn-press').addClass('btn-normal');
            return false;
        }
        if (4 !== $('#verifycode').val().length) {
            toastCenter('验证码长度必须为4位');
            $(this).removeClass('btn-press').addClass('btn-normal');
            return false;
        }
        return true;
    }

    $(function() {
        $('.password img').on('click', function () {
            if ($('#pass').attr('type') === 'password') {
                $('#pass').attr('type', 'text');
                $(this).removeAttr('src', '/images/eye-close.png');
                $(this).attr({src: '/images/eye-open.png', alt: 'eye-open'});
            } else {
                $('#pass').attr('type', 'password');
                $(this).removeAttr('src', '/images/eye-open.png');
                $(this).attr({src: '/images/eye-close.png', alt: 'eye-close'});
            }
        });

        csrf = $('meta[name=csrf-token]').attr('content');
        $('#login-btn').on('click',function () {
            $(this).addClass('btn-press').removeClass('btn-normal');
            var tel = $('#iphone').val();
            if ('' === $('#iphone').val()) {
                toastCenter('手机号不能为空');
                $(this).removeClass('btn-press').addClass('btn-normal');
                return false;
            }
            if ('' === $('#pass').val()) {
                toastCenter('密码不能为空');
                $(this).removeClass('btn-press').addClass('btn-normal');
                return false;
            }
            var reg = /^0?1\d{10}$/;
            if (!reg.test(tel)) {
                toastCenter('手机号格式错误');
                $(this).removeClass('btn-press').addClass('btn-normal');
                return false;
            }

            if (showCaptcha) {
                var isVerified = verifyCode();
                if (!isVerified) {
                    return false;
                }
            }
            $('#pass2').val($('#pass').val());
            $('#pass').val('********');
            $('#pass').attr('type', 'text');

            var datas = {'phone':$('#iphone').val(),'bad':$('#pass2').val(),'verifyCode':$('#verifycode').val(),'sms':$('#yanzhengma').val()};
            var $btn = $('#login-btn');
            var to = $('#login').attr("data-to");//设置如果返回错误，是否需要跳转界面

            $btn.attr('disabled', true);
            $btn.removeClass("btn-normal").addClass("btn-press");
            var xhr = $.post($('#login').attr("action"), datas , function (data) {
                if (data.code != 0) {
                    $('#pass').val( $('#pass2').val());
                    $('#pass').attr('type', 'password');
                }
                if (data.code == '-1') {
                    alertTrue(function () {
                        location.href = '/user/user';
                    })
                } else if (data.code != 0 && to == 1 && data.tourl != undefined) {
                    toast(data.message, function() {
                        location.href = data.tourl;
                    });
                } else {
                    if (data.code != 0) {
                        toast(data.message);
                         if (data.code > 0 && data.requiresCaptcha) {
                             if ($('.verify-div-box').hasClass('hide')) {
                                 $('.verify-div-box').removeClass('hide');
                             }
                             $('#loginform-verifycode-image').attr('src', '/site/captcha?' + Math.random());
                         }
                    }
                    if (to == 1 && data.tourl != undefined) {
                        location.href = data.tourl;
                    }
                }

            });
            xhr.always(function () {
                $btn.removeClass("btn-press").addClass("btn-normal");
                $btn.attr('disabled', false);
            });
            $(this).removeClass('btn-press').addClass('btn-normal');
        });
        $('input.login-info').focus(function () {
            $(this).css('color', '#000');
        });
        $('input.login-info').blur(function () {
            $(this).css('color', '');
        });
    });
</script>