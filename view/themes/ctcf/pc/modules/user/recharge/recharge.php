<?php

use yii\bootstrap\ActiveForm;
use yii\web\View;
use yii\helpers\Html;

$this->title = '充值';
$this->registerJsFile(ASSETS_BASE_URI.'js/bootstrap-modal.js', ['position' => View::POS_END, 'depends' => 'frontend\assets\FrontAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/bootstrap-modalmanager.js', ['position' => View::POS_END, 'depends' => 'frontend\assets\FrontAsset']);

$_js = <<<'JS'
$(function() {
    $('#payment-bank').each(function() {
        $(this).on('click', '.picker-item', function() {
            var $this = $(this);
            var bankid = $this.data('bankid');
            $this.closest('.picker').find('.picker-item').removeClass('picked');
            $this.addClass('picked');
            $('#bankid').val(bankid);

            $.get('/user/userbank/ebank-limit', {bid: bankid}, function(data) {
                if (data.length !== 1) {
                    $('.bank-limit').show();
                    $('div.bank-limit').html(data+'<br>');
                } else {
                    $('.bank-limit').hide();
                }
            });
        });
    });

    var $form = $('#recharge_form');
    $form.on('beforeValidate', function() {
         if (m == 1) {
            mianmi();
            return false;
        }
        var bank_id = $('#bankid').val();
        if (bank_id === '') {
            alert("请选择银行信息");
            return false;
        }

        return true;
    });
    $form.on('beforeSubmit', function() {
        if (m == 1) {
            mianmi();
            return false;
        }
        alertMessage('请在新打开的联动优势页面进行充值，充值完成前不要关闭该窗口。', '/user/user/index');
    });
})
JS;

$this->registerJs($_js, View::POS_END, 'body_close');
$this->registerCssFile(ASSETS_BASE_URI.'css/useraccount/chargedeposit.css');
$this->registerCssFile(ASSETS_BASE_URI.'css/frontend.css?v=20160829');
$this->registerCssFile(ASSETS_BASE_URI.'css/useraccount/mytrade.css?v=20160720');
$this->registerCssFile(ASSETS_BASE_URI.'css/useraccount/bindcardalready.css');
?>

<div class="myCoupon-box">
    <div class="bindCard-header">
        <div class="bindCard-header-icon"></div>
        <span class="bindCard-header-font" style="font-size: 16px;">充值</span>
    </div>
    <div class="myCoupon-content">
        <div class="list-single">
            <a class="a_first select" href="/user/recharge/init">个人网银</a>
            <a class="a_second " href="/user/userbank/recharge">快捷充值</a>
        </div>
        <br>
        <h3>网银充值</h3>
        <div id="payment-bank" class="section">
            <ul class="picker">
                <?php foreach($bank as $val): ?>
                    <li class="picker-item <?= (strval($val->bankId) === $recharge->bank_id)?"picked":"" ?>" data-bankid="<?= $val->bankId ?>"><img src="<?= ASSETS_BASE_URI ?>images/banks/<?= $val->bankId ?>.jpg" alt="<?= $val->bank->bankName ?>"></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <h3 class="bank-limit">限额提醒</h3>
        <div class="section bank-limit"></div>
        <h3>请填写充值金额</h3>
        <div class="section">
            <?php $form = ActiveForm::begin(['id' => 'recharge_form', 'action' => '/user/recharge/apply', 'options' => ['target' => '_blank']]); ?>
            <ul class="wdjf-form">
                <li><div class="wdjf-label">&nbsp;账户余额</div> <div class="wdjf-field"><span class="balance"><?= $user_account->available_balance ?></span> 元</div></li>
                <li><div class="wdjf-label"><span class="fee-info">*</span>充值金额</div> <div class="wdjf-field"><?= $form->field($recharge, 'fund', ['template' => '{input}{error}'])->textInput(['autocomplete' => 'off']); ?></div><span style='margin-left: 5px; line-height: 33px;'>元</span></li>
                <li class="wdjf-action">
                    <input class="btn btn-primary" type="submit" style="    color: #fff;background-color: #f44336;width: 65px;border: 0px;border-radius: 0px;" value="充值">
                    <p class="fee-info">* 充值所需费用由楚天垫付</p>
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
            <p>投资人充值手续费由楚天财富垫付；</p>
            <p>最低充值金额应大于等于1元；</p>
            <p>充值期间请勿关闭浏览器，待充值成功并返回账户中心后，所充资金才能入账。如有疑问，请联系客服<?= Yii::$app->params['platform_info.contact_tel'] ?>（<?= Yii::$app->params['platform_info.customer_service_time'] ?>）。</p>
        </div>
    </div>
</div>
<div class="modal fade" id="bind-card-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p><h5>充值结果</h5></p>

                <p>
                    <a class="btn btn-primary" href="/info/success?source=chongzhi&jumpUrl=<?= Html::encode($url) ?>">充值成功</a>
                    <a class="btn btn-default" href='/info/fail?source=chongzhi&jumpUrl=/user/recharge/init'>充值失败</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    var m = <?= intval($data['code'])?>;
    if (m == 1) {
        mianmi();
    }
</script>