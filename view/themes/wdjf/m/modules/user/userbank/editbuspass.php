<?php

use yii\bootstrap\ActiveForm;

$this->title = '修改第三方交易密码';

?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css">

<style>
	body{
		background: #E8ECEB;
	}
	.container {
		background: #E8ECEB;
	}
</style>

<div class="row" id='payment'>
        <div class="col-xs-12">忘记资金托管账户支付密码</div>
</div>
<div class="row paymentLock">
        <div class="col-xs-12">
                <img src="<?= ASSETS_BASE_URI ?>images/paymentLock.jpg" alt="">
        </div>
</div>
<div class="row" id='payment-content'>
        <div class="col-xs-12">若您忘记了您的资金托管账户支付密码，可以申请密码重置，联动优势将新的支付密码以短信形式发送到您的手机，请注意接收并妥善保管。</div>
</div>
<?php $form = ActiveForm::begin(['id' => 'form', 'action' =>"/user/userbank/reset-trade-pass"]); ?>
<div class="row">
    <div class="col-xs-12">
        <input id="editpassbtn" class="btn-common btn-normal" style="margin-top:10px; background-color: #F2F2F2;" type="button" value="重置支付密码">
    </div>
</div>
<?php ActiveForm::end(); ?>
<div class="row" id='payment-change'>
        <div class="col-xs-12">修改资金托管账户支付密码</div>
</div>
<div class="row" id='payment-how'>
        <div class="col-xs-12">您可以编辑短信<span>“GGMM#原密码#新密码”</span>(例如:GGMM#123456#234567) 发送至联动优势修改您的支付密码，支付密码只能是6位数字。</div>
</div>
<div class="row" id='payment-li'>
        <div class="col-xs-12">
                <div>联动优势短信号码:</div>
                <div>移动，联通，电信用户编辑短信发至10690569687</div>
        </div>
</div>

<!-- 绑定提示 end  -->
<!-- 修改登录密码页 end  -->
<script type="text/javascript">
    var csrf;
    $(function(){
        var err = '<?= $data['code'] ?>';
        var mess = '<?= $data['message'] ?>';
        var tourl = '<?= $data['tourl'] ?>';
        if ('1' === err) {
            toast(mess, function() {
                if (tourl !== '') {
                    location.href = tourl;
                }
            });
        }

        $('#editpassbtn').bind('click', function() {
            var $form = $('#form');
            $(this).attr('disabled', true);
            var xhr = $.post(
                $form.attr('action'),
                $form.serialize()
            );

            xhr.done(function(data) {
                if (data.message !== undefined) {
                    toast(data.message, function() {
                        $('#editpassbtn').attr('disabled', false);
                    });
                }
            });

            xhr.fail(function(jqXHR) {
                var errMsg = jqXHR.responseJSON && jqXHR.responseJSON.message
                    ? jqXHR.responseJSON.message
                    : '未知错误，请刷新重试或联系客服';

                toast(errMsg);
                $('#rechargebtn').attr('disabled', false);
            });
        });
    })
</script>

