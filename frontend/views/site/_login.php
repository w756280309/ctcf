<style>
    .popUp{display: none;}
</style>
<link rel="stylesheet" href="/css/base.css"/>
<link rel="stylesheet" href="/css/login/login_pop.css"/>
<script src="/js/jquery-1.8.3.min.js"></script>
<script src="/js/login/login_pop.js"></script>
<div class="login-mark"></div>
<div class="loginUp-box">
    <form id="login" action="/site/dologin" method="post">
        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken; ?>">
        <h3 class="loginUp-top">登录 <img class="close" src="../../images/login/close.png" alt=""></h3>
        <div class="loginUp-inner">
            <div class="phone-box">
                <label for="phone">手机号码</label>
                <input id="phone" class="error-border" type="tel" maxlength="11" placeholder="请输入手机号码" required name="LoginForm[phone]" />
                <div style="clear: both"></div>
                <div class="popUp phone_err">手机号码应该包含11个字符</div>
            </div>
            <div class="password-box">
                <label for="password">登录密码</label>
                <input id="password" name="LoginForm[password]" type="password" maxlength="20" placeholder="请输入密码" required>
                <div style="clear: both"></div>
                <div class="popUp pass_err">手机号码应该包含11个字符</div>
            </div>
            <div class="verity-box" id="login_verity" style="display: none;">
                <label>图形验证码</label>
                <input type="text" id="verity" maxlength="4" placeholder="请输入图形验证码" name="LoginForm[verifyCode]"/>
                <img src="/site/captcha" class="verity-img" alt="" id="verity_img"/>
                <div style="clear: both"></div>
                <div class="popUp verity_err">手机号码应该包含11个字符</div>
            </div>
            <div class="loginUp-bottom">
                <div class="loginUp-bottom-right">
                    <i><a class="mima" href="">忘记密码</a></i>
                </div>
            </div>
            <span class="loginUp-btn" id="login_submit_button">立即登录</span>
            <div class="resign-btn">没有账号？<a class="resign" href="">免费注册</a></div>
        </div>
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
    function verify_phone() {
        var phone_err = $('.phone_err');
        if ('' == $('#phone').val()) {
            phone_err.show();
            phone_err.html('手机号不能为空');
            return false;
        }
        var reg = /^0?1[3|4|5|6|7|8][0-9]\d{8}$/;
        if (!reg.test($('#phone').val())) {
            phone_err.show();
            phone_err.html('手机号格式错误');
            return false;
        }
        phone_err.hide();
        phone_err.html();
        return true;
    }

    function verify_pass() {
        var pass_err = $('.pass_err');
        if ('' == $('#password').val()) {
            pass_err.show();
            pass_err.html('手机号格式错误');
            return false;
        }
        if (6 > $('#password').val().length) {
            pass_err.show();
            pass_err.html('密码长度最少6位');
            return false;
        }
        pass_err.hide();
        pass_err.html('');
        return true;
    }

    function verify_verity() {
        var verity_err = $('.verity_err');
        if ('' == $('#verity').val()) {
            verity_err.show();
            verity_err.html('验证码不能为空');
            return false;
        }
        if (4 !== $('#verity').val().length) {
            verity_err.show();
            verity_err.html('验证码长度必须为4位');
            return false;
        }
        verity_err.hide();
        verity_err.html('');
        return true;
    }

    function verify_form() {
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

    $(function () {
        mobile.bind("blur", function () {
            verify_phone();
        });
        password.bind("blur", function () {
            verify_pass();
        });

        var button = $('#login_submit_button');
        button.bind('click', function () {
            var res = verify_form();
            if (res) {
                button.attr('disabled', true);
                button.html('登录中...');
                $.post('/site/dologin', $('#login').serialize(), function (data) {
                    if (0 == data.code) {
                        location.reload();
                    } else if (data.code > 0) {
                        if (data.requiresCaptcha) {
                            $("#login_verity").show();
                            $("#verity_img").attr("src", "/site/captcha?" + Math.random());
                        }
                        if (1 == data.code) {
                            $('.phone_err').show();
                            $('.phone_err').html(data.message);
                        } else if (2 == data.code) {
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
