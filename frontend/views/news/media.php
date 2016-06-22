<?php

$this->title = '媒体报道';
$this->registerCssFile(ASSETS_BASE_URI.'css/news/mediumreport.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerCssFile(ASSETS_BASE_URI.'css/pagination.css', ['depends' => 'frontend\assets\FrontAsset']);

use common\widgets\Pager;
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
            <div class="right-header"><span class="title">媒体报道</span></div>

            <div class="box">
                <?php foreach ($model as $val) : ?>
                <div class="list xian">
                    <div class="lf list-lf">
                        <p class="a-box"><a href="/news/detail?type=<?= $type ?>&id=<?= $val->id ?>" class="a-title"><?= $val['title'] ?></a><span class="pos-time rg"><?= empty($val->news_time) ? '' : date('Y-m-d', $val->news_time) ?></span></p>
                        <p class="p-content"><?= $val->summary ?></p>
                    </div>
                    <div class="rg  list-rg">
<!--                        <img src="#" alt="媒体报道图片">-->
                    </div>
                    <div class="clear"></div>
                </div>
                <?php endforeach; ?>
                <!--分页 start-->
                <div class="pagination-content">
                    <?= Pager::widget(['pagination' => $pages]); ?>
                </div>
                <!--分页 end-->

            </div>
        </div>
        <!-- right end-->
    </div>
    <div class="clear"></div>
</div>

