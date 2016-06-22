<?php

$this->registerCssFile(ASSETS_BASE_URI.'css/news/left_nav.css', ['depends' => 'frontend\assets\FrontAsset']);


?>

<div class="userAccount-left-nav">
    <ul>
        <li class="nav-head"><span>最新动态</span></li>
        <li class="nav-title"><span>新闻报道</span></li>
        <li class="nav-content">
            <ul>
                <li class="<?= ('news/index' === Yii::$app->controller->action->getUniqueId() || 'news/detail' === Yii::$app->controller->action->getUniqueId()) && "media" === $type ? 'selected' : '' ?>"><a href="/news/index?type=media"><span class="star"></span>媒体报道</a></li>
                <li class="<?= ('news/index' === Yii::$app->controller->action->getUniqueId() || 'news/detail' === Yii::$app->controller->action->getUniqueId()) && "info" == $type ? 'selected' : '' ?>"><a href="/news/index?type=info"><span class="star"></span>最新资讯</a></li>
            </ul>
        </li>
        <li class="nav-title"><span>相关公告</span></li>
        <li class="nav-content">
            <ul>
                <li class="<?= ('news/index' === Yii::$app->controller->action->getUniqueId() || 'news/detail' === Yii::$app->controller->action->getUniqueId()) && "notice" === $type ? 'selected' : '' ?>"><a href="/news/index?type=notice"><span class="star"></span>网站公告</a></li>
            </ul>
        </li>
        <li class="nav-title"><span>关于我们</span></li>
        <li class="nav-content">
            <ul>
                <li class="<?= 'helpcenter/about' === Yii::$app->controller->action->getUniqueId() ? 'selected' : '' ?>"><a href="/helpcenter/about/"><span class="star"></span>关于我们</a></li>
                <li class="<?= 'helpcenter/advantage' === Yii::$app->controller->action->getUniqueId() ? 'selected' : '' ?>"><a href="/helpcenter/advantage/"><span class="star"></span>平台优势</a></li>
                <li class="<?= 'helpcenter/contact' === Yii::$app->controller->action->getUniqueId() ? 'selected' : '' ?>"><a href="/helpcenter/contact/"><span class="star"></span>联系我们</a></li>
            </ul>
        </li>
    </ul>
</div>
