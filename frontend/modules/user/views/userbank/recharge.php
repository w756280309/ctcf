<?php
    $this->title = '充值';
    $this->registerCssFile('/css/useraccount/bindcardalready.css');
    $this->registerCssFile('/css/useraccount/chargedeposit.css');
    \frontend\assets\FrontAsset::register($this);
?>
<style>
    .list-single {
        color: #555;
        font-size: 16px;
        line-height: 16px;
        width: 100%;
        height: 36px;
        position: relative;
        border-bottom: 1px solid #e3e3e3;
    }
    .list-single a {
        position: absolute;
        top: 10px;
        color: #99999d;
        cursor: pointer;
        padding: 0 10px;
    }
    .list-single a.select {
        color: #f44336;
        padding-bottom: 9px;
        border-bottom: 2px solid #f44336;
    }
    .list-single .a_first {
        left: 0;
    }
     .list-single .a_second {
        left: 162px;
    }
</style>

<div class="bindCard-box">
    <div class="bindCard-header">
        <div class="bindCard-header-icon"></div>
        <span class="bindCard-header-font">充值</span>
    </div>
    <div class="bindCard-content">
        <div class="list-single">
            <a class="a_first " href="/user/recharge/init">个人网银</a>
            <a class="a_second select" href="/user/userbank/recharge">快捷充值</a>
        </div>
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
                <span class="single-icon"><img class="single-icon-img" height="18" src="<?= ASSETS_BASE_URI ?>images/useraccount/bankicon/<?= $user_bank->bank_id ?>.png" alt=""></span>
                <span class="single-name"><?= $user_bank->bank_name ?></span>
                <span class="single-number">尾号<?= $user_bank->card_number?substr($user_bank->card_number, -4):"" ?></span>
            </div>
            <div class="clear"></div>
            <div style="    margin-top: 5px;margin-bottom: 10px;">
                <span style="font-size: 12px;">(限额<?= \Yii::$app->functions->toFormatMoney($bank['singleLimit']) ?>/笔，<?= \Yii::$app->functions->toFormatMoney($bank['dailyLimit']) ?>/日)</span>
            </div>
        </div>
        <div class="bindCard-single">
            <span class="single-left" style="line-height: 34px;">账户余额</span>
            <span class="single-right" style="line-height: 34px;font-size: 20px;font-weight: bold;color: #f44336;"><?= number_format($user->lendAccount->available_balance, 2) ?></span>元
        </div>
        <form method="post" class="cmxform" id="form" action="/user/qpay/qrecharge/verify" data-to="1">
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <input name="from" type="hidden" value="<?= Yii::$app->request->get('from'); ?>">
            <div class="row kahao">
                <p class="bindCard-content-header">充值金额：<input style="width: 244px;height: 34px;border: 1px solid #e4e4e8;background: #efeff3;text-align: right;outline: none;color: #6e6e72;padding: 0 15px;font-size: 16px;" type="text" id="fund" name='RechargeRecord[fund]'  placeholder="输入充值金额"/></p>
                <p class="error" style="display: none; padding-left: 70px;color: red;"></p>
            </div>
            <div class="link-en">
                <input type="submit" class="link-charge" value="充值" id="rechargebtn" />
            </div>
            <p class="fee-info" style="    margin-top: 15px;color: #f44336;    margin-left: 60px;">* 充值所需费用由温都垫付</p>
        </form>
        <!------------------已绑卡结束------------------>
        <?php } elseif ($binding) { ?>
            <p class="bindCard-content-header">绑卡处理中：</p>
            <div class="bindCard-single">
                <span class="single-left">持卡人</span>
                <span class="single-right"><?= $binding->account ?></span>
            </div>
            <div class="bindCard-already">
                <span class="single-left">银行卡</span>
                <div class="single-div no-pointer">
                    <span class="single-icon"><img class="single-icon-img" height="18" src="<?= ASSETS_BASE_URI ?>images/useraccount/bankicon/<?= $binding->bank_id ?>.png" alt=""></span>
                    <span class="single-name"><?= $binding->bank_name ?></span>
                    <span class="single-number">尾号<?= $binding->card_number?substr($binding->card_number, -4):"" ?></span>
                </div>
                <div class="clear"></div>
                <div class="link-en">
                </div>
            </div>
        <?php } else { ?>
        <p class="bindCard-content-header">未绑定银行卡：</p>
        <!------------------未绑卡开始------------------>
        <div class="bindCard-yet">
        <span class="single-left">银行卡</span>
        <a class="single-div">
        <span class="add-icon"></span>
        <span class="link-font" onclick="location.href='/user/userbank/bindbank'">点击绑定银行卡</span>
        </a>
        <a class="clear"></a>
        </div>
        <!------------------未绑卡结束------------------>
        <?php }?>
        <div class="clear"></div>
    </div>
    <div class="charge-explain">
        <p class="charge-explain-title">温馨提示：</p>
        <ul>
            <li>投资人充值手续费由温都金服垫付；</li>
            <li>最低充值金额应大于等于1元；</li>
            <li>充值期间请勿关闭浏览器，待充值成功并返回账户中心后，所充资金才能入账。如有疑问，请联系客服<?= Yii::$app->params['contact_tel'] ?>。</li>
        </ul>
    </div>
</div>

<script>
    var m = <?= $user->mianmiStatus?>;
    if (m == 0) {
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
            err_message('充值金额不能为空');
            $('#rechargebtn').removeClass("btn-press").addClass("btn-normal");
            return false;
        }
        if ($('#fund').val() === '0') {
            err_message('充值金额不能为零');
            $('#rechargebtn').removeClass("btn-press").addClass("btn-normal");
            return false;
        }
        var reg = /^[0-9]+([.]{1}[0-9]{1,2})?$/;
        if (!reg.test($('#fund').val())) {
            err_message('充值金额格式不正确');
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

        $('#form').on('submit', function(e) {
            e.preventDefault();
            if (m == 0) {
                mianmi();
                return false;
            }
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
        var xhr = $.ajax({
            url: $form.attr('action'),
            data: $form.serialize(),
            type:'POST',
            async:false
        });

        xhr.done(function(data) {
            $('#rechargebtn').attr('disabled', false);
            if (data.next){
                alertMessage('请在新打开的联动优势页面进行充值，充值完成前不要关闭该窗口。', '/user/user/index');
                window.open(data.next);
            }
        });

        xhr.fail(function(jqXHR) {
            var errMsg = jqXHR.responseJSON && jqXHR.responseJSON.message ? jqXHR.responseJSON.message : '';

            errMsg = errMsg ? errMsg : $.parseJSON(jqXHR.responseText).message;
            errMsg = errMsg ? errMsg : '未知错误，请刷新重试或联系客服';
            err_message(errMsg);
            $('#rechargebtn').attr('disabled', false);
        });
    }
</script>