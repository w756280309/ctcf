<?php

$this->registerCssFile(ASSETS_BASE_URI.'css/help/operation.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerCssFile(ASSETS_BASE_URI.'css/help/left_nav.css', ['depends' => 'frontend\assets\FrontAsset']);

?>

<div class="userAccount-left-nav">
    <ul>
        <li class="nav-head"><span>帮助中心</span></li>
        <li class="nav-title"><span>操作篇</span></li>
        <li class="nav-content">
            <ul>
                <li class="scrollClick top1 <?= 'helpcenter/operation' === Yii::$app->controller->action->getUniqueId() && 0 == $type ? 'selected' : '' ?>"><a href="/helpcenter/operation?type=0"><span class="star"></span>注册登录</a></li>
                <li class="scrollClick top2 <?= 'helpcenter/operation' === Yii::$app->controller->action->getUniqueId() && 1 == $type ? 'selected' : '' ?>"><a href="/helpcenter/operation?type=1"><span class="star"></span>开通资金托管</a></li>
                <li class="scrollClick top3 <?= 'helpcenter/operation' === Yii::$app->controller->action->getUniqueId() && 2 == $type ? 'selected' : '' ?>"><a href="/helpcenter/operation?type=2"><span class="star"></span>绑卡充值</a></li>
                <li class="scrollClick top4 <?= 'helpcenter/operation' === Yii::$app->controller->action->getUniqueId() && 3 == $type ? 'selected' : '' ?>"><a href="/helpcenter/operation?type=3"><span class="star"></span>投资提现</a></li>
            </ul>
        </li>
        <li class="nav-title"><span>安全篇</span></li>
        <li class="nav-content">
            <ul>
                <li class="<?= 'helpcenter/security' === Yii::$app->controller->action->getUniqueId() && 0 == $type ? 'selected' : '' ?>"><a href="/helpcenter/security/"><span class="star"></span>安全篇</a></li>
            </ul>
        </li>
        <li class="nav-title"><span>背景篇</span></li>
        <li class="nav-content">
            <ul>
                <li class="<?= 'helpcenter/background' === Yii::$app->controller->action->getUniqueId() && 0 == $type ? 'selected' : '' ?>"><a href="/helpcenter/background/"><span class="star"></span>背景篇</a></li>
            </ul>
        </li>
        <li class="nav-title"><span>产品篇</span></li>
        <li class="nav-content">
            <ul>
                <li class="<?= 'helpcenter/product' === Yii::$app->controller->action->getUniqueId() && 0 == $type ? 'selected' : '' ?>"><a href="/helpcenter/product/"><span class="star"></span>产品篇</a></li>
            </ul>
        </li>
    </ul>
</div>
