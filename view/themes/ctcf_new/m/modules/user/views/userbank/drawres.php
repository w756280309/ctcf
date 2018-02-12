<?php
$this->title = '提现申请';
$this->backUrl = false;
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/buy-setting/setting.css?v=18021211">

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
        <img src="<?= ASSETS_BASE_URI ?>images/bind-true.png" alt="">
        <?php } else { ?>
        <img src="<?= ASSETS_BASE_URI ?>images/bind-false.png" alt="">
        <?php } ?>
    </div>
</div>
<div class="row daojishi">
     <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
        <div>工作日下午五点前提现当天到账</div>
        <div>工作日下午五点后提现下一个工作日到账</div>
        <div style="color:#ff6707">如遇节假日，到账时间将顺延到下个工作日哦！</div>
        <div>如有疑问请客服电话：<a class="contact-tel" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><?= Yii::$app->params['platform_info.contact_tel'] ?></a></div>
        <?php } else { ?>
        <div>请联系客服: <a style="color: #0c0c0c" class="contact-tel" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><?= Yii::$app->params['platform_info.contact_tel'] ?></a></div>
        <?php } ?>
     </div>
</div>
<div class="row" id='bind-close1'>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <a href="/user/user" class="bind-close1">返回账户</a>
    </div>
    <div class="col-xs-4"></div>
</div>
