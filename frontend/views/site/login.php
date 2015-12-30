<?php
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
    <body>
        <?php $this->beginBody() ?>
        <?php $form = ActiveForm::begin(['id' => 'login', 'action' => "/site/login",]); ?>
        <?=
        $form->field($model, 'phone', ['template' => '{input}{error}'])->textInput();
        ?>

        <?=
        $form->field($model, 'password', ['template' => '{input}{error}'])->textInput();
        ?>

        <?php if ($is_flag) { ?>
            <input name="is_flag" type="hidden" value="<?= $is_flag ?>">
            <input class="login-info" type="text" id="verifycode" placeholder="请输入验证码" name="LoginForm[verifyCode]" maxlength="6" >
            <?=
            $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                'template' => '{image}', 'captchaAction' => '/site/captcha'
            ])
            ?>
        <?php } ?>
        <input id="login-btn" class="btn-common btn-normal" name="start" type="submit" value="登录" >
        <?php ActiveForm::end(); ?>

        <?php $this->endBody() ?>

    </body>
</html>
<?php $this->endPage() ?>
