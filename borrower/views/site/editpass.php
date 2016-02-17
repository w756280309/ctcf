<?php
$this->title = '修改登陆密码 - 温都金服';

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\web\View;

$_js = <<<'JS'
$(function() {
    var $form = $('#editpass');
    $form.on('beforeValidate', function() {
        var verifycode = $('#verifycode').val();
        if (verifycode === '') {
            alert("请填写图形验证码");
            return false;
        }

        return true;
    });
})
JS;

$this->registerJs($_js, View::POS_END, 'body_close');
?>
<style>
    .login {
        position: absolute;
        top: 0;
        padding: 0 10px;
        height: 40px;
        font-size: 20px;
        color: #f44336;
        border-bottom: 1px solid #f44336;
        left: 18%;
    }
</style>

<div class="container">
    <div id="login-box">
        <div class="login-tabs">
            <div class="login"><center>首次登录,需重置登录密码</center></div>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'editpass', 'action' => "/site/editpass",]); ?>
        <?= $form->field($model, 'password', ['template' => '{input}{error}'])->passwordInput(['class' => 'form-control input-lg', 'placeholder' => '请输入原密码']); ?>

        <?= $form->field($model, 'new_pass', ['template' => '{input}{error}'])->passwordInput(['class' => 'form-control input-lg', 'placeholder' => '请输入6-20个字母与数字']); ?>

        <div style="float: left">
            <input class="form-control input-lg" style="width: 240px;" type="text" id="verifycode" placeholder="请输入验证码" name="EditpassForm[verifyCode]" maxlength="6" >
        </div>
        <div style="float: right">
            <?=
            $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                'template' => '{image}', 'captchaAction' => '/site/captcha'
            ])
            ?>
        </div>

        <input id="login-btn" class="btn btn-primary btn-lg btn-block" name="start" type="submit" value="重置">
        <?php ActiveForm::end(); ?>
    </div>
</div>
