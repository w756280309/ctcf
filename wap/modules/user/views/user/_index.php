<?php

use common\utils\StringUtils;

?>
<div class="top_one flex-content">
    <?php if (\Yii::$app->user->isGuest) { ?>
        <div id="statu_one" style="padding-top: 1.173rem;">
            <p class="award f24">注册就送<span class="f27" style="font-weight: 500;">288元</span>专享红包</p>
            <div class="buttons">
                <a href="/site/signup" class="button f17 lf">注 册</a>
                <a href="/site/login?next=<?= urlencode(Yii::$app->request->hostInfo.'/user/user') ?>" class="button f17 rg">登 录</a>
            </div>
        </div>
    <?php } else { ?>
        <div id="statu_two" style="padding-top:0.373rem">
            <!--  账户中心页 start-->
            <?php if ($showPointsArea) { //积分活动生效时显示,或白名单用户登录时显示 ?>
                <a href="/user/usergrade">
                    <p class="user_imf">
                        <span class="user_tel f18"><?= StringUtils::obfsMobileNumber($user->mobile) ?></span>
                        <span class="user_level">
                            <img src="<?= FE_BASE_URI ?>wap/ucenter/images/shape_level<?= $user->level ?>.png">
                        </span>
                        <span class="user_caifu f12">财富值：<?= StringUtils::amountFormat2($user->coins) ?></span>
                    </p>
                </a>
            <?php } ?>
            <ul class="property clearfix">
                <li class="number lf">
                    <a href="/user/user/assets">
                        <p class="property_word f15">资产总额 (元)</p>
                        <p class="property_number f24" id="zonge"><?= isset($ua) ? StringUtils::amountFormat3($ua->getTotalFund()) : '' ?></p>
                    </a>
                </li>
                <li class="number lf">
                    <a href="/user/user/profit">
                        <p class="property_word f15">累计收益 (元)</p>
                        <p class="property_number f24" id="shouyi"><?= isset($user) ? StringUtils::amountFormat3($user->getProfit()) : '' ?></p>
                    </a>
                </li>
            </ul>
        </div>
    <?php } ?>
</div>

<!--登录状态下显示-->
<?php if (!\Yii::$app->user->isGuest) { ?>
    <div class="remain flex-content">
        <div class="lf" style="position: absolute;">
            <p class="remain_num f24" id="keyong"><?= StringUtils::amountFormat3($ua->available_balance) ?></p>
            <p class="remain_word f12">可用余额（元）</p>
        </div>
        <div class="rg f15" style="width: 49%; overflow: hidden;">
            <a href="javascript:void(0);" class="remain_button rg" onclick="tixian()">提现</a>
            <a href="javascript:void(0);" class="remain_button rg" style="margin-left: 0;" onclick="recharge()">充值</a>
        </div>
    </div>
<?php } ?>

<div class="youihui flex-content clearfix">
    <a href="/user/coupon/list" class="my_youhui lf youhui1">
        <img src="<?= FE_BASE_URI ?>wap/ucenter/images/coupon.png" alt="">
        <div class="youhui_content f12">
            <?php if (!\Yii::$app->user->isGuest) { ?>
                <p class="line_one f24" id="daijin"><?= isset($sumCoupon) ? StringUtils::amountFormat2($sumCoupon) : '0' ?></p>
            <?php } ?>
            <p class="line_two">我的代金券 (元)</p>
        </div>
    </a>
    <a href="/mall/point" class="my_youhui rg youhui2">
        <img src="<?= FE_BASE_URI ?>wap/ucenter/images/coins.png" alt="">
        <div class="youhui_content f12">
            <?php if (!\Yii::$app->user->isGuest) { ?>
                <p class="line_one f24" id="jifen"><?= isset($user->points) ? StringUtils::amountFormat2($user->points) : '0' ?></p>
            <?php } ?>
            <p class="line_two">我的积分</p>
        </div>
    </a>
</div>