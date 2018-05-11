<?php

$this->title= '授权登录失败';
$this->registerCssFile(ASSETS_BASE_URI.'css/deal/buy.css');
?>

<div class="invest-box clearfix">
    <div class="invest-container">
        <div class="invest-container-box invest-success">
            <div class="invest-content" style="width: 500px;">
                <p style="font-size: 25px;text-align: center;margin-bottom: 30px;"><span><?= $message ?></span></p>
                <p class="buy-txt-tip" style="font-size: 14px;">遇到问题请联系客服，电话：<?= Yii::$app->params['platform_info.contact_tel'] ?> <a href="/site/index" class="bind-close1">回到首页>>></a></p>
            </div>
        </div>
    </div>
</div>
