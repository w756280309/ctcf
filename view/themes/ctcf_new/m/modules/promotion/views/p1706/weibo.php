<?php

$this->title = '楚天财富福利';
$this->share = $share;

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/marketing-weibo/css/index.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>

<div class="flex-content">
    <img src="<?= FE_BASE_URI ?>wap/campaigns/marketing-weibo/images/bg_banner.jpg" alt="" class="top-banner">
    <!-- 二维码图片修改位置,名字的数字递增即可 -->
    <img src="<?= FE_BASE_URI ?>wap/campaigns/marketing-weibo/images/code/3.png" alt="" class="pic-group">
    <p class="remind" style="margin-top: 1em;">*本福利由楚天财富提供</p>
    <p class="remind">楚天财富，市民身边的财富管家。</p>
    <p class="remind">由湖北日报新媒体集团成立的互联网金融平台，提供安</p>
    <p class="remind">全、便捷、可靠的网上金融服务</p>

    <div class="rule-box">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/marketing-weibo/images/list-banner.png" alt="" class="rule-banner">
        <ol class="rule-list">
            <li>注册即送<span>288元</span>红包；</li>
            <li>新手专享预期年化<span>10%</span>收益率；</li>
            <li>首次出借送1400积分，可兑换<span>50元</span>沃尔玛超市购物卡。</li>
        </ol>
    </div>
    <a href="<?= Yii::$app->params['clientOption']['host']['frontend'] ?>" class="go-look">去看看</a>
</div>
