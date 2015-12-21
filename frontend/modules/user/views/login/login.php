<?php

use yii\widgets\ActiveForm;


?>
<?php $form = ActiveForm::begin(['id'=>'user_form', 'action' =>"/user/login" ]); ?>
<div class="main">

    <?= $form->field($model, 'username',
        [
            'labelOptions'=>['class'=>''],
            'inputOptions'=>['class'=>'user','id'=>'user','tabindex'=>'1'],
            'template' => '<div class="tip">{label}：</div><div class="text">{input}{error}</div>'])->textInput();?>
    <?= $form->field($model, 'password',
        [
            'labelOptions'=>['class'=>''],
            'inputOptions'=>['class'=>'password','id'=>'user','tabindex'=>'2'],
            'template' => '<div class="tip">{label}：</div><div class="text">{input}{error}</div>'])->passwordInput();?>
</div>
<div class="button">
    <button type="submit"  tabindex="3">登录</button>
    <button type="reset"  tabindex="4">重置</button>
</div>
<?php ActiveForm::end(); ?>
