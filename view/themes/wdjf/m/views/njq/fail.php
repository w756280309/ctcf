<?php

$this->title = '授权登录失败';
$this->showViewport = false;
$redirect = Yii::$app->request->get('redirect');
$url = filter_var($redirect, FILTER_VALIDATE_URL) ? $redirect : Yii::$app->request->hostInfo.'/site/index';

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=20170906">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/tie-card/css/operate.css?v=20170906">
<script src="<?= FE_BASE_URI ?>/libs/lib.flexible3.js"></script>

<div class="flex-content">
    <img class="sucImg" src="<?= FE_BASE_URI ?>wap/tie-card/img/fail.png" alt="">
    <p class="sucMes f17" style="padding: 0 20px;margin-top: 15px;"><?= $message ?></p>
    <a class="instant f18 mg-110" href="<?= $url ?>" style="display: none">我 知 道 了</a>
    <p class="f13 color-6 mg-17">遇到问题，请联系客服</p>
    <p class="f13 color-6">客服电话：<a class="color-blue f15" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><?= Yii::$app->params['platform_info.contact_tel'] ?></a></p>
</div>