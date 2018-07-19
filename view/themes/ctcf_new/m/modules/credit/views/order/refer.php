<?php
$this->title = ("success" === $ret) ? "出借成功" : "出借失败";
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/buy-setting/setting.css?v=20160331">

<div class="row" id='bind-box'>
    <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
            <div>出借成功</div>
        <?php } else { ?>
            <div>出借失败</div>
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
<div class="row daojishi" id='bind-close1'>
    <div class="col-xs-12 page_padding">
        <?php if ('success' === $ret) { ?>
            <div>您已成功出借项目，可以进入我的出借查看出借详情</div>
        <?php } else { ?>
            <div>遇到问题请联系客服，电话：<a class="contact-tel" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><?= Yii::$app->params['platform_info.contact_tel'] ?></a></div>
        <?php } ?>
    </div>
    <?php if ('success' === $ret) { ?>
        <div class="col-xs-4"></div>
        <div class="col-xs-4">
            <a href="javascript:void(0)" onclick="location.replace('/user/user/orderdetail?asset_id=<?= $order['currentAsset']['id'] ?>')" class="bind-close1">查看详情</a>
        </div>
        <div class="col-xs-4"></div>
    <?php } else { ?>
        <div class="col-xs-4"></div>
        <div class="col-xs-4">
            <a href="javascript:void(0)" onclick="history.go(-1)" class="bind-close1">重新购买</a>
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