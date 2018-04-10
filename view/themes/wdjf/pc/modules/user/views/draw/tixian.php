<?php
    $this->title = '提现';

    $this->registerCssFile('/css/useraccount/bindcardalready.css');
    $this->registerCssFile('/css/useraccount/chargedeposit.css');

    use common\utils\StringUtils;

    $drawFreeLimit = Yii::$app->params['draw_free_limit'];
    $drawFee = Yii::$app->params['drawFee'];
    $restDrawCount = $drawFreeLimit - $user_acount->user->getDrawCount();
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
                <span class="single-icon"><img class="single-icon-img" height="18" src="<?= ASSETS_BASE_URI ?>images/useraccount/bankicon/<?= $user_bank->bank_id ?>.png" alt=""></span>
                <span class="single-name"><?= $user_bank->bank_name ?></span>
                <span class="single-number">尾号<?= $user_bank->card_number?substr($user_bank->card_number, -4):"" ?></span>
            </div>
            <div class="clear"></div>
        </div>
        <div class="bindCard-single" style="margin-top: 10px;">
            <span class="single-left" style="line-height: 34px;">可提现金额</span>
            <span class="single-right" style="line-height: 34px;font-size: 20px;font-weight: bold;color: #f44336;"><?= $user_acount->available_balance ?></span>元
        </div>
        <form method="post" class="cmxform" id="form" action="/user/draw/tixian" data-to="1">
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <input name="flag" type="hidden" value="checktrade">
            <div class="row kahao">
                <p class="bindCard-content-header">提现金额：<input style="width: 244px;height: 34px;border: 1px solid #e4e4e8;background: #efeff3;text-align: right;outline: none;color: #6e6e72;padding: 0 15px;font-size: 16px;" type="text" id="fund" name="DrawRecord[money]" autocomplete="off"  placeholder="输入提现金额"/></p>
                <p class="error" style="display: none; padding-left: 70px;color: red;"></p>
            </div>
            <div class="link-en">
                <input type="submit" class="link-charge" value="提现" id="rechargebtn" />
            </div>
            <p class="fee-info" style="margin-top: -24px;color: #f44336;margin-left: 60px;">* 每月有<?= $drawFreeLimit ?>次免费发起提现的机会，本月您还有<?= $restDrawCount > 0 ? $restDrawCount : 0 ?>次免费机会，之后每笔收取<?= $drawFee ?>元手续费；</p>
            <p class="fee-info" style="color: #f44336;margin-left: 60px;">* 当日可提现100万，超过100万以上请提前一个工作日联系客服申请，客服电话<?= Yii::$app->params['platform_info.contact_tel'] ?>。</p>
        </form>
        <!------------------已绑卡结束------------------>
    <?php } else { ?>
        <p class="bindCard-content-header">未绑定银行卡：</p>
        <!------------------未绑卡开始------------------>
        <div class="bindCard-yet">
            <span class="single-left">银行卡</span>
            <a class="single-div">
                <span class="add-icon"></span>
                <span class="link-font"  onclick="location.href='/user/bank'">点击绑定银行卡</span>
            </a>
            <a class="clear"></a>
        </div>
        <!------------------未绑卡结束------------------>
    <?php }?>
    <div class="clear"></div>
</div>
<div class="charge-explain">
    <p class="charge-explain-title">温馨提示：</p>
    <p class="charge-explain-content">身份认证、提现银行卡绑定均设置成功后，才能进行提现；</p>
    <p class="charge-explain-content">工作日内17:00之前申请提现，当日到账；工作日17:00之后申请提现，会在下一个工作日到账。如遇双休日或法定节假日顺延。</p>
    <p class="charge-explain-content">每月有<?= $drawFreeLimit ?>次免费发起提现的机会，之后每笔收取<?= $drawFee ?>元手续费，由第三方资金托管平台收取；</p>
    <p class="charge-explain-content"> 特殊声明：禁止洗钱、信用卡套现、虚假交易等行为，一经发现并确认，将终止该账户的使用；</p>
    <p class="charge-explain-content">如需咨询请联系客服<?= Yii::$app->params['platform_info.contact_tel'] ?> (周一至周日8:30-20:00，假日另行告知)。</p>
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

        $('#form').on('submit', function(e) {
            e.preventDefault();
            if (m == 1) {
                mianmi();
                return false;
            }
            var $btn = $('#rechargebtn');
            $btn.addClass("btn-press").removeClass("btn-normal");
            if (!validateform()) {
                return false;
            }
            subDraw();
            $btn.removeClass("btn-press").addClass("btn-normal");
        });

    });

    function subDraw()
    {
        var $form = $('#form');
        $('#rechargebtn').attr('disabled', true);
        var xhr = $.ajax({
            url: $form.attr('action'),
            data: $form.serialize(),
            type:'POST',
            async:false
        });

        xhr.done(function(data) {
            res = true;
            $('#rechargebtn').attr('disabled', false);
            if(data.code == 1 && data.message.length > 0) {
                err_message(data.message);
            }
            if (data.tourl) {
                var w = window.open(data.tourl);
                var url = data.tourl;
                if (url && !w) {
                    location.href = url;
                } else if(url && w) {
                    alertMessage('请在新打开的联动优势页面进行提现，提现完成前不要关闭该窗口。', '/user/user/index');
                }
            }
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
