<?php

?>
<div class="container">
    <div class="main">
        <div class="section">
            <div class="section-header">
                <h2>账户资金</h2>
            </div>

            <div class="stats row">
                <div class="col-sm-4">累计收益 <span class="balance profit"><?= $model->profit_balance ?></span> 元</div>
                <div class="col-sm-4">账户总额 <span class="balance"><?= $model->account_balance ?></span> 元</div>
                <div class="col-sm-4">可用余额 <span class="balance"><?= $model->available_balance ?></span> 元</div>
            </div>

            <div class="actions">
                <a class="btn btn-primary" href="/user/recharge/recharge">充值</a>
                <a class="btn btn-default" href="/user/useraccount/tixian">提现</a>
            </div>
        </div>
    </div>
</div>

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
