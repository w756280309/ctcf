<?php
$this->title = '企业登录 - 温都金服';

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
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

        <?php $form = ActiveForm::begin(['id' => 'login', 'action' => "/site/login",]); ?>
        <?= $form->field($model, 'username', ['template' => '{input}{error}'])->textInput(['class' => 'form-control input-lg', 'placeholder' => '请输入企业账户']); ?>

        <?= $form->field($model, 'password', ['template' => '{input}{error}'])->passwordInput(['class' => 'form-control input-lg', 'placeholder' => '请输入密码']); ?>

        <?php if ($is_flag) { ?>
            <div style="float: left">
                <input name="is_flag" type="hidden" value="<?= $is_flag ?>">
                <input class="form-control input-lg" style="width: 240px;" type="text" id="verifycode" placeholder="请输入验证码" name="LoginForm[verifyCode]" maxlength="6" >
            </div>
            <div style="float: right">
                <?=
                $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                    'template' => '{image}', 'captchaAction' => '/site/captcha'
                ])
                ?>
            </div>
        <?php } ?>

        <input id="login-btn" class="btn btn-primary btn-lg btn-block" name="start" type="submit" value="登录">
        <?php ActiveForm::end(); ?>
    </div>
</div>
