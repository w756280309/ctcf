<?php
use yii\bootstrap\ActiveForm;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
    <body>
        <?php $this->beginBody() ?>
        <?php echo "账户余额： ".$user_account->available_balance."元" ?>
        <?php $form = ActiveForm::begin(['action' => "/user/recharge/recharge"]); ?>
        <input name="_csrf" type="hidden" id="_csrf" value="<?=Yii::$app->request->csrfToken ?>">
        <input type="text" name="bankid" value="404" readonly="true"/><br>
        <input type="text" name="account_type" value="11" readonly="true"/>
        <?=
        $form->field($recharge, 'fund', ['template' => '{input}{error}'])->textInput();
        ?>
        <input type="submit" value="充值" >
        <?php ActiveForm::end(); ?>

        <?php $this->endBody() ?>

    </body>
</html>
<?php $this->endPage() ?>
