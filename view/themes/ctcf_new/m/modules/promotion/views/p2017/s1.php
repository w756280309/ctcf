<?php

$this->title = 'TA的2017年报';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180102/css/share.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<style>
    .flex-content {
        background: url(<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/share-years-report-1.png) 0 0 no-repeat;
        -webkit-background-size: 100%;
        background-size: 100%;
        background-color: #EFCFB1;
    }
</style>
<div class="flex-content" id="app">
    <div class="header clearfix">
        <div class="header-nav">
            <div class="fl">
                <!--下面这个i是头像PHP加或者前台加？-->
                <i style="background-image:url(<?= $headImgUrl ?>);" class="header-img"></i>
            </div>
            <div class="fl">
                <p style="height: 0.64rem;"><?= $nickName ?></p>
                <p>给你分享了一份年报</p>
            </div>
        </div>
        <div class="header-summary">
            <p>我来到楚天财富已经<span><?= $registerToTodayDays ?></span>天</p>
            <p>获得了<span><?= $totalPoints ?></span>积分、<span><?= $couponNum ?></span>张代金券</p>
            <p><span><?= $bonusCouponNum ?></span>张加息券、<span><?= $totalRedPacket ?></span>元现金红包</p>
            <p>捐赠了<span><?= $charityAmount ?></span>元慈善金</p>
            <p>收益击败了<span><?= $totalProfitRanking ?>%</span>的用户</p>
        </div>
    </div>
    <div class="footer">
        <div class="footer-nav">
            <p>至今，楚天财富已安全运营<span><?= $platSafeDays ?></span>天</p>
            <p>已累计兑付<span><?= $platRefundAmount ?></span>亿元</p>
            <p>兑付率达<span>100%</span></p>
            <p>为客户赚取<span><?= $platRefundInterest ?></span>亿元</p>
            <p>未来，楚天财富将继续与您携手同行</p>
        </div>
        <a href="/promotion/p2017/annual-report" class="footer-button">查看我的年报</a>
    </div>
</div>