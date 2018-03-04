<?php

$this->title = 'TA的2017年报';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180102/css/share.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<style>
    div.new-share {
        background: url(<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/share-new-bg-1.png) 0 0 no-repeat;
        -webkit-background-size: 100% 100%;
        background-size: 100% 100%;
    }
</style>
<div class="flex-content new-share" id="app">
    <div class="new-share-nav">
        <div class="new-nav-contain">
            <p>至今，楚天财富已安全运营<span><?= $platSafeDays ?></span>天</p>
            <p>已累计兑付<span><?= $platRefundAmount ?></span>亿元</p>
            <p>兑付率达<span>100%</span></p>
            <p>为客户赚取<span><?= $platRefundInterest ?></span>亿元</p>
            <p>未来，楚天财富将继续与您携手同行</p>
        </div>
        <a href="/promotion/p2017/annual-report" class="new-nav-button">查看我的年报</a>
    </div>
</div>