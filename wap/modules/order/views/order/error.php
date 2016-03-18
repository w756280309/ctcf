<?php
$this->title="购买失败";

$this->registerJs('var yr='.number_format($deal->yield_rate, 2), 1);
$this->registerJs('var qixian='.$deal->expires, 1);

$this->registerJsFile(ASSETS_BASE_URI.'js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
$this->registerJsFile(ASSETS_BASE_URI.'js/order.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>

<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css">

<div class="row flase-box">
    <div class="col-xs-12 text-align-ct">
        <img src="<?= ASSETS_BASE_URI ?>images/false.png" class="false-img" alt="失败">
    </div>
    <div class="col-xs-12 text-align-ct false-txt">购买失败</div>
    <div class="col-xs-12 text-align-ct bg-height">客服联系电话：400-888-6888</div>
</div>
<div class="row login-sign-btn">
    <div class="col-xs-3"></div>
    <div class="col-xs-6 text-align-ct">
        <input id="" class="btn-common btn-normal" name="" type="button" value="重新投标" onclick="location.href='/deal/deal/index'">
        <a href="/" class="back-index" >回到首页</a>
    </div>
    <div class="col-xs-3"></div>
</div>
