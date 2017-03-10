<?php

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = '注册';
$this->params['breadcrumbs'][] = $this->title;

?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/loginsign.css">
<script  src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>wap/luodiye/js/luodiye.js?v=20170310"></script>

<div class="row kongxi">
    <?php
        $actionUrl = '/site/signup';
        if (!empty($next)) {
            $actionUrl .= '?next='.urlencode($next);
        }
    ?>
    <?php $form = ActiveForm::begin(['id' => 'signup_form', 'action' => $actionUrl]); ?>
        <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <input id="iphone" class="login-info" name="SignupForm[phone]" maxlength="11" type="tel" placeholder="请输入手机号">
        <div class="row sm-height border-bottom" style="line-height: 47px;">
            <div class="col-xs-8 col">
                <input id="captchaform-captchacode" class="login-info" type="text" name="SignupForm[captchaCode]" maxlength="4" placeholder="请输入图形验证码" autocomplete="off">
            </div>
            <div class="col-xs-4 yz-code text-align-rg col" style="height:51px;background: #fff; overflow: hidden;" >
                <?=
                    $form->field($model, 'captchaCode', [
                        'inputOptions' => [
                            'style' => 'height: 40px',
                        ]])
                        ->label(false)
                        ->widget(Captcha::className(), [
                            'template' => '{image}', 'captchaAction' => '/site/captcha',
                        ])
                ?>
            </div>
        </div>

        <div class=" position">
            <input id="yanzhengma" class="login-info" name="SignupForm[sms]" maxlength="6" type="tel" placeholder="请输入短信验证码" autocomplete="off">
            <input id="yzm" class="yzm yzm-normal" name="yzm" value="获取验证码" type="button">
        </div>
        <div class="col-xs-9 col">
            <input id="pass" class="login-info" name="SignupForm[password]" maxlength="20" type="password" placeholder="请输入6到20位的密码" autocomplete="off">
        </div>
        <div class="col-xs-3 col border-bottom login-eye password">
            <img src="<?= ASSETS_BASE_URI ?>images/eye-close.png" width="26" height="20" alt=" 闭眼">
        </div>
        <div class="col-xs-12 form-bottom">
            <input id="xieyi" class="xieyi lf" type="checkbox" checked="checked"/>
            <div class="div-xieyi"> 我已经阅读并同意<a href="/site/xieyi">《网站服务协议》</a></div>
        </div>
        <div class="col-xs-3"></div>
        <div class="col-xs-6 login-sign-btn">
            <input id="signup-btn" class="btn-common btn-normal" type="submit" value="注册">
        </div>
        <div class="col-xs-3"></div>
        <div class="col-xs-12" style="text-align: center;"><p>已有账号 <a href="/site/login" style="color: #f44336;">登录</a></p></div>
    <?php $form->end(); ?>
</div>

<script>
    $(function () {
        $(".field-captchaform-captchacode").css("float", "none");
    })
</script>
<!-- 注册页 end  -->
