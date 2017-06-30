<?php

use common\utils\StringUtils;

?>

<?php foreach ($coupons as $coupon) : ?>
    <li class="coupons <?= in_array($coupon->id, $selectedCoupon) ? 'coupons-on' : 'coupons-off' ?>" sn="<?= $sn ?>" userCouponId="<?= $coupon->id ?>">
        <div class="coupon-lf lf">
            <p class="coupon-number">
                ￥<span class="coupon-shu"><?= StringUtils::amountFormat2($coupon->couponType->amount) ?></span>
            </p>
            <p class="coupon-condition">
                满<span class="coupon-condition shu"><!--
                    --><?= StringUtils::amountFormat1('{amount}{unit}', $coupon->couponType->minInvest) ?><!--
                --></span>可用
            </p>
        </div>
        <div class="coupon-rg rg">
            <p class="coupon-title"><?= $coupon->couponType->name ?></p>
            <p class="coupon-limited">
<?= $coupon->couponType->loanExpires ? '期限满'.$coupon->couponType->loanExpires.'天可用(除转让)' : '新手标、转让不可用' ?>
            </p>
            <p class="coupon-date">有效期至<span class="coupon-riqi"><?= $coupon->expiryDate ?></span></p>
        </div>
    </li>
<?php endforeach; ?>
