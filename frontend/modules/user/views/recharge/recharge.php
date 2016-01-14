<?php

use yii\bootstrap\ActiveForm;
use yii\web\View;

$_js = <<<'JS'
$(function() {
    $('#payment-method, #payment-bank').each(function() {
        var $this = $(this);
        $this.on('click', '.picker-item', function() {
            $(this).closest('.picker').find('.picker-item').removeClass('picked');
            $(this).addClass('picked');
        });
    });
})
JS;
$this->registerJs($_js, View::POS_END, 'body_close');

?>
<div class="container">
    <div class="main">
        <div class="page-heading">
            <h2>账户充值</h2>
        </div>

        <h3>付款方式</h3>
        <div id="payment-method" class="section">
            <ul class="picker">
                <li class="picker-item picked">个人网银</li>
            </ul>
        </div>

        <h3>网银充值</h3>
        <div id="payment-bank" class="section">
            <ul class="picker">
                <li class="picker-item picked">个人网银</li>
                <li class="picker-item">个人网银</li>
                <li class="picker-item">个人网银</li>
                <li class="picker-item">个人网银</li>
                <li class="picker-item">个人网银</li>
                <li class="picker-item">个人网银</li>
                <li class="picker-item">个人网银</li>
                <li class="picker-item">个人网银</li>
                <li class="picker-item">个人网银</li>
                <li class="picker-item">个人网银</li>
            </ul>
        </div>

        <h3>快捷限额</h3>
        <div class="section">
            <table class="quota-info">
                <tr>
                    <th>单笔限额（元）</th>
                    <th>每日限额（元）</th>
                    <th>需满足条件</th>
                    <th>备注</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>2</td>
                    <td>3</td>
                    <td rowspan="2">4</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>2</td>
                    <td>3</td>
                </tr>
            </table>
        </div>

        <h3>请填写充值金额</h3>
        <div class="section">
            <?php $form = ActiveForm::begin(['action' => "/user/recharge/recharge"]); ?>
            <ul class="wdjf-form">
                <li><div class="wdjf-label">账户余额</div> <div class="wdjf-field"><span class="balance"><?= $user_account->available_balance ?></span> 元</div></li>
                <li><div class="wdjf-label">充值金额</div> <div class="wdjf-field"><?= $form->field($recharge, 'fund', ['template' => '{input}{error}'])->textInput(); ?></div></li>
                <li class="wdjf-action">
                    <input class="btn btn-primary" type="submit" value="充值">
                    <p class="fee-info">* 充值所需费用由温都垫付</p>
                </li>
            </ul>
            <input name="_csrf" type="hidden" id="_csrf" value="<?=Yii::$app->request->csrfToken ?>">
            <input type="text" name="bankid" value="404" readonly="true"/><br>
            <input type="text" name="account_type" value="11" readonly="true"/><br>
            <input type="text" name="pay_type" value="2" readonly="true"/>
            <?php ActiveForm::end(); ?>
            <a href="/user/recharge/checkarchstatus">充值完成</a>
        </div>

        <h3>温馨提示</h3>
        <div class="section">
            <ol>
                <li>A</li>
                <li>B</li>
                <li>C</li>
            </ol>
        </div>
    </div>
</div>
