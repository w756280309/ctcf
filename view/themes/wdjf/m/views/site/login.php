<?php

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = '登录';

?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/loginsign.css?v=20160531">

<?php if (!empty($aff)) { ?>
    <div>
        <center><img class="fenxiao" src="<?= UPLOAD_BASE_URI.$aff->picPath ?>"></center>
    </div>
<?php } ?>

<div class="row kongxi">
    <?php $form = ActiveForm::begin(['id' => 'login', 'action' => "/site/login", 'options' => ['data-to'=>'1']]); ?>
    <input name="from" type="hidden" value="<?= $from ?>">
    <input id="iphone" class="login-info" name="LoginForm[phone]" maxlength="11" type="tel" placeholder="请输入手机号" autocomplete="off" >

    <div class="row sm-height">
        <div class="col">
            <input id="pass" class="login-info" name="LoginForm[password]" maxlength="20" type="password" placeholder="请输入密码" autocomplete="off" />
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

    <div class="form-bottom">&nbsp;</div>
    <div class="col-xs-3"></div>
    <div class="col-xs-6 login-sign-btn">
        <input id="login-btn" class="btn-common btn-normal" name="start" type="button" value="登录" >
    </div>
    <div class="col-xs-6 login-sign-btn reg_forget_area">
        <a  href="/site/signup" align="center" >注册账号</a>
        &emsp;
        |
        &emsp;
        <a href="/site/resetpass" align="center" >忘记密码</a>
    </div>
    <div class="col-xs-3"></div>

    <?php ActiveForm::end(); ?>
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
            var reg = /^0?1[3|4|5|6|7|8][0-9]\d{8}$/;
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

            subForm('#login', '#login-btn', function (data) {
                if (data.code > 0 && data.requiresCaptcha) {
                    if ($('.verify-div-box').hasClass('hide')) {
                        $('.verify-div-box').removeClass('hide');
                    }
                    $('#loginform-verifycode-image').attr('src', '/site/captcha?' + Math.random());
                }
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