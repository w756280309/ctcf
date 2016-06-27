<?php

$this->title = $new->title;
$this->registerCssFile(ASSETS_BASE_URI.'css/news/notice.css', ['depends' => 'frontend\assets\FrontAsset']);

?>
<div class="wdjf-body">
    <div class="wdjf-ucenter">
        <!-- left-nav start-->
        <div class="userAccount-left-nav lf">
            <?= $this->render('@frontend/views/news/left_nav.php', ['type'=>$type]) ?>
        </div>
        <!-- left-nav end-->

        <!-- right start-->
        <div class="right-box rg">
            <div class="right-header"><span class="title">网站公告</span></div>
            <h2><?= $new->title ?></h2>
            <h3><span class="span_01"><?= empty($new->news_time) ? '' : date('Y-m-d', $new->news_time) ?></span>来源：<?= $new->source ?></h3>
            <div class="txt-box">
                <?= $new->body ?>
            </div>
        </div>
        <!-- right end-->
    </div>
    <div class="clear"></div>
</div>
