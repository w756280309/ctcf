<style>
    .popUp{display: none;}
</style>
<link rel="stylesheet" href="/css/base.css"/>
<link rel="stylesheet" href="/css/login/login_pop.css"/>
<script src="/js/jquery-1.8.3.min.js"></script>
<script src="/js/login/login_pop.js"></script>
<div class="login-mark"></div>
<div class="loginUp-box">
    <h3 class="loginUp-top">登录 <img class="close" src="../../images/login/close.png" alt=""></h3>
    <div class="loginUp-inner">
        <div class="phone-box">
            <label for="phone">手机号码</label>
            <input id="phone" class="error-border" type="tel" name="phone" maxlength="11" placeholder="请输入手机号码" required>
            <div style="clear: both"></div>
            <div class="popUp">手机号码应该包含11个字符</div>
        </div>
        <div class="password-box">
            <label for="password">登录密码</label>
            <input id="password" name="password" type="password" maxlength="20" placeholder="请输入密码" required>
            <div style="clear: both"></div>
            <div class="popUp">手机号码应该包含11个字符</div>
        </div>
        <div class="verity-box" id="login_verity" style="display: none">
            <label>图形验证码</label>
            <input type="text" id="verity" maxlength="4" placeholder="请输入图形验证码">
            <img src="" class="verity-img" alt="">
            <div style="clear: both"></div>
            <div class="popUp">手机号码应该包含11个字符</div>
        </div>
        <!--<div class="loginUp-bottom">
            <div class="loginUp-bottom-right">
                <i><a class="mima" href="">忘记密码</a></i>
            </div>
        </div>-->
        <input type="button" class="loginUp-btn" value="立即登录">
        <div class="resign-btn">没有账号？<a class="resign" href="">免费注册</a></div>
    </div>
</div>
