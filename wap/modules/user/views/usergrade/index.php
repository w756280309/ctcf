<?php
use common\utils\StringUtils;

$this->title = '会员等级';
$level = $user->level;
$coins = $user->coins;

if (0 === $level) {
    $coinscha = 20 - $coins;
    $yuju = '还需' . $coinscha . '财富值成为VIP1';
} elseif (1 === $level) {
    $coinscha = 50 - $coins;
    $yuju = '还需' . $coinscha . '财富值成为VIP2';
} elseif (2 === $level) {
    $coinscha = 100 - $coins;
    $yuju = '还需' . $coinscha . '财富值成为VIP3';
} elseif (3 === $level) {
    $coinscha = 200 - $coins;
    $yuju = '还需' . $coinscha . '财富值成为VIP4';
} elseif (4 === $level) {
    $coinscha = 500 - $coins;
    $yuju = '还需' . $coinscha . '财富值成为VIP5';
} elseif (5 === $level) {
    $coinscha = 800 - $coins;
    $yuju = '还需' . $coinscha . '财富值成为VIP6';
} elseif (6 === $level) {
    $coinscha = 1500 - $coins;
    $yuju = '还需' . $coinscha . '财富值成为VIP7';
} else {
    $yuju = '您已成为VIP7';
}

?>

<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/base.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/memberlevel/css/index.css?v=1.11">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>

<div class="member-level">
    <div class="memberlevel-treasure padd-lf-rg">
        <h4 class="treasure-title">当前财富值：<?= StringUtils::amountFormat2($user->coins) ?></h4>
        <div class="memberlevel-vip clearfix">
            <ul class="vip-ul">
                <li class="<?= ($user->level) === 0 ? 'lf vip vip-main' : 'lf vip' ?> "><img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= ($user->level) === 0 ? 'red' : 'white' ?>-vip-0.png" class="v0" alt="v0"></li>
                <li class="lf line"></li>
                <li class="<?= ($user->level) === 1 ? 'lf vip vip-main' : 'lf vip' ?> "><img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= ($user->level) === 1 ? 'red' : 'white' ?>-vip-1.png" class="v1" alt="v1"></li>
                <li class="lf line"></li>
                <li class="<?= ($user->level) === 2 ? 'lf vip vip-main' : 'lf vip' ?> "><img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= ($user->level) === 2 ? 'red' : 'white' ?>-vip-2.png" class="v2" alt="v2"></li>
                <li class="lf line"></li>
                <li class="<?= ($user->level) === 3 ? 'lf vip vip-main' : 'lf vip' ?> "><img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= ($user->level) === 3 ? 'red' : 'white' ?>-vip-3.png" class="v3" alt="v3"></li>
                <li class="lf vertical-line"></li>
                <li class="<?= ($user->level) === 4 ? 'rg vip marg-rg' : 'rg vip' ?> "><img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= ($user->level) === 4 ? 'red' : 'white' ?>-vip-4.png" class="v4" alt="v4"></li>
                <li class="rg line"></li>
                <li class="<?= ($user->level) === 5 ? 'rg vip marg-rg' : 'rg vip' ?> "><img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= ($user->level) === 5 ? 'red' : 'white' ?>-vip-5.png" class="v5" alt="v5"></li>
                <li class="rg line"></li>
                <li class="<?= ($user->level) === 6 ? 'rg vip marg-rg' : 'rg vip' ?> "><img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= ($user->level) === 6 ? 'red' : 'white' ?>-vip-6.png" class="v6" alt="v6"></li>
                <li class="rg line"></li>
                <li class="<?= ($user->level) === 7 ? 'rg vip marg-rg' : 'rg vip' ?> "><img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= ($user->level) === 7 ? 'red' : 'white' ?>-vip-7.png" class="v7" alt="v7"></li>
            </ul>
        </div>
    </div>
    <div class="membellevel-prerogative ">
        <h4 class="treasure-title">
            <?= $yuju ?>
            <a href="/user/usergrade/obtaincoins" class="a-blue-membel">如何获得?</a>
        </h4>
    </div>
    <div class="memberlevel-obtain">
        <h3 class="treasure-title">
            会员特权
            <a href="/user/usergrade/detail" class="a-blue-membel">特权说明</a>
        </h3>
    </div>
    <!-- V0 -->
    <div class="memberlevel-prerogative clearfix"  style="display: block">
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/colours-birthday-cake.png" alt="生日特权">
            <h4 class="prerogative-title">生日特权</h4>
            <p class="details">生日祝福及代金券</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/colours-integration.png" alt="积分权利">
            <h4 class="prerogative-title">积分权利</h4>
            <p class="details">购买积分</p>
        </div>
    </div>

    <!-- V1 -->
    <div class="memberlevel-prerogative clearfix" style="display: none">
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-birthday-cake.png" alt="生日特权">
            <h4 class="prerogative-title">生日特权</h4>
            <p class="details">生日祝福及代金券</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-integration.png" alt="积分权利">
            <h4 class="prerogative-title">积分权利</h4>
            <p class="details">购买积分*1.02倍</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-customer-service.png" alt="专属顾问">
            <h4 class="prerogative-title">专属顾问</h4>
            <p class="details">微信客服专享</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-activity.png" alt="活动权利">
            <h4 class="prerogative-title">活动权利</h4>
            <p class="details">享受微信群活动报名</p>
        </div>
    </div>

    <!-- V2 -->
    <div class="memberlevel-prerogative clearfix" style="display: none">
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-birthday-cake.png" alt="生日特权">
            <h4 class="prerogative-title">生日特权</h4>
            <p class="details">生日祝福及代金券</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-integration.png" alt="积分权利">
            <h4 class="prerogative-title">积分权利</h4>
            <p class="details">购买积分*1.04倍</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-customer-service.png" alt="专属顾问">
            <h4 class="prerogative-title">专属顾问</h4>
            <p class="details">微信客服专享</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-activity.png" alt="活动权利">
            <h4 class="prerogative-title">活动权利</h4>
            <p class="details">享受微信群活动报名</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-product-order.png" alt="产品预约">
            <h4 class="prerogative-title">产品预约</h4>
            <p class="details">产品预约</p>
        </div>
        <div class="prerogative rg-prerogative rg">
        </div>
    </div>

    <!-- V3 -->
    <div class="memberlevel-prerogative clearfix" style="display: none">
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-birthday-cake.png" alt="生日特权">
            <h4 class="prerogative-title">生日特权</h4>
            <p class="details">生日祝福及代金券</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-integration.png" alt="积分权利">
            <h4 class="prerogative-title">积分权利</h4>
            <p class="details">购买积分*1.06倍</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-customer-service.png" alt="专属顾问">
            <h4 class="prerogative-title">专属顾问</h4>
            <p class="details">微信客服专享</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-activity.png" alt="活动权利">
            <h4 class="prerogative-title">活动权利</h4>
            <p class="details">预约报名活动</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-product-order.png" alt="产品预约">
            <h4 class="prerogative-title">产品预约</h4>
            <p class="details">产品预约、单独挂标</p>
        </div>
        <div class="prerogative rg-prerogative rg">
        </div>
    </div>

    <!-- V4 -->
    <div class="memberlevel-prerogative clearfix" style="display: none">
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-birthday-cake.png" alt="生日特权">
            <h4 class="prerogative-title">生日特权</h4>
            <p class="details">生日祝福及代金券</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-integration.png" alt="积分权利">
            <h4 class="prerogative-title">积分权利</h4>
            <p class="details">购买积分*1.08倍</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-customer-service.png" alt="专属顾问">
            <h4 class="prerogative-title">专属顾问</h4>
            <p class="details">微信客服专享、物品寄送</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-activity.png" alt="活动权利">
            <h4 class="prerogative-title">活动权利</h4>
            <p class="details">预约报名活动</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-product-order.png" alt="产品预约">
            <h4 class="prerogative-title">产品预约</h4>
            <p class="details">产品预约、单独挂标、次日起息(单笔认购100万以上)</p>
        </div>
        <div class="prerogative rg-prerogative rg">
        </div>
    </div>

    <!-- V5 -->
    <div class="memberlevel-prerogative clearfix" style="display: none">
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-birthday-cake.png" alt="生日特权">
            <h4 class="prerogative-title">生日特权</h4>
            <p class="details">生日祝福、代金券及生日蛋糕券</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-integration.png" alt="积分权利">
            <h4 class="prerogative-title">积分权利</h4>
            <p class="details">购买积分*1.1倍</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-customer-service.png" alt="专属顾问">
            <h4 class="prerogative-title">专属顾问</h4>
            <p class="details">微信客服专享、物品寄送</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-activity.png" alt="活动权利">
            <h4 class="prerogative-title">活动权利</h4>
            <p class="details">预约报名活动、预留活动名额</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-product-order.png" alt="产品预约">
            <h4 class="prerogative-title">产品预约</h4>
            <p class="details">产品预约、单独挂标、次日起息(单笔认购100万以上)</p>
        </div>
        <div class="prerogative rg-prerogative rg">
        </div>
    </div>

    <!-- V6 -->
    <div class="memberlevel-prerogative clearfix" style="display: none">
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-birthday-cake.png" alt="生日特权">
            <h4 class="prerogative-title">生日特权</h4>
            <p class="details">生日祝福、代金券及生日蛋糕券</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-integration.png" alt="积分权利">
            <h4 class="prerogative-title">积分权利</h4>
            <p class="details">购买积分*1.12倍</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-customer-service.png" alt="专属顾问">
            <h4 class="prerogative-title">专属顾问</h4>
            <p class="details">微信客服专享、物品寄送及享受双人上门服务</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-activity.png" alt="活动权利">
            <h4 class="prerogative-title">活动权利</h4>
            <p class="details">预约报名活动、预留活动名额</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-product-order.png" alt="产品预约">
            <h4 class="prerogative-title">产品预约</h4>
            <p class="details">产品预约、单独挂标、次日起息(单笔认购100万以上)</p>
        </div>
        <div class="prerogative rg-prerogative rg">
        </div>
    </div>

    <!-- V7 -->
    <div class="memberlevel-prerogative clearfix" style="display:none">
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-birthday-cake.png" alt="生日特权">
            <h4 class="prerogative-title">生日特权</h4>
            <p class="details">生日祝福、代金券及生日蛋糕券</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-integration.png" alt="积分权利">
            <h4 class="prerogative-title">积分权利</h4>
            <p class="details">购买积分*1.15倍</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-customer-service.png" alt="专属顾问">
            <h4 class="prerogative-title">专属顾问</h4>
            <p class="details">微信客服专享、物品寄送及享受双人上门服务</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-activity.png" alt="活动权利">
            <h4 class="prerogative-title">活动权利</h4>
            <p class="details">预约报名活动、预留活动名额、专属定制活动</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/grey-product-order.png" alt="产品预约">
            <h4 class="prerogative-title">产品预约</h4>
            <p class="details">产品预约、单独挂标、次日起息(单笔认购100万以上)</p>
        </div>
        <div class="prerogative rg-prerogative rg">
        </div>
    </div>
</div>


<script>
$(function(){
    FastClick.attach(document.body);
    $('.vip-ul .vip').on('click',function(){
        var index = $('.vip-ul .vip').index(this);

        for(var i=0;i<= $('.vip-ul .vip').length; i++ ){
            if( i!=index ) {
                $('.memberlevel-prerogative').eq(i).hide();
                $('.vip-ul .vip').eq(i).removeClass('vip-other');
            }
        }

               $('.memberlevel-prerogative').eq(index).show();
               $('.vip-ul .vip').eq(index).addClass('vip-other');
           });

});
</script>

