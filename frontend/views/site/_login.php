<style>
    .popUp {
        display: none;
    }
</style>

<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/login/login_pop.css?v=20160711"/>

<div class="login-mark" style="display: none;"></div>
<div class="loginUp-box" style="display: none;">
    <form id="login" action="/site/dologin" method="post">
        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken; ?>">
        <h3 class="loginUp-top">登录 <img class="close" src="<?= ASSETS_BASE_URI ?>images/login/close.png" alt=""></h3>
        <div class="loginUp-inner">
            <div class="phone-box">
                <label for="phone">手机号码</label>
                <input id="phone" type="tel" maxlength="11" placeholder="请输入手机号码"  name="LoginForm[phone]" />
                <div style="clear: both"></div>
                <div class="popUp phone_err">手机号码应该包含11个字符</div>
            </div>
            <div class="password-box">
                <label for="password">登录密码</label>
                <input id="password" name="LoginForm[password]" type="password" maxlength="20" placeholder="请输入密码" autocomplete="off"/>
                <div style="clear: both"></div>
                <div class="popUp pass_err">密码不能为空</div>
            </div>
            <div class="verity-box" id="login_verity" style="display: none;">
                <label>图形验证码</label>
                <input type="text" id="verity" maxlength="4" placeholder="请输入图形验证码" name="LoginForm[verifyCode]" autocomplete="off"/>
                <img src="/site/captcha" class="verity-img" alt="" id="verity_img"/>
                <div style="clear: both"></div>
                <div class="popUp verity_err">验证码不能为空</div>
            </div>
            <div class="loginUp-bottom">
                <div class="loginUp-bottom-right">
                    <i><a class="mima" href="/site/resetpass">忘记密码</a></i>
                </div>
            </div>
            <span class="loginUp-btn" id="login_submit_button">立即登录</span>
            <div class="resign-btn">没有账号？<a class="resign" href="/site/signup">免费注册</a></div>
        </div>
        <input type="hidden" name="is_flag" value="<?= $requiresCaptcha ?>">
    </form>
</div>
<script>
    var requiresCaptcha = <?= $requiresCaptcha ? 1 : 0 ?>;
    var mobile = $('#phone');
    var password = $('#password');
    var verity = $('#verity');

    if (requiresCaptcha) {
        $("#login_verity").show();
    }

    function verify_phone()
    {
        var phone_err = $('.phone_err');
        if ('' == $('#phone').val()) {
            phone_err.show();
            phone_err.html('手机号不能为空');
            $('#phone').addClass('error-border');
            return false;
        }
        var reg = /^0?1[3|4|5|6|7|8][0-9]\d{8}$/;
        if (!reg.test($('#phone').val())) {
            phone_err.show();
            phone_err.html('手机号格式错误');
            $('#phone').addClass('error-border');
            return false;
        }
        phone_err.hide();
        phone_err.html();
        $('#phone').removeClass('error-border');
        return true;
    }

    function verify_pass()
    {
        var pass_err = $('.pass_err');
        if ('' == $('#password').val()) {
            pass_err.show();
            pass_err.html('密码不能为空');
            $('#password').addClass('error-border');
            return false;
        }
        if (6 > $('#password').val().length) {
            pass_err.show();
            pass_err.html('密码长度最少6位');
            $('#password').addClass('error-border');
            return false;
        }
        pass_err.hide();
        pass_err.html('');
        $('#password').removeClass('error-border');
        return true;
    }

    function verify_verity()
    {
        var verity_err = $('.verity_err');
        if ('' == $('#verity').val()) {
            verity_err.show();
            verity_err.html('验证码不能为空');
            $('#verity').addClass('error-border');
            return false;
        }
        if (4 !== $('#verity').val().length) {
            verity_err.show();
            verity_err.html('验证码长度必须为4位');
            $('#verity').addClass('error-border');
            return false;
        }
        verity_err.hide();
        verity_err.html('');
        $('#verity').removeClass('error-border');
        return true;
    }

    function verify_form()
    {
        var mobile_verify = verify_phone();
        var pass_verify = verify_pass();
        mobile.trigger('blur');
        password.trigger('blur');
        if (requiresCaptcha) {
            verity.trigger('blur');
            var verity_verify = verify_verity();
            return mobile_verify && pass_verify && verity_verify;
        } else {
            return mobile_verify && pass_verify;
        }
    }

    $("input").bind('keypress', function(e) {
        if (e.keyCode === 13) {
            $('#login_submit_button').click();
        }
    });

    $(function () {
        $('.loginUp-btn').hover(function(){
            $('.loginUp-btn').css({background:'#f41c11'});
        },function(){
            $('.loginUp-btn').css({background:'#f44336'});
        });

        $('.close').on('click',function(){
            $('.login-mark').fadeOut();
            $('.loginUp-box').fadeOut();
            $('.phone_err').hide().html('');
            $('.pass_err').hide().html('');
            $('.verity_err').hide().html('');
            $('#phone').val('').removeClass('error-border');
            $('#password').val('').removeClass('error-border');
            $('#verity').val('').removeClass('error-border');

            document.documentElement.style.overflow = 'auto';
        })

        //键盘按下ESC时关闭窗口!
        $(document).keydown(function(e) {
            if (e.keyCode === 27) {
                $('.close').click();
            }
        });

        mobile.bind("blur", function () {
            verify_phone();
        });

        password.bind("blur", function () {
            verify_pass();
        });

        $('#verity_img').click(function() {
            $(this).attr("src", "/site/captcha?" + Math.random());
        });

        var button = $('#login_submit_button');
        button.bind('click', function () {
            var res = verify_form();
            if (res) {
                button.attr('disabled', true);
                button.html('登录中...');
                $.post('/site/dologin', $('#login').serialize(), function (data) {
                    if (0 == data.code) {
                        if (typeof(callbackOnLogin) != 'undefined') {
                            callbackOnLogin();
                            return false;
                        }
                        location.reload();
                    } else if (data.code > 0) {
                        if (data.requiresCaptcha) {
                            $("#login_verity").show();
                            $("#verity_img").attr("src", "/site/captcha?" + Math.random());
                        }
                        if (1 == data.code || 2 == data.code) {
                            $('.pass_err').show();
                            $('.pass_err').html(data.message);
                        } else if (3 == data.code) {
                            $('.verity_err').show();
                            $('.verity_err').html(data.message);
                        }
                    }
                    button.removeAttr('disabled');
                    button.html('立即登录');
                });
            }
        });
    })
</script>