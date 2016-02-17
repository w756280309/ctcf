<?php
$this->title="绑卡受理结果";

$this->registerJsFile('/js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>

<link rel="stylesheet" href="/css/setting.css">

<div class="row flase-box">
    <div class="col-xs-12 text-align-ct">
        <img src="/images/<?php if ('error' === $ret) { echo "false"; } else { echo 'true'; } ?>.png" class="false-img">
    </div>
    <div class="col-xs-12 text-align-ct false-txt">
    <?php 
        if ('success' === $ret) {
    ?>
        绑卡受理成功
    <?php 
        } else {
    ?>
        绑卡受理失败
    <?php     
        } 
    ?>
    </div>
    <div class="col-xs-12 text-align-ct bg-height">客服联系电话：<?= Yii::$app->params['contact_tel'] ?></div>
</div>
<div class="row login-sign-btn">
    <div class="col-xs-3"></div>
    <div class="col-xs-6 text-align-ct">
        <a href="/user/user" class="back-index" >回到账户中心</a>
    </div>
    <div class="col-xs-3"></div>
</div>
