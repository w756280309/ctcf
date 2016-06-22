<?php

$this->title = '最新资讯详情';
$this->registerCssFile(ASSETS_BASE_URI.'css/news/informationdetail.css', ['depends' => 'frontend\assets\FrontAsset']);

?>
<div class="wdjf-body">
    <div class="wdjf-ucenter clearfix">
        <div class="leftmenu">
            <!--左侧导航-->
            <div class="userAccount-left-nav">
                <?= $this->render('@frontend/views/news/left_nav.php', ['type'=>$type]) ?>
            </div>
        </div>
        <div class="rightcontent">
            <div class="information-box">
                <div class="information-header">
                    <span class="information-header-font">最新资讯</span>
                </div>
                <div class="news_notice_detail_title"><?= $new->title ?></div>
                <p class="news_details_time">
                    <span><?= empty($new->news_time) ? '' : date('Y-m-d', $new->news_time) ?></span>
                </p>
                <div class="news_text">
                    <?= $new->body ?>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
