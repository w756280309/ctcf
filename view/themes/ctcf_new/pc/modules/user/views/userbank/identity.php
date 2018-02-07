<?php

$this->registerCssFile('/css/useraccount/chargedeposit.css');
$this->title = $title;
?>

<div class="charge-box">
    <div class="charge-header">
        <div class="charge-header-icon"></div>
        <span class="charge-header-font">
            <?= $title ?>
        </span>
    </div>
    <div class="charge-content">
        <span>开通联动优势资金托管账户，享资金安全保障</span>
        <a style="cursor: pointer;" onclick="location.href='/user/identity'">立即开通</a>
        <div class="clear"></div>
    </div>
</div>
<div class="charge-explain">
    <p class="charge-explain-title">为什么要开通第三方资金托管账户？</p>
    <div class="charge-explain-content">
        <span class="span-left">合法合规的需要：</span>
        <span class="span-right">按照监管部门要求，互联网金融平台客户资金需第三方存管。</span>
        <div class="clear"></div>
    </div>
    <div class="charge-explain-content">
        <span class="span-left">资金安全的需要：</span>
        <span class="span-right">开通第三方资金存管账户后，可避免资金挪用风险，用户完全拥有资金自主使用权，可有效保证投/融资双方资金安全。</span>
        <div class="clear"></div>
    </div>
</div>