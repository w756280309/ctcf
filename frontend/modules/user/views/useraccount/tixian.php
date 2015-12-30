<?php
use yii\bootstrap\ActiveForm;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
    <body>
        <?php $this->beginBody() ?>
        <?php $form = ActiveForm::begin(['id' => 'login', 'action' => "/user/useraccount/tixian",]); ?>
        <?=
        $form->field($draw, 'money', ['template' => '{input}{error}'])->textInput();
        ?>
        <?=
        $form->field($model, 'password', ['template' => '{input}{error}'])->textInput();
        ?>
        <input type="submit" value="登录" >
        <?php ActiveForm::end(); ?>

        <?php $this->endBody() ?>

    </body>
</html>
<?php $this->endPage() ?>
