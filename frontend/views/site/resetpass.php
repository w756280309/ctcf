<?php
$this->title = '找回密码';

$this->registerCssFile(ASSETS_BASE_URI.'css/register/register.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/register/register.js', ['depends' => 'frontend\assets\FrontAsset']);

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
?>

<div class="register-box">
    <h3 class="register-top">找回密码</h3>
    <?php $form = ActiveForm::begin(['action' => "/site/resetpass", 'id' => 'form']); ?>
    <input type="hidden" id="sms-type" value="2">
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
                <?= $form->field($captcha, 'captchaCode')->label(false)->widget(Captcha::className(), ['template' => '<div class="verity-img">{image}</div>', 'captchaAction' => '/site/captcha']) ?>
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
                <label for="password">新登录密码</label>
                <input id="password" name="SignupForm[password]" maxlength="20" type="password" placeholder="请输入6到20位的密码" AUTOCOMPLETE="off">
                <div style="clear: both"></div>
                <div class="popUp password-err"></div>
            </div>
            <input class="agree" type="hidden" checked="checked">
            <div class="resign-btn-box">
                <input type="submit" class="resign-btn" value="确认重置">
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>
<div class="clear"></div>