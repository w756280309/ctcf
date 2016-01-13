<?php

?>
<div class="container">
    <div class="main">
        <div class="page-heading">
            <h2>账户提现</h2>
        </div>

        <div class="section">
            <ul class="wdjf-form">
                <li>
                    <div class="wdjf-label">持卡人</div>
                    <div class="wdjf-field"><?= $bank->account ?></div>
                </li>
                <li>
                    <div class="wdjf-label">银行卡</div>
                    <div class="wdjf-field"><?= $bank->bank_name ?></div>
                </li>
                <li>
                    <div class="wdjf-label">开户行信息</div>
                    <div class="wdjf-field"></div>
                </li>
                <li>
                    <div class="wdjf-label">分支行名称</div>
                    <div class="wdjf-field"></div>
                </li>
                <li>
                    <div class="wdjf-label">分支行省份</div>
                    <div class="wdjf-field"></div>
                </li>
            </ul>
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
        <br>
        <br>
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
