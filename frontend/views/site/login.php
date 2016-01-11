<?php

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

?>

<div class="container">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <?php $form = ActiveForm::begin(['id' => 'login', 'action' => "/site/login",]); ?>
                <div class="login-tabs">
                    <a class="login current" href="#">登录</a>
                    <a class="reg" href="#">注册</a>
                </div>

                <?= $form->field($model, 'phone', ['template' => '{input}{error}'])->textInput(); ?>

                <?= $form->field($model, 'password', ['template' => '{input}{error}'])->textInput(); ?>

                <?php if ($is_flag) { ?>
                    <input name="is_flag" type="hidden" value="<?= $is_flag ?>">
                    <input class="login-info" type="text" id="verifycode" placeholder="请输入验证码" name="LoginForm[verifyCode]" maxlength="6" >
                    <?=
                    $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                        'template' => '{image}', 'captchaAction' => '/site/captcha'
                    ])
                    ?>
                <?php } ?>

                <input id="login-btn" class="btn btn-primary btn-lg btn-block" name="start" type="submit" value="登录">
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
