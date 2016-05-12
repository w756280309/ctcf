<?php
$this->title = ('success' === $ret) ? "开通成功" : "开通失败";
$this->backUrl = false;
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css">

<div class="row" id='bind-box'>
    <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
        <div>资金托管账户开通成功</div>
        <?php } else { ?>
        <div>开通失败</div>
        <?php } ?>
    </div>
</div>
<div class="row" id='bind-true'>
    <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
        <img src="<?= ASSETS_BASE_URI ?>images/bind-true.png" alt="">
        <?php } else { ?>
        <img src="<?= ASSETS_BASE_URI ?>images/bind-false.png" alt="">
        <?php } ?>
    </div>
</div>
<div class="row rz-msg">
    <div class="col-xs-1"></div>
     <div class="col-xs-10">
        <?php if ('success' === $ret) { ?>
         <div><span style="font-weight: bold">支付密码将会以短信的形式发送到您的手机上</span>,请注意查收并妥善保存.支付密码为6位随机数,可根据短信内容修改密码</div>
        <?php } else { ?>
         <div style="text-align: center">请联系客服: <?= Yii::$app->params['contact_tel'] ?></div>
        <?php } ?>
     </div>
    <div class="col-xs-1"></div>
</div>
<?php if ('success' === $ret) { ?>
<div class="row" id='bind-close1'>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <a href="javascript:nextStep()" class="bind-close1">下一步</a>
    </div>
    <div class="col-xs-4"></div>
</div>

<?php } else { ?>
<div class="row" id='bind-close1'>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <a href="/user/user" class="bind-close1">返回账户</a>
    </div>
    <div class="col-xs-4"></div>
</div>
<?php } ?>
<script type="text/javascript">
function nextStep() {
    alertTrue(function(){
        location.href = "/user/qpay/binding/umpmianmi";
    });
}
function alertTrue(trued) {
        var chongzhi = $('<div class="mask" style="display: block"></div><div class="bing-info show"> <p class="tishi-p" style="line-height: 20px;">将为您开通免密支付功能之后进行投资时,无需输入资金托管账户支付密码,但是,当您需要提现时,为确保您的资金安全,仍需要输入支付密码</p > <div class="bind-btn"> <span class="true">确定</span> </div> </div>');
        $(chongzhi).insertAfter($('#bind-box'));
        $('.bing-info').on('click', function () {
            $(chongzhi).remove();
            trued();
        })
    }
</script>
