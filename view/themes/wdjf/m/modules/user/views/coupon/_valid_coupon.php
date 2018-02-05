<?php

use common\utils\StringUtils;
use yii\helpers\ArrayHelper;

?>

<div class="lf" style="width: 31%;color:#a3a4a6;font-size: 13px">优惠券</div>
<?php //if (empty($coupons)) { ?>
    <div class="col-xs-8 safe-txt" onclick="toCoupon()" style="padding: 0;"><span class="notice">选择代金券或加息券</span></div>
<?php //} else { ?>
<!--    --><?php
//        $count = (int) count($coupons);
//        $amountArr = ArrayHelper::getColumn($coupons, 'amount');
//        $totalAmount = array_sum($amountArr);
//    ?>
<!--    <div class="col-xs-5 safe-txt" id="updateCouponBox" onclick="toCoupon()" style="padding: 0 0 0 15px;">--><?//= StringUtils::amountFormat2($totalAmount) ?><!--元（共--><?//= $count ?><!--张）</div>-->
<!--    <div class="col-xs-3 safe-txt text-align-ct" onclick="resetCoupon()">清除</div>-->
<!--    <input id="selectedCouponCount" type="hidden" value="--><?//= $count ?><!--">-->
<!--    <input id="selectedCouponAmount" type="hidden" value="--><?//= $totalAmount ?><!--">-->
<?php //} ?>

