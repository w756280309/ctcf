<?php

$this->title="充值";

use common\utils\StringUtils;

?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/bind.css"/>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/chongzhi.css?v=20160412"/>
<style type="text/css">
    .yzm-show {
        width: 100px !important;
        margin-top:-4px !important;
        font-size:12px !important;
        height:28px !important;
        line-height:27px !important;
    }
    .ryzm-disabled {
        background:#fff !important;
        border: 1px solid #f44336 !important;
    }
</style>

    <!--银行卡-->
    <div class="row bank-card">
        <div class="col-xs-2 bank-img"><img src="<?= ASSETS_BASE_URI ?>images/bankicon/<?= $user_bank->bank_id ?>.png" alt=""/></div>
        <div class="col-xs-10 bank-content">
            <div class="bank-content1" style="font-size: 14px;">
                <?= $user_bank->bank_name ?>
                <span style="font-size: 12px;">(限额<?=  StringUtils::amountFormat1('{amount}{unit}', $bank['singleLimit']) ?>/笔，<?= StringUtils::amountFormat1('{amount}{unit}', $bank['dailyLimit']) ?>/日)</span>
            </div>
            <div class="bank-content2">
                尾号<?= $user_bank->card_number?substr($user_bank->card_number, -4):"" ?> 储蓄卡
                </br>银行预留手机号 <?= substr_replace(Yii::$app->user->identity->mobile,'****',3,-4); ?>
            </div>
        </div>
<!--        <div class="col-xs-1"></div>-->
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
        <div class="form-bottom note">*绑定银行暂不支持快捷充值，如有问题请联系客服<?= Yii::$app->params['contact_tel'] ?></div>
        <?php } else { ?>
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
           var err = '<?= $data['code'] ?>';
           var mess = '<?= $data['message'] ?>';
           var tourl = '<?= $data['tourl'] ?>';
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
                var errMsg = jqXHR.responseJSON && jqXHR.responseJSON.message
                    ? jqXHR.responseJSON.message
                    : '未知错误，请刷新重试或联系客服';

                toast(errMsg);
                $('#rechargebtn').attr('disabled', false);
            });
        }
    </script>