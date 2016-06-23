<?php
    $this->title = '资金托管账户';
    \frontend\assets\FrontAsset::register($this);
    $this->registerCssFile('/css/useraccount/accountentrust.css');
?>
<div class="accountentrust-box">
    <div class="accountentrust-header">
        <div class="accountentrust-header-icon"></div>
        <span class="accountentrust-header-font">资金托管账户</span>
    </div>

    <div class="accountentrust-content">
        <p class="accountentrust"><span>真实姓名</span><i><?= $user->real_name?></i></p>
        <p class="accountentrust"><span>身份证号</span><i><?= substr($user->idcard,0,4)?>**** **** <?=substr($user->idcard,-4,4)?></i></p>
        <p class="accountentrust"><span>账户余额</span><i class="red-num "><?= number_format($user->lendAccount?$user->lendAccount->available_balance:0,2)?></i>元</p>
        <p class="accountentrust"><span>冻结余额</span><i class="red-num "><?= number_format($user->lendAccount?$user->lendAccount->freeze_balance:0,2)?></i>元</p>
        <p class="tuoguan">托管账号</p>
        <div class="tuoguan-img">登录联动优势查看账户总览及收支明细<a target="_blank"  href="https://www.soopay.net/" class="login-liandong">登录联动优势</a> </div>
    </div>
</div>
<script>
    $('.userAccount-left-nav .account').addClass('selected');
</script>
