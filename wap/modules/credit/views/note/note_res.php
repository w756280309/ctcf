<?php
$this->title = ("success" === $res) ? "发起转让成功" : "发起转让成功";
$this->backUrl = false;
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css?v=20160331">

<div class="row" id='bind-box'>
    <div class="col-xs-12">
        <?php if ('success' === $res) { ?>
            <div>发起转让成功</div>
        <?php } else { ?>
            <div>发起转让成功</div>
        <?php } ?>
    </div>
</div>
<div class="row" id='bind-true'>
    <div class="col-xs-12">
        <?php if ('success' === $res) { ?>
        <img src="<?= ASSETS_BASE_URI ?>images/bind-true.png" alt="">
        <?php } else { ?>
        <img src="<?= ASSETS_BASE_URI ?>images/bind-false.png" alt="">
        <?php } ?>
    </div>
</div>
<div class="row daojishi" id='bind-close1'>
    <div class="col-xs-12 page_padding">
        <?php if ('success' === $res) { ?>
            <div>您已成功发起转让，可以进入我的转让查看转让详情</div>
        <?php } else { ?>
            <div>遇到问题请联系客服，电话：<a class="contact-tel" href="tel:<?= Yii::$app->params['contact_tel'] ?>"><?= Yii::$app->params['contact_tel'] ?></a></div>
        <?php } ?>
    </div>
    <?php if ('success' === $res) { ?>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <a href="javascript:void(0)" onclick="location.replace('/credit/trade/assets?type=2')" class="bind-close1">我的转让</a>
    </div>
        <div class="col-xs-4"></div>
    <?php } else { ?>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <a href="javascript:void(0)" onclick="history.go(-1)" class="bind-close1">重新发起</a>
    </div>
    <div class="col-xs-4"></div>
    <?php } ?>
</div>
<div class="row" id='bind-close1'>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <a href="/" class="bind-close1">回到首页</a>
    </div>
    <div class="col-xs-4"></div>
</div>