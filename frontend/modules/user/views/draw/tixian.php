<?php
    $this->title = '提现';
    \frontend\assets\FrontAsset::register($this);
    $this->registerCssFile('/css/UserAccount/bindcardalready.css');
    $this->registerCssFile('/css/useraccount/chargedeposit.css');
?>
<div class="bindCard-box">
<div class="bindCard-header">
    <div class="bindCard-header-icon"></div>
    <span class="bindCard-header-font">提现</span>
</div>
<div class="bindCard-content">
    <?php if($user_bank) { ?>
        <p class="bindCard-content-header">已绑定银行卡：</p>
        <div class="bindCard-single">
            <span class="single-left">持卡人</span>
            <span class="single-right"><?= $user_bank->account ?></span>
        </div>
        <!------------------已绑卡开始------------------>
        <div class="bindCard-already">
            <span class="single-left">银行卡</span>
            <div class="single-div no-pointer">
                <span class="single-icon"><img class="single-icon-img" height="18" src="<?= ASSETS_BASE_URI ?>images/useraccount/bankIcon/<?= $user_bank->bank_id ?>.png" alt=""></span>
                <span class="single-name"><?= $user_bank->bank_name ?></span>
                <span class="single-number">尾号<?= $user_bank->card_number?substr($user_bank->card_number, -4):"" ?></span>
            </div>
            <div class="clear"></div>
            <div>
                <span style="font-size: 12px;">可提现金额：<?= rtrim(rtrim(number_format($user_acount->available_balance, 2), '0'), '.') ?>元</span>
            </div>
        </div>
        <form method="post" class="cmxform" id="form" action="/user/draw/tixian" data-to="1">
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <input name="flag" type="hidden" value="checktrade">
            <div class="row kahao">
                <p class="bindCard-content-header">提现金额：<input style="width: 244px;height: 38px;border: 1px solid #e4e4e8;background: #efeff3;text-align: right;outline: none;color: #6e6e72;padding: 0 15px;font-size: 24px;" type="text" id="fund" name="DrawRecord[money]"  placeholder="输入提现金额"/></p>
                <p class="error" style="display: none; padding-left: 70px;color: red;"></p>
            </div>
            <div class="link-en">
                <input type="submit" class="link-charge" value="提现" id="rechargebtn" />
            </div>
        </form>
        <!------------------已绑卡结束------------------>
    <?php } else { ?>
        <p class="bindCard-content-header">未绑定银行卡：</p>
        <!------------------未绑卡开始------------------>
        <div class="bindCard-yet">
            <span class="single-left">银行卡</span>
            <a class="single-div">
                <span class="add-icon"></span>
                <span class="link-font"  onclick="location.href='/user/userbank/bindbank'">点击绑定银行卡</span>
            </a>
            <a class="clear"></a>
        </div>
        <!------------------未绑卡结束------------------>
    <?php }?>
    <div class="clear"></div>
</div>
<div class="charge-explain">
    <p class="charge-explain-title">温馨提示：</p>
    <p class="charge-explain-content">1、绑定银行卡为快捷卡，绑定后将默认为快捷充值卡和取现卡，如需修改，可申请更换银行卡；</p>
    <p class="charge-explain-content">2、暂不支持设置多张快捷支付卡；</p>
    <p class="charge-explain-content">3、绑定快捷卡后，不影响使用本人其他银行卡或他人银行卡代充值。</p>
</div>
</div>
<script>
    var m = <?= intval($data['code'])?>;
    if (m == 1) {
        mianmi();
    }

    function err_message(message){
        if (message.length > 0) {
            $('.error').show();
            $('.error').html(message);
        } else {
            $('.error').hide();
        }
    }

    function validateform()
    {
        if($.trim($('#fund').val()) === '') {
            err_message('提现金额不能为空');
            $('#rechargebtn').removeClass("btn-press").addClass("btn-normal");
            return false;
        }
        if ($('#fund').val() === '0') {
            err_message('提现金额不能为零');
            $('#rechargebtn').removeClass("btn-press").addClass("btn-normal");
            return false;
        }
        var reg = /^[0-9]+([.]{1}[0-9]{1,2})?$/;
        if (!reg.test($('#fund').val())) {
            err_message('提现金额格式不正确');
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
            err_message(mess, function() {
                if (tourl !== '') {
                    location.href = tourl;
                }
            });
            return;
        }

        csrf = '<?= Yii::$app->request->csrfToken ?>';

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
            location.href=data['tourl']
        });

        xhr.fail(function(jqXHR) {
            var errMsg = jqXHR.responseJSON && jqXHR.responseJSON.message
                ? jqXHR.responseJSON.message
                : '未知错误，请刷新重试或联系客服';

            err_message(errMsg);
            $('#rechargebtn').attr('disabled', false);
        });
    }
</script>
