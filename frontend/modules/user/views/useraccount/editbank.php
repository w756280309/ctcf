<?php
use yii\bootstrap\ActiveForm;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
    <body>
        <?php $this->beginBody() ?>
        <?php $form = ActiveForm::begin(['id' => 'login', 'action' => "/user/useraccount/editbank",]); ?>
        <?=
        $form->field($model, 'sub_bank_name', ['template' => '{input}{error}'])->textInput();
        ?>
        <?=
        $form->field($model, 'province', ['template' => '{input}{error}'])->textInput();
        ?>
        <?=
        $form->field($model, 'city', ['template' => '{input}{error}'])->textInput();
        ?>
        <input type="submit" value="登录" >
        <?php ActiveForm::end(); ?>

        <?php $this->endBody() ?>

    </body>
</html>
<?php $this->endPage() ?>
