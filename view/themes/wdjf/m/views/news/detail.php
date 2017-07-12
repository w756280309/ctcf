<?php

$this->title = '最新资讯';
$this->showIndexBottomNav = true;

$hostInfo = Yii::$app->request->hostInfo;
$referer = Yii::$app->request->referrer;

$this->backUrl = false === strpos($referer, $hostInfo) ? '/' : null;

?>
<link href="<?= ASSETS_BASE_URI ?>css/news.css?v=20161108" rel="stylesheet">
<link href="<?= ASSETS_BASE_URI ?>css/first.css?v=20160418-f" rel="stylesheet">

<!-- 主体 -->
<div class="information-details">
    <div class="header">
        <p class="header-title"><?= $new->title ?></p>
        <p class="header-time"><?= empty($new->news_time) ? '' : date('Y-m-d', $new->news_time) ?></p>
    </div>
    <div class="header-content">
        <?= $new->body ?>
    </div>
</div>