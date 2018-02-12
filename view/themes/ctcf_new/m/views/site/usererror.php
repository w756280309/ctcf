<?php

$this->title="账户异常提醒";
$this->backUrl = '/';

?>

<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css">
<style>
    a.btn-normal {
        display: block;
    }

    a.btn-normal:active {
        color: #f44336;
    }
</style>

<div class="row flase-box">
    <div class="col-xs-12 text-align-ct">
        <img src="<?= ASSETS_BASE_URI ?>images/false.png" class="false-img" alt="失败">
    </div>
    <div class="col-xs-12 text-align-ct false-txt">当前用户已被冻结</div>
    <div class="col-xs-12 text-align-ct bg-height">
        客服联系电话：<a class="contact-tel" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><?= Yii::$app->params['platform_info.contact_tel'] ?></a>
    </div>
</div>
<br>
<div class="row login-sign-btn">
    <div class="col-xs-3"></div>
    <div class="col-xs-6 text-align-ct">
        <a class="btn-common btn-normal" href="/">回到首页</a>
    </div>
    <div class="col-xs-3"></div>
</div>
