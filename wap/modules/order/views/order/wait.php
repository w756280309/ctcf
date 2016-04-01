<?php
$this->title= "订单处理中";

$this->registerJsFile(ASSETS_BASE_URI.'js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>

<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css?v=20160331">
<div class="row" id='bind-box'>
    <div class="col-xs-12">
        <div>订单处理中……</div>
    </div>
</div>
<div class="row" id='bind-true'>
    
</div>
<div class="row daojishi" id='bind-close1'>
    <div class="col-xs-12 page_padding">
        <div>遇到问题请联系客服，电话：<?= Yii::$app->params['contact_tel'] ?></div>
    </div>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <a href="/user/user/myorder" class="bind-close1">查看订单</a>
    </div>
     <div class="col-xs-4"></div>
</div>
<div class="row" id='bind-close1'>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <a href="/" class="bind-close1">回到首页</a>
    </div>
    <div class="col-xs-4"></div>
</div>