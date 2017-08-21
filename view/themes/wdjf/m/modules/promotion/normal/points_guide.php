<?php
$this->title = '限时积分新品';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/point-shop/css/index.css?v=1">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<?php
    $bgColor = isset($detail['bgColor']) ? $detail['bgColor'] : '';
    $topBox = isset($detail['top']) ? $detail['top'] : '';
    $middleBox = isset($detail['middle']) ? $detail['middle'] : '';
    $bottomBox = isset($detail['bottom']) ? $detail['bottom'] : '';
    $banner = isset($detail['banner']) ? $detail['banner'] : '';
    $baseUrl = '/mall/portal/guest?dbredirect=';
?>
<div class="flex-content" style="background:url(<?= FE_BASE_URI.$bgColor['url'] ?>);background-size:contain">
    <div class="part-one" style="background: url(<?= FE_BASE_URI.$banner['url'] ?>) no-repeat;background-size: contain;"></div>
    <div class="part-two">
        <?php if (!empty($topBox)) { ?>
            <a href="<?= $baseUrl.$topBox['duibaUrl'] ?>" class="gift-outbox gift-one" style="padding-right: 0.1rem;">
                <img src="<?= FE_BASE_URI.$topBox['imgUrl'] ?>" alt="" class="lf">
                <p class="gift-name"><?= $topBox['mainTitle'] ?></p>
                <p class="gift-introduce"><?= $topBox['subTitle'] ?></p>
                <p class="gift-point"><?= $topBox['points'] ?></p>
                <div class="gift-button">立即抢购</div>
            </a>
        <?php } ?>
        <div class="fix-gift clearfix">
            <?php foreach ($middleBox as $key => $box) { ?>
                <a href="<?= $baseUrl.$box['duibaUrl'] ?>" class="gift-box fix-gift clearfix <?= $box['class'] ?>">
                    <img src="<?= FE_BASE_URI.$box['imgUrl'] ?>" alt="">
                    <p class="gift-name"><?= $box['mainTitle'] ?></p>
                    <p class="gift-introduce"><?= $box['subTitle'] ?></p>
                    <p class="gift-point"><?= $box['points'] ?></p>
                    <div class="gift-button">立即抢购</div>
                </a>
            <?php } ?>
        </div>
        <?php if (!empty($bottomBox)) { ?>
        <a href="<?= $baseUrl.$bottomBox['duibaUrl'] ?>" class="gift-outbox gift-six">
            <img src="<?= FE_BASE_URI.$bottomBox['imgUrl'] ?>" alt="" class="rg">
            <p class="gift-name"><?= $bottomBox['mainTitle'] ?></p>
            <p class="gift-introduce"><?= $bottomBox['subTitle'] ?></p>
            <p class="gift-point"><?= $bottomBox['points'] ?></p>
            <div class="gift-button">立即抢购</div>
        </a>
        <?php } ?>
    </div>
    <div class="part-three">
        <img src="<?= FE_BASE_URI ?>wap/point-shop/images/pic-howto.png" alt="" class="how-title">
        <div class="get-box get-one">
            <a href="/user/checkin">去签到</a>
        </div>
        <div class="get-box get-two">
            <a href="/deal/deal/index">去理财</a>
        </div>
        <img src="<?= FE_BASE_URI ?>wap/point-shop/images/pic_remind.png" alt="" class="how-remind">
    </div>
</div>
