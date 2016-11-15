<?php

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = "温州报业传媒旗下理财平台";
$this->params['breadcrumbs'][] = $this->title;
$this->headerNavOn = true;

$this->registerCssFile(ASSETS_BASE_URI . 'css/first.css', ['depends' => 'wap\assets\WapAsset']);
$this->registerCssFile(ASSETS_BASE_URI . 'css/luodiye/luodiye.css?v=160802', ['depends' => 'wap\assets\WapAsset']);
$this->registerJsFile(ASSETS_BASE_URI . 'js/fastclick.js', ['depends' => 'wap\assets\WapAsset']);
?>
<div class="row banner-box">
    <div class="col-xs-12">
        <img src="<?= ASSETS_BASE_URI ?>images/luodiye/banner-top.png" alt="">
        <img src="<?= ASSETS_BASE_URI ?>images/luodiye/banner-bottom.png" alt="">
    </div>
</div>
<?php $form = ActiveForm::begin(['id' => 'signup_form', 'action' => '/site/signup']); ?>
<div class="row register-box">
    <h2>免费注册</h2>
    <div class="text-box">
        <input id="iphone" name="SignupForm[phone]" class="text-single login-info" maxlength="11" type="tel" placeholder="请输入手机号">
        <div class="clear"></div>
    </div>
    <div class="text-box">
        <input id="captchaform-captchacode" class="login-info text-single" type="text" name="SignupForm[captchaCode]" maxlength="4" placeholder="请输入图形验证码" AUTOCOMPLETE="off">
        <?= $form->field($captcha, 'captchaCode', ['template' => '{input}'])->label(false)->widget(Captcha::className(), ['template' => '{image}', 'imageOptions' => ['class' => 'varify-img'], 'captchaAction' => '/site/captcha']) ?>
        <div class="clear"></div>
    </div>
    <div class="text-box">
        <input id="yanzhengma" class="login-info text-single" name="SignupForm[sms]" maxlength="6" type="tel" placeholder="请输入短信验证码" AUTOCOMPLETE="off">
        <input id="yzm" class="yzm yzm-normal get-phonecode" name="yzm" value="获取验证码" type="button">
        <div class="clear"></div>
    </div>
    <div class="text-box password-box">
        <input id="pass" class="login-info text-single" name="SignupForm[password]" maxlength="20" type="password" placeholder="请输入6到20位的密码" AUTOCOMPLETE="off">
        <a class="eye-choose login-eye">
            <img width="26" height="20" alt="eye-close" src="<?= ASSETS_BASE_URI ?>images/eye-close.png">
        </a>
        <div class="clear"></div>
    </div>
    <div class="agreement">
        <table>
            <tr>
                <td><input type="checkbox" id="xieyi" class="xieyi lf" checked="checked"></td>
                <td>
                    <label class="agreement div-xieyi" for="xieyi">我已阅读并同意<a href="/site/xieyi" target="_blank">《网站服务协议》</a></label>
                </td>
            </tr>
        </table>
    </div>
    <input type="submit" id="signup-btn" class="register-submit btn-normal" value="立即注册">
    <p class="leave-login">已有账号？<a href="/site/login" target="_self">登录</a></p>
</div>
<?php $form->end(); ?>
<div class="row description-box">
    <p class="description-header"><span>什么是温都金服？</span></p>
    <p class="description-content">温州温都金融信息服务股份有限公司简称“温都金服”，隶属温州报业传媒旗下的理财平台。甄选各类金融机构、优质企业理财产品。提供银行级理财服务，保障用户资金安全，安享稳健高收益。</p>
</div>
<div class="row production-box">
    <p class="production-header">精品理财</p>
    <div class="licai-img">
        <div class="col-xs-6 licai-img">
            <a href="/deal/deal/index/">
                <img src="<?= ASSETS_BASE_URI ?>images/luodiye/production-left.png" alt="温盈金">
            </a>
        </div>
        <div class="col-xs-6 licai-img">
            <a href="/deal/deal/index/">
                <img src="<?= ASSETS_BASE_URI ?>images/luodiye/production-right.png" alt="温盈宝">
            </a>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="row choose-box">
    <h3>为什么选择温都金服？</h3>
    <div class="choose-content">
        <img src="<?= ASSETS_BASE_URI ?>images/luodiye/choose-top.png" alt="">
        <img src="<?= ASSETS_BASE_URI ?>images/luodiye/why-wdjf-new.png" alt="">
    </div>
</div>
<a class="link-last" href="/deal/deal/index/">立即赚钱</a>
<p class="danger-tip">理财非存款，产品有风险，投资须谨慎</p>
<div class="fixed-float">
    <img src="<?= ASSETS_BASE_URI ?>images/luodiye/fixed-float.png" alt="">
</div>
<div class="fixed-box">
    <div class="fixed-outside">
        <div class="fixed-opacity"><img src="<?= ASSETS_BASE_URI ?>images/luodiye/fixed-float.png" alt=""></div>
        <table class="fixed-content">
            <tr>
                <td colspan="3" class="table-img"><img src="<?= ASSETS_BASE_URI ?>images/luodiye/fixed-float.png" alt=""></td>
            </tr>
            <tr class="table-content">
                <td width="600"><p class="content-font">使用APP客户端，理财随时随地！</p></td>
                <td width="300"><a class="content-link" href="http://a.app.qq.com/o/simple.jsp?pkgname=com.wz.wenjf" target="_self">立即下载</a></td>
                <td width="200">
                    <a class="content-picture"><img src="<?= ASSETS_BASE_URI ?>images/luodiye/close-icon-height.png" alt=""></a>
                </td>
            </tr>
        </table>
    </div>
</div>
<script>
    function validateForm() {
        if ($('#iphone').val() === '') {
            toast('手机号不能为空');
            return false;
        }

        var tel = $('#iphone').val();
        reg = /^0?1[3|4|5|6|7|8][0-9]\d{8}$/;
        if (!reg.test(tel)) {
            toast('手机号格式错误');
            return false;
        }

        if ($("#captchaform-captchacode").val() === '') {
            toast('图形验证码不能为空');
            return false;
        }

        if ($("#captchaform-captchacode").val().length !== 4) {
            toast('图形验证码必须为4位字符');
            return false;
        }

        if ($('#yanzhengma').val() === '') {
            toast('短信验证码不能为空');
            return false;
        }

        if ($('#yanzhengma').val().length !== 6) {
            toast('手机验证码必须为6位字符');
            return false;
        }

        if ($('#pass').val() === '') {
            toast('密码不能为空');
            return false;
        }

        if ($('#pass').val().length < 6) {
            toast('密码长度最少6位');
            return false;
        }

        var reg = /[a-zA-Z]/;
        var reg2 = /[0-9]/;
        if (!(-1 === $('#pass').val().indexOf(' ') && reg.test($('#pass').val()) && reg2.test($('#pass').val()))) {
            toast('请至少输入字母与数字组合');
            return false;
        }

        if ($('#xieyi').attr('checked') !== 'checked') {
            toast('请查看用户注册协议');
            return false;
        }

        return true;
    }

    function signup() {
        var $form = $('#signup_form');
        $('#signup-btn').attr('disabled', true);

        var xhr = $.post(
            $form.attr('action'),
            $form.serialize()
        );

        xhr.done(function (data) {
            if (data.code) {
                if ('undefined' !== typeof data.tourl) {
                    toast(data.message, function () {
                        if ('undefined' !== typeof ga) {
                            ga('send', {
                                hitType: 'event',
                                eventCategory: 'reg',
                                eventAction: 'm',
                                hitCallback: function () {
                                    location.href = data.tourl;
                                }
                            });
                            setTimeout(function () {
                                location.href = data.tourl;
                            }, 1500);
                        } else {
                            location.href = data.tourl;
                        }
                    });
                } else if ('undefined' !== typeof data.message) {
                    toast(data.message);
                }
            }
            $('#signup-btn').attr('disabled', false);
        });

        xhr.fail(function () {
            $('#signup-btn').attr('disabled', false);
        });
    }

    $(function () {
        FastClick.attach(document.body);

        $(".field-captchaform-captchacode").css('float', 'left');
        $('.content-picture').on('click', function () {
            $(this).parents('.fixed-box').hide().siblings('.fixed-float').hide();
        });

        $('input.login-info').focus(function () {
            $(this).css("color", "#000");
        });

        $('input.login-info').blur(function () {
            $(this).css("color", "");
        });

        /* 提交表单 */
        $('#signup_form').submit(function (e) {
            e.preventDefault();

            if (!validateForm()) {
                return false;
            }

            signup();
        });

        $(".login-eye img").on("click", function () {
            if ($("#pass").attr("type") === "password") {
                $("#pass").attr("type", "text");
                $(this).removeAttr("src", "<?= ASSETS_BASE_URI ?>images/eye-close.png");
                $(this).attr({src: "<?= ASSETS_BASE_URI ?>images/eye-open.png", alt: "eye-open"});
            } else {
                $("#pass").attr("type", "password");
                $(this).removeAttr("src", "<?= ASSETS_BASE_URI ?>images/eye-open.png");
                $(this).attr({src: "<?= ASSETS_BASE_URI ?>images/eye-close.png", alt: "eye-close"});
            }
        });

        $('#xieyi').bind('click', function () {
            if ($(this).attr('checked') === 'checked') {
                $(this).attr('checked', false);
            } else {
                $(this).attr('checked', true);
            }
        });

        //60秒倒计时
        var InterValObj; //timer变量，控制时间
        var curCount;//当前剩余秒数
        var count = 60; //间隔函数，1秒执行
        $('#yzm').bind('click', function () {
            if ($("#captchaform-captchacode").val() === '') {
                toast('图形验证码不能为空');
                return false;
            }
            if ($("#captchaform-captchacode").val().length !== 4) {
                toast('图形验证码必须为4位字符');
                return false;
            }
            createSms("#iphone", 1, "#captchaform-captchacode", function () {
                fun_timedown();
            });
        });

        function SetRemainTime() {
            if (curCount === 0) {
                window.clearInterval(InterValObj);//停止计时器
                $('#yzm').removeAttr("disabled");//启用按钮
                $('#yzm').removeClass("yzm-disabled");
                $("#yzm").val("重新发送");
            } else {
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
