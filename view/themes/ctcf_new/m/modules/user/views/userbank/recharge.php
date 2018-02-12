<?php

use yii\helpers\Html;
use common\utils\StringUtils;
use common\view\UdeskWebIMHelper;

$this->title="充值";
UdeskWebIMHelper::init($this);

if ($backUrl = \Yii::$app->session['recharge_back_url']) {
    $this->backUrl = Html::encode($backUrl);
}
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/bind.css"/>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/chongzhi.css?v=20171213"/>
<script src="<?= ASSETS_BASE_URI ?>js/layer.js?v=1"></script>
<!--银行卡-->
<div class="row bank-card">
    <div class="col-xs-2 bank-img"><img src="<?= ASSETS_BASE_URI ?>images/bankicon/<?= $user_bank->bank_id ?>.png" alt=""/></div>
    <div class="col-xs-10 bank-content">
        <div class="bank-content1" style="font-size: 14px;">
            <?= $user_bank->bank_name ?>
            <span style="font-size: 12px;">(限额<?= StringUtils::amountFormat1('{amount}{unit}', $bank['singleLimit']) ?>/笔，<?= StringUtils::amountFormat1('{amount}{unit}', $bank['dailyLimit']) ?>/日)</span>
        </div>
        <div class="bank-content2" style="color: #aab2bd;">
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
    <div class="row kahao recharge-kahao">
        <div class="col-xs-3 col-sm-2">充值金额</div>
        <div class="col-xs-9 col-sm-8 safe-lf">
            <input type="number" id="fund" autocomplete="off" name='RechargeRecord[fund]' placeholder="输入充值金额"/>
            <span class="trans" ></span>
        </div>
    </div>
    <p class="formula">手机充值超过<span class="formula-money"></span>每日限额，您可以用电脑登录楚天财富官网（www.hbctcf.com）充值<a class="a-formula">[查看详情]</a></p>

    <!--提交按钮-->
    <div class="row">
        <div class="col-xs-12 login-sign-btn">
            <!--当快捷充值不支持时,按钮禁用-->
            <input id="rechargebtn" class="rechargebtn btn-common btn-normal" type="submit" value="立即充值" <?= $bank->isDisabled ? "disabled=\"disabled\"" : "" ?>>
        </div>
    </div>
    <!--  当快捷充值被禁用,需要显示提示信息 -->
    <?php if ($bank->isDisabled) { ?>
        <div class="note form-bottom" style="font-size: 13px">*当前所绑定银行卡的快捷充值已暂停，您可以用电脑登录网站（www.hbctcf.com）进行大额充值。如有疑问请拨打客服电话：<a class="contact-tel" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><?= Yii::$app->params['platform_info.contact_tel'] ?></a>。</div>
    <?php } else { ?>
        <div class="note">
            <ul>
                <li class="li-title">温馨提示</li>
                <li style="color: #ff6707">手机充值不能超过<?= StringUtils::amountFormat1('{amount}{unit}', $bank['dailyLimit']) ?>每日限额，可以用电脑登录网站（www.hbctcf.com）进行大额充值。
                    <a href="/user/userbank/refer" style="color: #419bf9;">[查看详情]</a>
                </li>
                <li>为保障安全，连续3次充值失败，24小时内将无法通过手机充值。</li>
                <li>客服电话：<a class="contact-tel" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><?= Yii::$app->params['platform_info.contact_tel'] ?>。</li>
            </ul>
        </div>
        <div class="form-bottom">&nbsp;</div>
    <?php } ?>
</form>

<a href="javascript:void(0)" id="btn_udesk_im" style="display:block; margin-bottom: 10%;"><img src="<?= FE_BASE_URI ?>wap/new-homepage/images/online-service-blue.png">在线客服</a>

<script type="text/javascript">
    var csrf;
    function validateform() {
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
        var singleLimit = '<?= $bank['singleLimit'] ?>';
        var singleLimitUnit = '<?= StringUtils::amountFormat1('{amount}{unit}', $bank['singleLimit']) ?>';
        var dailyLimit = '<?= $bank['dailyLimit'] ?>';
        var dailyLimitUnit = '<?= StringUtils::amountFormat1('{amount}{unit}', $bank['dailyLimit']) ?>';
        $('.formula-money').html(dailyLimitUnit);
        (function () {
            if ( parseFloat($('#fund').val()) > parseFloat(dailyLimit) ) {
                $('.recharge-kahao .trans,.cmxform .formula').show();
                return false;
            } else {
                $('.recharge-kahao .trans,.cmxform .formula').hide();
                return false;
            }
        })();
        $('.formula').on('click', '.a-formula', function () {
            $('.recharge-kahao .trans,.cmxform .formula').hide();
            location.href='/user/userbank/refer';
        });
        inputListener('#fund',dailyLimit);
        function inputListener(obj,dailyMoney) {
            document.querySelector(obj).addEventListener('input', function () {
                var _this = this;
                _this.onkeyup = function () {
                    _this.value = _this.value.replace(/[^\d.]/g, '');
                    if ( parseFloat(_this.value) > parseFloat(dailyMoney) ) {
                        $('.recharge-kahao .trans,.cmxform .formula').show();
                        return false;
                    } else {
                        $('.recharge-kahao .trans,.cmxform .formula').hide();
                        return false;
                    }

                }
            });
        }

        //  温馨提示 弹框
        function openPopup() {
            var message = '';
            if(singleLimit != undefined){
                message = '本次充值超过单笔'+singleLimitUnit+'限额，建议您分多笔进行充值';
            }
            layer.open({
                title: ['温馨提示', 'background-color: #ff6058; color:#fff;']
                ,content: message
                ,shadeClose:false
                ,className: 'customer-layer-popuo'
                ,btn: [ '返回修改']
                ,no: function(index){
                    layer.closeAll();
                }
            });
        }

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
           var money = $('#fund').val();
           var $btn = $('#rechargebtn');
           $btn.addClass("btn-press").removeClass("btn-normal");
           if (parseFloat(singleLimit) < parseFloat(money) ) {
               openPopup();
               return false;
           }
           if (!validateform()) {
               return false;
           }
           subRecharge();
           $btn.removeClass("btn-press").addClass("btn-normal");
       });
    });

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