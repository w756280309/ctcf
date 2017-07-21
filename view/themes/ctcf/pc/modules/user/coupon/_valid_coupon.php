<?php

use common\utils\StringUtils;
use yii\helpers\ArrayHelper;

?>

<?php if (!empty($validCoupons)) : ?>
    <?php
    $couponCount = count($validCoupons);
    $selectedAmount = 0;
    $selectedCount = 0;
    $selectedIds = [];

    if (!empty($selectedCoupons)) {
        $selectedIds = ArrayHelper::getColumn($selectedCoupons, 'id');
        $selectedCount = count($selectedCoupons);
        foreach ($selectedCoupons as $coupon) {
            $selectedAmount = bcadd($selectedAmount, $coupon->couponType->amount, 2);
        }
    }
    ?>

    <!--待选代金券-->
    <ul class="dR-down clearfix">
        <li class="dR-down-left"  id="coupon_title">
            <?php if (empty($selectedCoupons)) : ?>
                <img class="dR-add" src="/images/deal/add.png" alt="">选择一张代金券<i id="coupon_count"><?= $couponCount ?></i>
            <?php else : ?>
                使用代金券<?= StringUtils::amountFormat2($selectedAmount) ?>元（共<?= $selectedCount ?>张）
            <?php endif; ?>
        </li>
        <li class="dR-down-right"><img src="/images/deal/down.png" alt=""></li>
    </ul>
    <!--代金券选择-->
    <div class="dR-quan" id="valid_coupon_list">
        <input type="hidden" name="couponConfirm" id="couponConfirm" value="">
        <input type="hidden" name="selectedCouponCount" id="selectedCouponCount" value="<?= $selectedCount ?>">
        <input type="hidden" name="selectedCouponAmount" id="selectedCouponAmount" value="<?= $selectedAmount ?>">
        <ul>
            <?php foreach ($validCoupons as $coupon) { ?>
                <li class="quan-false<?php if (in_array($coupon->id, $selectedIds)) { ?> picked-box<?php } ?>" cid="<?= $coupon->id ?>">
                    <?php $couponMoney = StringUtils::amountFormat2($coupon->couponType->amount); ?>
                    <div class="quan-left" id="C<?= $coupon->id ?>" money="<?= $couponMoney ?>">
                        <span>￥</span><?= $couponMoney ?>
                    </div>
                    <div class="quan-right">
                        <div class="quan-right-content">
                            <div><?= $coupon->couponType->name ?></div>
                            <p>单笔投资满<?= StringUtils::amountFormat1('{amount}{unit}', $coupon->couponType->minInvest) ?>可用</p>
                            <p class="coupon_name" style="display: none"> 单笔投资满<?= StringUtils::amountFormat1('{amount}{unit}', $coupon->couponType->minInvest) ?>可抵扣<?= $coupon->couponType->amount ?>元</p>
                            <p>
                                <?= $coupon->couponType->loanExpires ? '期限满'.$coupon->couponType->loanExpires.'天可用(除转让)' : '新手标、转让不可用' ?>
                            </p>
                            <p>有效期至<?= $coupon->expiryDate ?></p>
                        </div>
                    </div>
                    <img class="quan-true <?php if (in_array($coupon->id, $selectedIds)) { ?>show<?php } ?>" src="/images/deal/quan-true.png" alt="">
                </li>
            <?php } ?>
        </ul>
    </div>
<?php endif; ?>