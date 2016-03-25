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
<div class="row" id='bind-close1'>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <?php if ('success' === $ret) { ?>
        <a href="/user/user/orderdetail?id=<?=$order->id ?>" class="bind-close1">查看详情</a>
        <?php } else { ?>
        <input id="" class="btn-common btn-normal" name="" type="button" value="重新投标" onclick="location.href='/deal/deal/index'">
        <?php } ?>
    </div>
    <div class="col-xs-4"></div>
</div>
<div class="row login-sign-btn">
    <div class="col-xs-3"></div>
    <div class="col-xs-6 text-align-ct">
        <a href="/" class="back-index" >回到首页</a>
    </div>
    <div class="col-xs-3"></div>
</div>