<?php
$this->title = '注册';

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
?>

<?php $form = ActiveForm::begin(['action' => "/site/signup"]); ?>
    <?= $form->field($model, 'phone', ['template' => '{input}{error}'])->textInput(['placeholder' => '请输入手机号']) ?>
    <input id="captchaCode" type="text" maxlength="4" placeholder="请输入图形验证码" AUTOCOMPLETE="off">
    <?= $form->field($captcha, 'captchaCode', ['template' => '{error}']) ?>
    <?= $form->field($captcha, 'captchaCode')->label(false)->widget(Captcha::className(), ['template' => '{image}', 'captchaAction' => '/site/captcha']) ?>
    <?= $form->field($model, 'sms', ['template' => '{input}{error}'])->textInput(['placeholder' => '请输入短信验证码', 'maxLength' => 6]) ?>
    <?= $form->field($model, 'password', ['template' => '{input}{error}'])->passwordInput(['placeholder' => '请输入登录密码']) ?>
    <input id="reg-btn" type="submit" value="注册">
<?php ActiveForm::end(); ?>

<br>

