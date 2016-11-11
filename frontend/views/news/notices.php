<?php

use frontend\assets\FrontAsset;

$this->title = $new->title;
$cssUrl = 'notice' === $type ? 'css/news/notice.css?v=161111-1' : 'css/news/mediumreport.css?v=161111';

$this->registerCssFile(ASSETS_BASE_URI.$cssUrl, ['depends' => FrontAsset::class]);
?>
<div class="wdjf-body">
    <div class="wdjf-ucenter">
        <!-- left-nav start-->
        <div class="userAccount-left-nav lf">
            <?= $this->render('@frontend/views/news/left_nav.php', ['type' => $type]) ?>
        </div>
        <!-- left-nav end-->

        <!-- right start-->
        <div class="right-box rg">
            <div class="right-header"><span class="title"><?= 'notice' === $type ? '网站公告' : '媒体报道' ?></span></div>
            <h2><?= $new->title ?></h2>
            <h3><span class="span_01"><?= empty($new->news_time) ? '' : date('Y-m-d', $new->news_time) ?></span><?= $new->source ? '来源：'.$new->source : '' ?></h3>
            <div class="txt-box">
                <?= $new->body ?>
            </div>
        </div>
        <!-- right end-->
    </div>
    <div class="clear"></div>
</div>
