<?php

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\web\View;

$_js = <<<'JS'
$(function() {
    $('#login-box>.login-tabs>a').on('click', function(e) {
        e.preventDefault();

        var $this = $(this);
        if ($this.hasClass('current')) {
            return;
        }

        $this.addClass('current')
            .siblings().removeClass('current');

        if ($this.hasClass('login')) {
            $('#login-box>form').show();
            $('#login-box>.login-reg').hide();
        } else {
            $('#login-box>form').hide();
            $('#login-box>.login-reg').show();
        }
    });
});
JS;
$this->registerJs($_js, View::POS_END, 'body_close');

?>

<div class="container">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <div id="login-box">
                <div class="login-tabs">
                    <a class="login current" href="#">登录</a>
                    <a class="reg" href="#">注册</a>
                </div>

                <?php $form = ActiveForm::begin(['id' => 'login', 'action' => "/site/login",]); ?>
                    <?= $form->field($model, 'phone', ['template' => '{input}{error}'])->textInput(['class' => 'form-control input-lg', 'placeholder' => '请输入手机号']); ?>

                    <?= $form->field($model, 'password', ['template' => '{input}{error}'])->passwordInput(['class' => 'form-control input-lg', 'placeholder' => '请输入密码']); ?>

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

                <div class="login-reg" style="display: none;">
                    <div style="margin: 20px auto; width: 160px; height: 160px; background-color: #eee;"></div>
                    <p>PC端暂不提供注册<br>请扫描二维码访问手机网站进行注册</p>
                </div>
            </div>
        </div>
    </div>
</div>
