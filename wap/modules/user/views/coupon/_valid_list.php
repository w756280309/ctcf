<?php

use common\utils\StringUtils;

?>

<?php foreach ($coupons as $coupon) : ?>
    <div class="box">
        <div class="row coupon_num" onclick="location.replace('/order/order?sn=<?= $sn ?>&money=<?= $money ?>&userCouponId=<?= $coupon->id ?>')">
            <img src="<?= ASSETS_BASE_URI ?>images/ok_ticket.png" alt="券">
            <div class="row pos_box">
                <div class="col-xs-2"></div>
                <div class="col-xs-4 numbers">¥<span><?= StringUtils::amountFormat2($coupon->couponType->amount) ?></span></div>
                <div class="col-xs-6 right_tip">
                    <div class="a_height"></div>
                    <div class="b_height">
                        <p class="b_h4"><?= $coupon->couponType->name ?></p>
                    </div>
                    <div class="c_height">
                        <p class="condition1">单笔投资满<?= StringUtils::amountFormat1('{amount}{unit}', $coupon->couponType->minInvest) ?>可用</p>
                    </div>
                    <div class="d_height"></div>
                    <div class="c_height">
                        <p  class="condition1">新手标、转让不可用</p>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="row gray_time">
            <img src="<?= ASSETS_BASE_URI ?>images/coupon_img.png" alt="底图">
            <div class="row pos_box">
                <div class="col-xs-8 ticket_time">有效期至<?= $coupon->expiryDate ?></div>
                <div class="col-xs-4 no-use">未使用</div>
            </div>
        </div>
    </div>
<?php endforeach; ?>