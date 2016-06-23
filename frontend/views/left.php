<?php
$this->registerCssFile(ASSETS_BASE_URI.'css/left.css', ['depends' => 'frontend\assets\FrontAsset']);
$action = Yii::$app->controller->action->getUniqueId();
?>

<div class="userAccount-left-nav">
    <ul>
        <li class="nav-head"><span>账户中心</span></li>
        <li class="nav-title"><span>我的账户</span></li>
        <li class="nav-content">
            <ul>
                <li class="<?= 'user/user/index' === Yii::$app->controller->action->getUniqueId() ? 'selected' : '' ?>"><a href="/user/user"><span class="star"></span>资产总览</a></li>
                <li class="recharge <?= ('user/userbank/recharge' == $action || 'user/recharge/init' == $action) ? 'selected' : '' ?>"><a href="/user/recharge/init"><span class="star"></span>充值</a></li>
                <li class="draw <?= ('user/draw/tixian' == $action) ? 'selected' : '' ?>"><a href="/user/draw/tixian"><span class="star"></span>提现</a></li>
            </ul>
        </li>
        <li class="nav-title"><span>资产管理</span></li>
        <li class="nav-content">
            <ul>
                <li class="<?= 'user/user/myorder' === $action ? 'selected' : '' ?>"><a href="/user/user/myorder"><span class="star"></span>我的理财</a></li>
                <li class="<?= 'user/coupon/index' === $action ? 'selected' : '' ?>"><a href="/user/coupon/"><span class="star"></span>我的代金券</a></li>
                <li class="<?= 'user/user/mingxi' === $action ? 'selected' : '' ?>"><a href="/user/user/mingxi"><span class="star"></span>交易明细</a></li>
            </ul>
        </li>
        <li class="nav-title"><span>账户管理</span></li>
        <li class="nav-content">
            <ul>
                <li class="<?= in_array($action, [
                    'user/userbank/mybankcard',
                    'user/userbank/bindcard',
                    'user/userbank/updatecard',
                    'user/userbank/xiane',
                ]) ? 'selected' : '' ?> mycard"><a href="/user/userbank/mybankcard"><span class="star"></span>我的银行卡</a></li>
                <li class="<?= 'user/securitycenter/index' === $action ? 'selected' : '' ?>"><a href="/user/securitycenter/"><span class="star"></span>安全中心</a></li>
                <li class="account <?= 'user/userbank/idcardrz' === $action ? 'selected' : '' ?>"><a href="/user/userbank/identity"><span class="star"></span>资金托管账户</a></li>
            </ul>
        </li>
    </ul>
</div>
