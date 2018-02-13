<?php

use common\utils\StringUtils;

$this->title = '我的积分';
$backUrl = \Yii::$app->request->referrer;
$this->backUrl = $backUrl ? $backUrl : '/?_mark='.time();

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css??v=20170906">
<!--<link rel="stylesheet" href="--><?//= FE_BASE_URI ?><!--wap/point/css/index.css?v=20170714-v">-->
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/account-coupon/jf-point.css?v=18021311">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>

<div class="shoptop">
    <span><?= $user->points ? StringUtils::amountFormat2($user->points) : 0 ?></span>
    <p>可用积分</p>
</div>
<div class="shopnav">
    <ul class="clearfix">
        <li class="lf">
            <a href="/mall/point/list">
                <img src="<?= FE_BASE_URI ?>wap/point/img/icon_06.png" alt="">
                <p>积分明细</p>
            </a>
        </li>
        <li class="lf">
            <a href="/mall/point/rules">
                <img src="<?= FE_BASE_URI ?>wap/point/img/icon_07.png" alt="">
                <p>积分规则</p>
            </a>
        </li>
        <li class="lf">
            <a href="/site/app-download?redirect=/mall/portal">
                <img src="<?= FE_BASE_URI ?>wap/point/img/icon_08.png" alt="">
                <p>积分商城</p>
            </a>
        </li>
    </ul>
</div>
<a href="/mall/point/prize-list" class="shoprecord">
    兑换记录
</a>
<div class="checkin">
    <a href="/user/checkin"><img src="<?= ASSETS_BASE_URI ?>images/checkin.jpg" alt=""></a>
</div>
