<?php
$this->title= ("success" === $ret) ? "购买成功" : "购买失败";

$this->registerJsFile(ASSETS_BASE_URI.'js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>

<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css">
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
        <img src="<?= ASSETS_BASE_URI ?>images/bind-true.png" alt="">
        <?php } else { ?>
        <img src="<?= ASSETS_BASE_URI ?>images/bind-false.png" alt="">
        <?php } ?>
    </div>
</div>
<div class="row daojishi" id='bind-close1'>
    <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
            <div>您已成功认购项目，可以进入我的理财查看认购详情</div>
        <?php } else { ?>
            <div>遇到问题请联系客服，电话：<?= Yii::$app->params['contact_tel'] ?></div>
        <?php } ?>
    </div>
    <?php if ('success' === $ret) { ?>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <a href="/user/user/orderdetail?id=<?=$order->id ?>" class="bind-close1">查看详情</a>
    </div>
        <div class="col-xs-4"></div>
    <?php }?>
</div>
<div class="row" id='bind-close1'>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <a href="/" class="bind-close1">回到首页</a>
    </div>
    <div class="col-xs-4"></div>
</div>