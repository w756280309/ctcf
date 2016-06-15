<?php
$this->registerCssFile(ASSETS_BASE_URI.'css/left.css', ['depends' => 'frontend\assets\FrontAsset']);
?>

<div class="userAccount-left-nav">
    <ul>
        <li class="nav-head"><span>账户中心</span></li>
        <li class="nav-title"><span>我的账户</span></li>
        <li class="nav-content">
            <ul>
                <li class="<?= 'user/user/index' === Yii::$app->controller->action->getUniqueId() ? 'selected' : '' ?>"><a href="/user/user"><span class="star"></span>资产总览</a></li>
                <li class=""><a href=""><span class="star"></span>充值</a></li>
                <li class=""><a href=""><span class="star"></span>提现</a></li>
            </ul>
        </li>
        <li class="nav-title"><span>资产管理</span></li>
        <li class="nav-content">
            <ul>
                <li class=""><a href=""><span class="star"></span>我的理财</a></li>
                <li class="<?= 'user/coupon/index' === Yii::$app->controller->action->getUniqueId() ? 'selected' : '' ?>"><a href="/user/coupon/"><span class="star"></span>我的代金券</a></li>
                <li class="<?= 'user/user/mingxi' === Yii::$app->controller->action->getUniqueId() ? 'selected' : '' ?>"><a href="/user/user/mingxi"><span class="star"></span>交易明细</a></li>
            </ul>
        </li>
        <li class="nav-title"><span>账户管理</span></li>
        <li class="nav-content">
            <ul>
                <li class=""><a href=""><span class="star"></span>我的银行卡</a></li>
                <li class=""><a href=""><span class="star"></span>安全中心</a></li>
                <li class=""><a href=""><span class="star"></span>资金托管账户</a></li>
            </ul>
        </li>
    </ul>
</div>