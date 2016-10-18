<?php
$this->title = '注册';

$this->registerCssFile(ASSETS_BASE_URI.'css/register/register.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/register/register.js?v=20160712', ['depends' => 'frontend\assets\FrontAsset']);

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
?>

<div class="register-box">
    <h3 class="register-top">注册</h3>
    <?php $form = ActiveForm::begin(['action' => "/site/signup", 'id' => 'form']); ?>
    <input type="hidden" id="sms-type" value="1">
        <div class="register-inner">
            <div class="phone-box">
                <label for="phone">手机号码</label>
                <input id="phone" type="tel" name="SignupForm[phone]" maxlength="11" placeholder="请输入手机号码">
                <div style="clear: both"></div>
                <div class="popUp phone-err"></div>
            </div>
            <div class="verity-box">
                <label>图形验证码</label>
                <input type="hidden" id="csrf" value="<?= Yii::$app->request->csrfToken ?>">
                <input id="verity" type="text" maxlength="4" placeholder="请输入图形验证码" AUTOCOMPLETE="off">
                <?= $form->field($captcha, 'captchaCode')->label(false)->widget(Captcha::className(), ['template' => '{image}', 'captchaAction' => '/site/captcha']) ?>
                <div style="clear: both"></div>
                <div class="popUp"></div>
            </div>
            <div class="ins-box">
                <label>短信验证码</label>
                <input id="sms" name="SignupForm[sms]" maxlength="6" type="tel" placeholder="请输入短信验证码" AUTOCOMPLETE="off">
                <div class="verity-ins">获取验证码</div>
                <div style="clear: both"></div>
                <div class="popUp sms-err"></div>
            </div>
            <div class="password-box">
                <label for="password">登录密码</label>
                <?php if (preg_match("/MSIE/", $_SERVER["HTTP_USER_AGENT"])) { ?>
                    <input id="password" name="SignupForm[password]" maxlength="20" type="password" placeholder="请输入6到20位的密码" AUTOCOMPLETE="off">
                <?php } else { ?>
                    <input id="password" name="SignupForm[password]" maxlength="20" onfocus="this.type='password'" placeholder="请输入6到20位的密码" AUTOCOMPLETE="off">
                <?php } ?>
                <div style="clear: both"></div>
                <div class="popUp password-err"></div>
            </div>
            <div class="resign-bottom">
                <div class="resign-check">
                    <input class="agree" type="checkbox" checked="checked"> 我已经阅读并同意
                </div>
                <a href="/site/xieyi" target="_blank">《网站服务协议》</a>
            </div>
            <div style="clear: both"></div>
            <div class="popUp xieyi-err">您未同意网站服务协议</div>
            <div class="resign-btn-box">
                <input type="button" class="resign-btn btn" id="submit" value="注册">
                <div class="login-btn">已有账号？<a href="/site/login">登录</a></div>
                <div style="clear: both"></div>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
    <div class="register-image">
        <img alt="" src="<?= ASSETS_BASE_URI ?>images/newbelongings.png">
    </div>
</div>
<div class="clear"></div>
