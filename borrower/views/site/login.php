<?php

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = '企业登录 - 温都金服';

?>
<style>
    .login {
        position: absolute;
        top: 0;
        padding: 0 10px;
        height: 40px;
        font-size: 22px;
        color: #f44336;
        border-bottom: 1px solid #f44336;
        left: 23%;
    }
</style>

<div class="container">
    <div id="login-box">
        <div class="login-tabs">
            <div class="login"><center>企业用户登录入口</center></div>
        </div>

        <?php $form = ActiveForm::begin(['action' => '/site/login']); ?>
        <?= $form->field($model, 'username', ['template' => '{input}{error}'])->textInput(['class' => 'form-control input-lg', 'placeholder' => '请输入企业账户']); ?>
        <?= $form->field($model, 'password', ['template' => '{input}{error}'])->passwordInput(['class' => 'form-control input-lg', 'placeholder' => '请输入密码']); ?>

        <?php if ($showCaptcha) { ?>
            <div style="float: left">
                <input class="form-control input-lg" style="width: 240px;" type="text" id="verifycode" placeholder="请输入验证码" name="LoginForm[verifyCode]" maxlength="4" >
            </div>
            <div style="float: right">
                <?=
                    $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                        'template' => '{image}', 'captchaAction' => '/site/captcha'
                    ])
                ?>
            </div>
        <?php } ?>

        <input class="btn btn-primary btn-lg btn-block" name="start" type="submit" value="登录">
        <?php ActiveForm::end(); ?>
    </div>
</div>
