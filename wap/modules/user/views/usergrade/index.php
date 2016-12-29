<?php
use common\utils\StringUtils;

$this->title = '会员等级';
$level = $user->level;
$coins = $user->coins;

$shuzu = array();
for ($x=0; $x<=7; $x++) {
    if ($x === $level) {
        if ($x < 4) {
            $shuzu[$x]['classone']  = 'lf vip vip-main';
        } else {
            $shuzu[$x]['classone']  = 'rg vip vip-main';
        }
        $shuzu[$x]['picone'] = 'red';
        $shuzu[$x]['classtwo'] = 'display: block';
        $shuzu[$x]['pictwo'] = 'colours';
    } else {
        if ($x < 4) {
            $shuzu[$x]['classone'] = 'lf vip';
        } else {
            $shuzu[$x]['classone'] = 'rg vip';
        }
        $shuzu[$x]['picone'] = 'white';
        $shuzu[$x]['classtwo'] = 'display: none';
        $shuzu[$x]['pictwo'] = 'grey';
    }
}

$viparray = array(
    '0' =>  '20',
    '1' =>  '50',
    '2' =>  '100',
    '3' =>  '200',
    '4' =>  '500',
    '5' =>  '800',
    '6' =>  '1500',
);

if (7 === $level) {
    $yuju = '您已成为VIP7';
} else {
    $yuju = '还需' . ($viparray[$level] - $coins) . '财富值成为VIP' . ($level + 1);
}

?>

<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/base.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/memberlevel/css/index.css?v=1.11">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>

<div class="member-level">
    <div class="memberlevel-treasure padd-lf-rg">
        <h4 class="treasure-title">当前财富值：<?= StringUtils::amountFormat2($coins) ?></h4>
        <div class="memberlevel-vip clearfix">
            <ul class="vip-ul">
                <li class="<?= $shuzu[0]['classone'] ?> "><img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[0]['picone'] ?>-vip-0.png" class="v0" alt="v0"></li>
                <li class="lf line"></li>
                <li class="<?= $shuzu[1]['classone'] ?> "><img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[1]['picone'] ?>-vip-1.png" class="v1" alt="v1"></li>
                <li class="lf line"></li>
                <li class="<?= $shuzu[2]['classone'] ?> "><img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[2]['picone'] ?>-vip-2.png" class="v2" alt="v2"></li>
                <li class="lf line"></li>
                <li class="<?= $shuzu[3]['classone'] ?> "><img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[3]['picone'] ?>-vip-3.png" class="v3" alt="v3"></li>
                <li class="lf vertical-line"></li>
                <li class="marg-rg"></li>
                <li class="<?= $shuzu[4]['classone'] ?> "><img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[4]['picone'] ?>-vip-4.png" class="v4" alt="v4"></li>
                <li class="rg line"></li>
                <li class="<?= $shuzu[5]['classone'] ?> "><img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[5]['picone'] ?>-vip-5.png" class="v5" alt="v5"></li>
                <li class="rg line"></li>
                <li class="<?= $shuzu[6]['classone'] ?> "><img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[6]['picone'] ?>-vip-6.png" class="v6" alt="v6"></li>
                <li class="rg line"></li>
                <li class="<?= $shuzu[7]['classone'] ?> "><img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[7]['picone'] ?>-vip-7.png" class="v7" alt="v7"></li>
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
    <div class="memberlevel-prerogative clearfix"  style="<?= $shuzu[0]['classtwo'] ?>">
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[0]['pictwo'] ?>-birthday-cake.png" alt="生日特权">
            <h4 class="prerogative-title">生日特权</h4>
            <p class="details">生日祝福及代金券</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[0]['pictwo'] ?>-integration.png" alt="积分权利">
            <h4 class="prerogative-title">积分权利</h4>
            <p class="details">购买积分</p>
        </div>
    </div>

    <!-- V1 -->
    <div class="memberlevel-prerogative clearfix" style="<?= $shuzu[1]['classtwo'] ?>">
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[1]['pictwo'] ?>-birthday-cake.png" alt="生日特权">
            <h4 class="prerogative-title">生日特权</h4>
            <p class="details">生日祝福及代金券</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[1]['pictwo'] ?>-integration.png" alt="积分权利">
            <h4 class="prerogative-title">积分权利</h4>
            <p class="details">购买积分*1.02倍</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[1]['pictwo'] ?>-customer-service.png" alt="专属顾问">
            <h4 class="prerogative-title">专属顾问</h4>
            <p class="details">微信客服专享</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[1]['pictwo'] ?>-activity.png" alt="活动权利">
            <h4 class="prerogative-title">活动权利</h4>
            <p class="details">享受微信群活动报名</p>
        </div>
    </div>

    <!-- V2 -->
    <div class="memberlevel-prerogative clearfix" style="<?= $shuzu[2]['classtwo'] ?>">
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[2]['pictwo'] ?>-birthday-cake.png" alt="生日特权">
            <h4 class="prerogative-title">生日特权</h4>
            <p class="details">生日祝福及代金券</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[2]['pictwo'] ?>-integration.png" alt="积分权利">
            <h4 class="prerogative-title">积分权利</h4>
            <p class="details">购买积分*1.04倍</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[2]['pictwo'] ?>-customer-service.png" alt="专属顾问">
            <h4 class="prerogative-title">专属顾问</h4>
            <p class="details">微信客服专享</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[2]['pictwo'] ?>-activity.png" alt="活动权利">
            <h4 class="prerogative-title">活动权利</h4>
            <p class="details">享受微信群活动报名</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[2]['pictwo'] ?>-product-order.png" alt="产品预约">
            <h4 class="prerogative-title">产品预约</h4>
            <p class="details">产品预约</p>
        </div>
        <div class="prerogative rg-prerogative rg">
        </div>
    </div>

    <!-- V3 -->
    <div class="memberlevel-prerogative clearfix" style="<?= $shuzu[3]['classtwo'] ?>">
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[3]['pictwo'] ?>-birthday-cake.png" alt="生日特权">
            <h4 class="prerogative-title">生日特权</h4>
            <p class="details">生日祝福及代金券</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[3]['pictwo'] ?>-integration.png" alt="积分权利">
            <h4 class="prerogative-title">积分权利</h4>
            <p class="details">购买积分*1.06倍</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[3]['pictwo'] ?>-customer-service.png" alt="专属顾问">
            <h4 class="prerogative-title">专属顾问</h4>
            <p class="details">微信客服专享</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[3]['pictwo'] ?>-activity.png" alt="活动权利">
            <h4 class="prerogative-title">活动权利</h4>
            <p class="details">预约报名活动</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[3]['pictwo'] ?>-product-order.png" alt="产品预约">
            <h4 class="prerogative-title">产品预约</h4>
            <p class="details">产品预约、单独挂标</p>
        </div>
        <div class="prerogative rg-prerogative rg">
        </div>
    </div>

    <!-- V4 -->
    <div class="memberlevel-prerogative clearfix" style="<?= $shuzu[4]['classtwo'] ?>">
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[4]['pictwo'] ?>-birthday-cake.png" alt="生日特权">
            <h4 class="prerogative-title">生日特权</h4>
            <p class="details">生日祝福及代金券</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[4]['pictwo'] ?>-integration.png" alt="积分权利">
            <h4 class="prerogative-title">积分权利</h4>
            <p class="details">购买积分*1.08倍</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[4]['pictwo'] ?>-customer-service.png" alt="专属顾问">
            <h4 class="prerogative-title">专属顾问</h4>
            <p class="details">微信客服专享、物品寄送</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[4]['pictwo'] ?>-activity.png" alt="活动权利">
            <h4 class="prerogative-title">活动权利</h4>
            <p class="details">预约报名活动</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[4]['pictwo'] ?>-product-order.png" alt="产品预约">
            <h4 class="prerogative-title">产品预约</h4>
            <p class="details">产品预约、单独挂标、次日起息(单笔认购100万以上)</p>
        </div>
        <div class="prerogative rg-prerogative rg">
        </div>
    </div>

    <!-- V5 -->
    <div class="memberlevel-prerogative clearfix" style="<?= $shuzu[5]['classtwo'] ?>">
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[5]['pictwo'] ?>-birthday-cake.png" alt="生日特权">
            <h4 class="prerogative-title">生日特权</h4>
            <p class="details">生日祝福、代金券及生日蛋糕券</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[5]['pictwo'] ?>-integration.png" alt="积分权利">
            <h4 class="prerogative-title">积分权利</h4>
            <p class="details">购买积分*1.1倍</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[5]['pictwo'] ?>-customer-service.png" alt="专属顾问">
            <h4 class="prerogative-title">专属顾问</h4>
            <p class="details">微信客服专享、物品寄送</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[5]['pictwo'] ?>-activity.png" alt="活动权利">
            <h4 class="prerogative-title">活动权利</h4>
            <p class="details">预约报名活动、预留活动名额</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[5]['pictwo'] ?>-product-order.png" alt="产品预约">
            <h4 class="prerogative-title">产品预约</h4>
            <p class="details">产品预约、单独挂标、次日起息(单笔认购100万以上)</p>
        </div>
        <div class="prerogative rg-prerogative rg">
        </div>
    </div>

    <!-- V6 -->
    <div class="memberlevel-prerogative clearfix" style="<?= $shuzu[6]['classtwo'] ?>">
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[6]['pictwo'] ?>-birthday-cake.png" alt="生日特权">
            <h4 class="prerogative-title">生日特权</h4>
            <p class="details">生日祝福、代金券及生日蛋糕券</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[6]['pictwo'] ?>-integration.png" alt="积分权利">
            <h4 class="prerogative-title">积分权利</h4>
            <p class="details">购买积分*1.12倍</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[6]['pictwo'] ?>-customer-service.png" alt="专属顾问">
            <h4 class="prerogative-title">专属顾问</h4>
            <p class="details">微信客服专享、物品寄送及享受双人上门服务</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[6]['pictwo'] ?>-activity.png" alt="活动权利">
            <h4 class="prerogative-title">活动权利</h4>
            <p class="details">预约报名活动、预留活动名额</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[6]['pictwo'] ?>-product-order.png" alt="产品预约">
            <h4 class="prerogative-title">产品预约</h4>
            <p class="details">产品预约、单独挂标、次日起息(单笔认购100万以上)</p>
        </div>
        <div class="prerogative rg-prerogative rg">
        </div>
    </div>

    <!-- V7 -->
    <div class="memberlevel-prerogative clearfix" style="<?= $shuzu[7]['classtwo'] ?>">
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[7]['pictwo'] ?>-birthday-cake.png" alt="生日特权">
            <h4 class="prerogative-title">生日特权</h4>
            <p class="details">生日祝福、代金券及生日蛋糕券</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[7]['pictwo'] ?>-integration.png" alt="积分权利">
            <h4 class="prerogative-title">积分权利</h4>
            <p class="details">购买积分*1.15倍</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[7]['pictwo'] ?>-customer-service.png" alt="专属顾问">
            <h4 class="prerogative-title">专属顾问</h4>
            <p class="details">微信客服专享、物品寄送及享受双人上门服务</p>
        </div>
        <div class="prerogative rg-prerogative rg">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[7]['pictwo'] ?>-activity.png" alt="活动权利">
            <h4 class="prerogative-title">活动权利</h4>
            <p class="details">预约报名活动、预留活动名额、专属定制活动</p>
        </div>
        <div class="prerogative lf-prerogative lf">
            <img src="<?= FE_BASE_URI ?>wap/memberlevel/img/<?= $shuzu[7]['pictwo'] ?>-product-order.png" alt="产品预约">
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



