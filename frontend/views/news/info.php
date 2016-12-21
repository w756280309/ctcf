<?php

$this->title = '最新资讯';

$this->registerCssFile(ASSETS_BASE_URI.'css/pagination.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerCssFile(ASSETS_BASE_URI.'css/news/informationlist.css', ['depends' => 'frontend\assets\FrontAsset']);

use common\widgets\Pager;
?>
<div class="wdjf-body">
    <div class="wdjf-ucenter clearfix">
        <div class="leftmenu">
            <?= $this->render('@frontend/views/news/left_nav.php', ['type'=>$type]) ?>
        </div>
        <div class="rightcontent">

            <div class="information-box">
                <div class="information-header">
                    <span class="information-header-font"><?= $title ?></span>
                </div>
                <?php foreach ($model as $val) : ?>
                <dl class="com_news_01">
                    <dd class="com_text com_left">
                        <p class="com_title">
                            <span class="size_16"><a href="/news/detail?type=<?= $type ?>&id=<?= $val->id ?>"><?= $val->title ?></a></span>
                            <span class="size_date"><?= empty($val->news_time) ? '' : date('Y-m-d', $val->news_time) ?></span>
                        </p>
                        <p class="size_12"><?= $val->summary ?></p>
                    </dd>
                </dl>
                <div class="xuxian"></div>
                <?php endforeach; ?>

                <div class="pagination-content">
                    <?= Pager::widget(['pagination' => $pages]); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>