<?php
$this->title = "我的银行卡";
$this->backUrl = '/system/system/safecenter';
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/mycard.css?v=20160503"/>

<div class="row bank-card" style="margin-bottom: 0!important;">
    <div class="col-xs-2 bank-img"><img src="<?= ASSETS_BASE_URI ?>images/bankicon/<?= $userBank->bank_id ?>.png"></div>
    <div class="col-xs-7 bank-content">
        <div class="bank-content1"><?= $userBank->bank_name ?></div>
        <div class="bank-content2">尾号<?= $userBank->card_number ? substr($userBank->card_number, -4) : '' ?> 储蓄卡</div>
    </div>
    <div class="col-xs-3 bank-content">
        <div class="bank-content3">户名</div>
        <div class="bank-content4"><?= $userBank->account ?></div>
    </div>
</div>
<div class="row mycard-tip">
    <div class="col-xs-12">*绑定的银行卡为唯一充值、提现银行卡</div>
</div>
<?php if (empty($bankcardUpdate)) { ?>
<div class="row">
    <div class="col-xs-3"></div>
    <div class="col-xs-6 apply-exchange-en"><a class="apply-exchange" href="/user/userbank/updatecard">申请换卡</a></div>
    <div class="col-xs-3"></div>
</div>
<?php } else { ?>
<div class="row">
    <div class="col-xs-3"></div>
    <div class="col-xs-6 apply-exchange-en">
        <div class="apply-exchange-zhong">换卡申请审核中</div>
    </div>
    <div class="col-xs-3"></div>
</div>
<?php } ?>