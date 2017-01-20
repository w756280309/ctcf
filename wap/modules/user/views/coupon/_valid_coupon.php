<?php

use common\utils\StringUtils;

?>

<?php if (null === $coupon) { ?>
    <div class="col-xs-4 safe-txt text-align-ct">使用代金券</div>
    <div class="col-xs-8 safe-txt" onclick="toCoupon()"><span class="notice">请选择</span></div>
<?php } else { ?>
    <div class="col-xs-4 safe-txt text-align-ct">代金券抵扣</div>
    <div class="col-xs-5 safe-txt" onclick="toCoupon()"><?= StringUtils::amountFormat2($coupon->couponType->amount) ?>元</div>
    <div class="col-xs-3 safe-txt text-align-ct" onclick="resetCoupon()">清除</div>
    <input name="couponId" id="couponId" type="text" value="<?= $coupon->id ?>" hidden="hidden">
    <input name="couponMoney" id="couponMoney" type="text" value="<?= $coupon->couponType->amount ?>" hidden="hidden">
<?php } ?>