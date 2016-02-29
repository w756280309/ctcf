<?php
$this->title = '提现申请';
?>
<link rel="stylesheet" href="/css/setting.css">
<div class="row" id='bind-box'>
    <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
        <div>提现申请成功</div>
        <?php } else { ?>
        <div>提现申请失败</div>
        <?php } ?>
    </div>
</div>
<div class="row" id='bind-true'>
    <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
        <img src="/images/bind-true.png" alt="">
        <?php } else { ?>
        <img src="/images/bind-false.png" alt="">
        <?php } ?>
    </div>
</div>
<div class="row daojishi">
     <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
        <div>您申请的提现将会在24小时内到账</div>
        <div>如有疑问请客服电话：<?= Yii::$app->params['contact_tel'] ?></div>
        <?php } else { ?>
        <div>请联系客服: <?= Yii::$app->params['contact_tel'] ?></div>
        <?php } ?>
     </div>
</div>
<div class="row" id='bind-close1'>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <a href="/user/user/index" class="bind-close1">返回账户</a>
    </div>
    <div class="col-xs-4"></div>
</div>
