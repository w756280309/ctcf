<?php

use yii\bootstrap\ActiveForm;
$this->title = '提现 - 大河阳光理财';

?>
<div class="container">
    <div class="main">
        <div class="page-heading">
            <h2>账户提现</h2>
        </div>

        <div class="section" style="padding-top: 40px">
            <ul class="wdjf-form">
                <li>
                    <div class="wdjf-label">持卡人</div>
                    <div class="wdjf-field"><?= $bank->account ?></div>
                </li>
                <li>
                    <div class="wdjf-label">银行卡</div>
                    <div class="wdjf-field" style="border: 1px solid #c0c0c0; width: 200px"><img src="<?= ASSETS_BASE_URI ?>images/banks/<?= $bank->bank_id ?>.jpg"><?= substr_replace($bank->card_number, "**", 0, -4) ?></div>
                </li>
            </ul>

            <h3>填写提现金额</h3>
            <div class="section">
                <?php $form = ActiveForm::begin(['id' => 'tixian_form', 'action' => "/user/draw/tixian",]); ?>
                <ul class="wdjf-form">
                    <li>
                        <div class="wdjf-label">可用余额</div>
                        <div class="wdjf-field"><span class="balance"><?= $user_account->available_balance ?></span> 元</div>
                    </li>
                    <li>
                        <div class="wdjf-label">提现金额</div>
                        <div class="wdjf-field"><?= $form->field($draw, 'money', ['template' => '{input}{error}'])->textInput(); ?></div><span style='margin-left: 5px;'>元</span>
                    </li>
                    <li class="wdjf-action">
                        <input class="btn btn-primary" type="submit" value="提现申请">
                        <p>* 提现T+1个工作日到账，遇到法定节假日顺延</p>
                    </li>
                </ul>
                <?php ActiveForm::end(); ?>
            </div>

            <h3>温馨提示</h3>
            <div class="section">
                <ol>
                    <li>身份认证、提现银行卡绑定均设置成功后，才能进行提现；</li>
                    <li>工作日内17:00之前申请提现，当日到账；工作日17:00之后申请提现，会在下一个工作日到账。如遇双休日或法定节假日顺延。</li>
                    <li>提现手续费每笔2元，由第三方资金托管平台收取；</li>
                    <li>特殊声明：禁止洗钱、信用卡套现、虚假交易等行为，一经发现并确认，将终止该账户的使用；</li>
                    <li>如需咨询请联系客服<?= Yii::$app->params['contact_tel'] ?> (周一至周日9:00-20:00，假日另行告知)。</li>
                </ol>
            </div>
        </div>
    </div>
</div>
