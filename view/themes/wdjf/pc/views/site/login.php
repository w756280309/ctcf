<?php

$this->title = '登录';

?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/login/login.css">
<script src="<?= ASSETS_BASE_URI ?>js/login/login.js"></script>

<div class="login-box">
    <div class="login-content">
        <div class="login-inner">
            <div class="login-ad">
                <img alt="" src="<?= ASSETS_BASE_URI ?>images/fiveadvantage.jpg">
            </div>
            <div class="login-right">
                <h3>登录</h3>
                <form id="login" action="/site/dologin" method="post">
                    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken(); ?>">
                    <div class="login-right-box">
                        <div class="phone-box">
                            <label for="phone">手机号码</label>
                            <input id="phone" type="tel" name="LoginForm[phone]" maxlength="11" placeholder="请输入手机号码" tabindex="1">
                            <div style="clear: both"></div>
                            <div class="popUp"></div>
                        </div>
                        <div class="password-box">
                            <label for="password">登录密码</label>
                            <input id="password" name="LoginForm[password]" type="password" maxlength="20" placeholder="请输入密码" autocomplete="off" tabindex="2">
                            <div style="clear: both"></div>
                            <div class="popUp"></div>
                        </div>

                        <div class="verity-box verity-box_none">
                            <label>图形验证码</label>
                            <input type="text" id="verity" maxlength="4" placeholder="请输入图形验证码" autocomplete="off" name="LoginForm[verifyCode]">
                            <img src="/site/captcha" class="verity-img" alt="">
                            <div style="clear: both"></div>
                            <div class="popUp"></div>
                        </div>

                        <div class="login-bottom">
                            <div class="login-check">
                                <input type="hidden" class="agree" name="agree" value="no">
                                <span>
                                     <input type="checkbox" name="remember" tabindex="3">
                                </span>
                                记住用户名
                            </div>
                            <div class="login-bottom-right">
                                <i><a class="mima" href="/site/resetpass">忘记密码</a></i>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="next" value="<?= $next ?>">
                    <input type="button" class="login-btn" id="login-btn" value="立即登录">
                    <div class="resign-btn">没有账号？<a class="resign" href="/site/signup">免费注册</a></div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>

<script>
    var requiresCaptcha = '<?= $requiresCaptcha ?>';
    var error_status = false;
    $("input").on('keypress', function(e) {
        if (e.keyCode === 13) {
            $('#login-btn').click();
        }
    });

    $(function() {
        checkCookie();
        if (requiresCaptcha) {
            $(".verity-box").show();
            $("#verity").attr("tabindex", "3")
            $("input[name='remember']").attr("tabindex", 4);
        }
        function errorInput(obj, content) {
            error_status = true;
            obj.html(content);
        }

        $(".verity-img").on("click", function() {
            $(this).attr("src", "/site/captcha?"+Math.random());
        });

        $("#phone").on("blur", function() {
            if('' == $(this).val()){
                errorInput($(".phone-box .popUp"), '手机号不能为空');
                $(this).addClass("error-border");
                return false;
            }

            var reg = /^0?1\d{10}$/;
            if (!reg.test($(this).val())) {
                errorInput($(".phone-box .popUp"), '手机号格式错误');
                $(this).addClass("error-border");
                return false;
            }
            $(".phone-box .popUp").html('');
            $(this).removeClass("error-border");
        });

        $("#password").on("blur", function() {
            if ('' == $(this).val()) {
                errorInput($(".password-box .popUp"), '密码不能为空');
                $(this).addClass("error-border");
                return false;
            }
            $(".password-box .popUp").html('');
            $(this).removeClass("error-border");
        });

        if (requiresCaptcha) {
            $("#verity").on("blur", function() {
                if ('' == $(this).val()) {
                    errorInput($(".verity-box .popUp"), '验证码不能为空');
                    $(this).addClass("error-border");
                    return false;
                }
                if (4 !== $(this).val().length) {
                    errorInput($(".verity-box .popUp"), '验证码长度必须为4位');
                    $(this).addClass("error-border");
                    return false;
                }
                $(".verity-box .popUp").html('');
                $(this).removeClass("error-border");
            })
        }

        $('#login-btn').click(function(e) {
            e.preventDefault();

            error_status = false;
            $("#phone").trigger("blur");

            if (error_status) {
                error_status = false;
                return false;
            }

            $("#password").trigger("blur");

            if (error_status) {
                error_status = false;
                return false;
            }

            if (requiresCaptcha) {
                $("#verity").trigger("blur");
                if (error_status) {
                    error_status = false;
                    return false;
                }
            }

            submitForm("#login", "#login-btn");
        });

        function submitForm(form, button) {
            var $btn = $(button);
            var vals = $(form).serialize();

            $btn.attr('disabled', true);
            var xhr = $.post($(form).attr("action"), vals, function (data) {
                if (0 == data.code) {
                    location.href = data.tourl;
                } else if (data.code > 0) {
                    if (data.requiresCaptcha) {
                        $(".verity-box").show();
                        $("#verity").attr("tabindex", "3");
                        $("input[name='remember']").attr("tabindex", 4);
                        $(".verity-img").attr("src", "/site/captcha?"+Math.random());
                    }
                    if (1 == data.code || 2 == data.code) {
                        $(".password-box .popUp").html(data.message);
                    } else if (3 == data.code) {
                        $(".verity-box .popUp").html(data.message);
                    }
                }
            });
            xhr.always(function () {
                $btn.attr('disabled', false);
            });
        }

        function getCookie(c_name)
        {
            if (document.cookie.length > 0)
            {
                c_start=document.cookie.indexOf(c_name + "=");
                if (c_start != -1)
                {
                    c_start = c_start + c_name.length + 1;
                    c_end=document.cookie.indexOf(";",c_start);
                    if (c_end==-1) c_end=document.cookie.length;
                    return unescape(document.cookie.substring(c_start,c_end));
                }
            }
            return ""
        }

        function checkCookie()
        {
            userphone = getCookie('userphone');
            if (userphone != null && userphone != "") {
                $("#phone").val(userphone);
            }
        }
    })
</script>