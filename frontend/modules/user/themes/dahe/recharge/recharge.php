<?php

use yii\bootstrap\ActiveForm;
use yii\web\View;

$this->title = '充值 - 大河金服';

$_js = <<<'JS'
$(function() {
    $('#payment-bank').each(function() {
        $(this).on('click', '.picker-item', function() {
            var $this = $(this);
            $this.closest('.picker').find('.picker-item').removeClass('picked');
            $this.addClass('picked');
            $('#bankid').val($this.data('bankid'));
        });
    });

    var $form = $('#recharge_form');
    $form.on('beforeValidate', function() {
        var bank_id = $('#bankid').val();
        if (bank_id === '') {
            alert("请选择银行信息");
            return false;
        }

        return true;
    });
    $form.on('beforeSubmit', function() {
        var $modal = $('#bind-card-modal').modal({backdrop: 'static'});
        $modal.modal('show');
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
                <li class="picker-item picked" id='tobank' data-url='<?= $resp ?>'>个人网银</li>
            </ul>
        </div>

        <h3>网银充值</h3>
        <div id="payment-bank" class="section">
            <ul class="picker">
                <?php foreach($bank as $val): ?>
                <li class="picker-item <?= (strval($val->bankId) === $recharge->bank_id)?"picked":"" ?>" data-bankid="<?= $val->bankId ?>"><img src="<?= ASSETS_BASE_URI ?>images/banks/<?= $val->bankId ?>.jpg" alt="<?= $val->bank->bankName ?>"></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <h3>限额提醒</h3>
        <div class="section">
            <p style="margin-left:63px">具体限额以您在银行协定的额度为准，详细情况下请进入您的网上银行查看，如有疑问，请联系客服：<?= Yii::$app->params['contact_tel'] ?>。</p>
        </div>

        <h3>请填写充值金额</h3>
        <div class="section">
            <?php $form = ActiveForm::begin(['id' => 'recharge_form', 'action' => '/user/recharge/apply', 'options' => ['target' => '_blank']]); ?>
            <ul class="wdjf-form">
                <li><div class="wdjf-label">账户余额</div> <div class="wdjf-field"><span class="balance"><?= $user_account->available_balance ?></span> 元</div></li>
                <li><div class="wdjf-label"><span class="fee-info">*</span>充值金额</div> <div class="wdjf-field"><?= $form->field($recharge, 'fund', ['template' => '{input}{error}'])->textInput(); ?></div><span style='margin-left: 5px;'>元</span></li>
                <li class="wdjf-action">
                    <input class="btn btn-primary" type="submit" value="充值">
                    <p class="fee-info">* 充值所需费用由大河垫付</p>
                </li>
            </ul>
            <input name="_csrf" type="hidden" id="_csrf" value="<?=Yii::$app->request->csrfToken ?>">
            <input type="hidden" id="bankid" name="bankid" value="<?= $recharge->bank_id ?>"/>
            <input type="hidden" name="account_type" value="11"/>
            <input type="hidden" name="pay_type" value="2"/>
            <?php ActiveForm::end(); ?>
        </div>

        <h3>温馨提示</h3>
        <div class="section">
            <ol>
                <li>投资人充值手续费由大河金服垫付；</li>
                <li>最低充值金额应大于等于1元；</li>
                <li>充值期间请勿关闭浏览器，待充值成功并返回账户中心后，所充资金才能入账。如有疑问，请联系客服<?= Yii::$app->params['contact_tel'] ?>。</li>
            </ol>
        </div>
    </div>
</div>

<div class="modal fade" id="bind-card-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p><h5>充值结果</h5></p>

                <p>
                    <a class="btn btn-primary" href="/user/recharge/query">充值成功</a>
                    <a class="btn btn-default" href="/user/recharge/query">充值失败</a>
                </p>
            </div>
        </div>
    </div>
</div>
