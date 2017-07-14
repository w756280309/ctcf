<?php

$this->title = ('success' === $ret) ? '购买成功' : '购买失败';
$this->backUrl = false;

?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css?v=20160714">

<div class="row" id='bind-box'>
    <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
            <div>购买成功</div>
        <?php } else { ?>
            <div>购买失败</div>
        <?php } ?>
    </div>
</div>

<div class="row" id='bind-true'>
    <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
        <img src="<?= ASSETS_BASE_URI ?>images/bind_true.png" alt="">
        <?php } else { ?>
        <img src="<?= ASSETS_BASE_URI ?>images/bind-false.png" alt="">
        <?php } ?>
    </div>
</div>

<div class="row daojishi" id='bind-close1'>
    <div class="col-xs-1"></div>
    <div class="col-xs-5">
        <?php if ('success' === $ret) { ?>
            <a href="javascript:void(0)" onclick="location.replace('/user/user/orderdetail?id=<?= $order->id ?>')" class="bind-close1">查看详情</a>
        <?php } else { ?>
            <a href="javascript:void(0)" onclick="history.go(-1)" class="bind-close1">重新购买</a>
        <?php } ?>
    </div>
    <div class="col-xs-5">
        <a href="/?_mark=1" class="bind-close1">回到首页</a>
    </div>
    <div class="col-xs-1"></div>

    <div class="col-xs-12 page_padding">
        <?php if ('success' === $ret) { ?>
            <div>绑定公众号<font color="#999999">（温都金服全拼）</font>: "<font color="#FF8000">wendujinfu</font>"</div>
            <div>可以及时了解自己的收益，还送10个积分哦</div>
        <?php } else { ?>
            <div>遇到问题请联系客服，电话：<a class="contact-tel" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><?= Yii::$app->params['platform_info.contact_tel'] ?></a></div>
        <?php } ?>
    </div>

    <?php if ('success' === $ret) { ?>
        <div class="checkin"><a href="/user/checkin"><img src="<?= ASSETS_BASE_URI ?>images/checkin.jpg" alt=""></a></div>
    <?php } ?>
</div>
