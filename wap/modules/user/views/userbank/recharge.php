<?php

use yii\helpers\Html;
use common\utils\StringUtils;

$this->title="充值";

if ($backUrl = \Yii::$app->session['recharge_back_url']) {
    $this->backUrl = Html::encode($backUrl);
}
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/bind.css"/>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/chongzhi.css?v=20160803"/>

<!--银行卡-->
<div class="row bank-card">
    <div class="col-xs-2 bank-img"><img src="<?= ASSETS_BASE_URI ?>images/bankicon/<?= $user_bank->bank_id ?>.png" alt=""/></div>
    <div class="col-xs-10 bank-content">
        <div class="bank-content1" style="font-size: 14px;">
            <?= $user_bank->bank_name ?>
            <span style="font-size: 12px;">(限额<?= StringUtils::amountFormat1('{amount}{unit}', $bank['singleLimit']) ?>/笔，<?= StringUtils::amountFormat1('{amount}{unit}', $bank['dailyLimit']) ?>/日)</span>
        </div>
        <div class="bank-content2">
            尾号<?= $user_bank->card_number?substr($user_bank->card_number, -4):"" ?> 储蓄卡
            </br>银行预留手机号 <?= substr_replace(Yii::$app->user->identity->mobile,'****',3,-4); ?>
        </div>
    </div>
</div>
<!--可用金额-->
<div class="row kahao">
    <div class="col-xs-3 col-sm-2">可用金额</div>
    <div class="col-xs-9 col-sm-8"><?= rtrim(rtrim(number_format($user_acount->available_balance, 2), '0'), '.') ?>元</div>
</div>
<!--充值金额-->
<form method="post" class="cmxform" id="form" action="/user/qpay/qrecharge/verify" data-to="1">
    <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
    <input name="from" type="hidden" value="<?= Yii::$app->request->get('from'); ?>">
    <div class="row kahao">
        <div class="col-xs-3 col-sm-2">充值金额</div>
        <div class="col-xs-9 col-sm-8 safe-lf"><input type="text" id="fund"  name='RechargeRecord[fund]' placeholder="输入充值金额"/></div>
    </div>
    <!--  当快捷充值被禁用,需要显示提示信息 -->
    <?php if ($bank->isDisabled) { ?>
        <div class="note form-bottom">*绑定银行暂不支持快捷充值，如有问题请联系客服<a class="contact-tel" href="tel:<?= Yii::$app->params['contact_tel'] ?>"><?= Yii::$app->params['contact_tel'] ?></a>。</div>
    <?php } else { ?>
        <br>
        <div class="note">
            <ul>
                <li>*温馨提示：</li>
                <li>为保障安全，连续3次因余额不足导致充值失败，快捷充值通道将被锁定24小时，请核实后交易；</li>
                <li>如果快捷充值通道已被锁定，可先选择PC官网进行网银充值；</li>
                <li>如需要大额充值，可选择PC官网进行网银充值，地址（www.wenjf.com），如有疑问，请联系客服<a class="contact-tel" href="tel:<?= Yii::$app->params['contact_tel'] ?>"><?= Yii::$app->params['contact_tel'] ?></a>。</li>
            </ul>
        </div>
        <div class="form-bottom">&nbsp;</div>
    <?php } ?>
    <!--提交按钮-->
    <div class="row">
        <div class="col-xs-3"></div>
        <div class="col-xs-6 login-sign-btn">
            <!--当快捷充值不支持时,按钮禁用-->
            <input id="rechargebtn" class="btn-common btn-normal" type="submit" value="立即充值" <?= $bank->isDisabled ? "disabled=\"disabled\"" : "" ?>>
        </div>
        <div class="col-xs-3"></div>
    </div>
</form>

<script type="text/javascript">
    var csrf;
    function validateform()
    {
        if($.trim($('#fund').val()) === '') {
            toast('充值金额不能为空');
            $('#rechargebtn').removeClass("btn-press").addClass("btn-normal");
            return false;
        }
        if ($('#fund').val() === '0') {
            toast('充值金额不能为零');
            $('#rechargebtn').removeClass("btn-press").addClass("btn-normal");
            return false;
        }
        var reg = /^[0-9]+([.]{1}[0-9]{1,2})?$/;
        if (!reg.test($('#fund').val())) {
            toast('充值金额格式不正确');
            $('#rechargebtn').removeClass("btn-press").addClass("btn-normal");
            return false;
        }
        return true;
    }
    $(function() {
       var err = '<?= isset($data['code']) ? $data['code'] : '' ?>';
       var mess = '<?= isset($data['message']) ? $data['message'] : '' ?>';
       var tourl = '<?= isset($data['tourl']) ? $data['tourl'] : '' ?>';
       if(err === '1') {
           toast(mess, function() {
               if (tourl !== '') {
                   location.href = tourl;
               }
           });
           return;
       }

       csrf = $("meta[name=csrf-token]").attr('content');

       $('#form').on('submit', function(e) {
           e.preventDefault();

           var $btn = $('#rechargebtn');
           $btn.addClass("btn-press").removeClass("btn-normal");
           if (!validateform()) {
               return false;
           }
           subRecharge();
           $btn.removeClass("btn-press").addClass("btn-normal");
       });

    })

    function subRecharge(){
        var $form = $('#form');
        $('#rechargebtn').attr('disabled', true);
        var xhr = $.post(
            $form.attr('action'),
            $form.serialize()
        );

        xhr.done(function(data) {
            $('#rechargebtn').attr('disabled', false);
            location.href=data['next']
        });

        xhr.fail(function(jqXHR) {
            var errMsg = jqXHR.status === 400 && jqXHR.responseJSON && jqXHR.responseJSON.message
                ? jqXHR.responseJSON.message
                : '系统繁忙，请稍后重试！';

            toast(errMsg);
            $('#rechargebtn').attr('disabled', false);
        });
    }
</script>