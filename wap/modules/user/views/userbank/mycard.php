<?php
$this->title = "我的银行卡";
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/mycard.css?na=1.0"/>
<div class="row bank-card" style="margin-bottom: 0!important;">
    <div class="col-xs-2 bank-img"><img src="<?= ASSETS_BASE_URI ?>images/bankicon/105.png" alt=""></div>
    <div class="col-xs-7 bank-content">
        <div class="bank-content1">建设银行</div>
        <div class="bank-content2">尾号8907 储蓄卡</div>
    </div>
    <div class="col-xs-3 bank-content">
        <div class="bank-content3">户名</div>
        <div class="bank-content4">左盟主</div>
    </div>
</div>
<div class="row mycard-tip">
    <div class="col-xs-12">*绑定的银行卡为唯一充值、提现银行卡</div>
</div>
<div class="row">
    <div class="col-xs-3"></div>
    <div class="col-xs-6 apply-exchange-en"><a class="apply-exchange">申请换卡</a></div>
    <div class="col-xs-3"></div>
</div>
<script>
    $(document).ready(function () {
        $('.apply-exchange').on('click', function () {
            $(this).text('换卡申请审核中').css({'color':'#999999','border-color':'#999999','width':'95%'});
        })
    })
</script>