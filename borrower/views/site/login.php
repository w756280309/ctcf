<?php

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

?>

<div class="container">
    <div id="login-box">
        <div class="login-tabs" style="text-align: center;">
            <a class="login current" href="#"><b>登录</b></a>
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
