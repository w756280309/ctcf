<?php
use yii\bootstrap\ActiveForm;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
    <body>
        <?php $this->beginBody() ?>
        <?= $bank->account ?><br>
        <?= $bank->bank_name ?><br>
        <?= $bank->card_number ?><br>
        <?= $user_account->available_balance ?>
        <?php $form = ActiveForm::begin(['id' => 'login', 'action' => "/user/useraccount/tixian",]); ?>
        <?=
        $form->field($draw, 'money', ['template' => '{input}{error}'])->textInput();
        ?>
        <?=
        $form->field($model, 'password', ['template' => '{input}{error}'])->textInput();
        ?>
        <input type="submit" value="提现申请" >
        <?php ActiveForm::end(); ?>
        <a href="/user/useraccount/editbank">完善银行信息</a>
        <?php $this->endBody() ?>

    </body>
</html>
<?php $this->endPage() ?>
