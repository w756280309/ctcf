<?php

$this->title = '注册成功';

//跳转规则,根据登录用户ID的奇偶来判断,奇数跳转首页,偶数跳转我的理财;没有登录也跳转到首页;
$toUrl = '/';
if ($user && !($user->id % 2)) {
    $toUrl = '/deal/deal/index';
}

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>libs/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/wendumao/css/registerSucc.css?v=20170213">
<script  src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script  src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script  src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>

<div class="container flex-content">
    <div class="header f16">
        <img src="<?= FE_BASE_URI ?>wap/wendumao/images/tick_01.png" alt="">
        <p>您已成功注册楚天财富</p>
        <p>现在投资即可领取楚天猫充值卡</p>
    </div>
    <ul class="liststep f16">
        <li><span class="f24 lf">1</span>注册成功</li>
        <li><a class="go-deal" href="<?= $toUrl ?>"><span class="f24 lf">2</span>首次投资1000元即可获得积分</a></li>
        <li><span class="f24 lf">3</span>前往积分商城兑换楚天猫充值卡</li>
    </ul>

    <a class="f15 invest" href="<?= $toUrl ?>">去理财</a>

    <p class="f12">客服热线：<a class="f15" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><?= Yii::$app->params['platform_info.contact_tel'] ?></a>(9:00~20:00)</p>
</div>

<script>
    $(function() {
        FastClick.attach(document.body);
    });
</script>
