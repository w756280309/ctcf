<?php
use yii\web\JqueryAsset;
use common\utils\StringUtils;

$this->title = '账户中心';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/activeComHeader.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/ucenter/css/homePage.css">
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>

<?php if (!defined('IN_APP')) { ?>
    <div class="UtopTitle f18 flex-content">
        账户中心
        <a class="f14" href="/system/system/setting">设置</a>
    </div>
<?php } ?>

<?php if (!\Yii::$app->user->isGuest) { ?>
    <!--登录状态下的顶部-->
    <div class="top_one flex-content">
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
    </div>
    <!--登录状态下显示-->
    <div class="remain flex-content">
        <div class="lf">
            <p class="remain_num f24" id="keyong"><?= StringUtils::amountFormat3($ua->available_balance) ?></p>
            <p class="remain_word f12">可用余额（元）</p>
        </div>
        <div class="rg f15">
            <a href="javascript:void(0);" class="remain_button rg" onclick="tixian()">提现</a>
            <a href="javascript:void(0);" class="remain_button rg" onclick="recharge()">充值</a>
        </div>
    </div>
    <div class="youihui flex-content clearfix">
        <a href="/user/coupon/list" class="my_youhui lf youhui1">
            <img src="<?= FE_BASE_URI ?>wap/ucenter/images/coupon.png" alt="">
            <div class="youhui_content f12">
                <p class="line_one f24" id="daijin"><?= isset($sumCoupon) ? StringUtils::amountFormat2($sumCoupon) : '0' ?></p>
                <p class="line_two">我的代金券 (元)</p>
            </div>
        </a>
        <a href="/mall/point" class="my_youhui rg youhui2">
            <img src="<?= FE_BASE_URI ?>wap/ucenter/images/coins.png" alt="">
            <div class="youhui_content f12">
                <p class="line_one f24" id="jifen"><?= isset($user->points) ? StringUtils::amountFormat2($user->points) : '0' ?></p>
                <p class="line_two">我的积分</p>
            </div>
        </a>
    </div>
<?php } else { ?>
    <!--未登录状态下的顶部-->
    <div class="top_one flex-content">
        <div id="statu_one" style="padding-top: 1.173rem;">
            <p class="award f24">注册就送<span class="f27" style="font-weight: 500;">288元</span>专享红包</p>
            <div class="buttons">
                <a href="/site/signup" class="button f17 lf">注 册</a>
                <a href="/site/login?next=<?= urlencode(Yii::$app->request->hostInfo . '/user/user') ?>" class="button f17 rg">登 录</a>
            </div>
        </div>
    </div>
    <div class="youihui flex-content clearfix">
        <a href="/user/coupon/list" class="my_youhui lf youhui1">
            <img src="<?= FE_BASE_URI ?>wap/ucenter/images/coupon.png" alt="">
            <div class="youhui_content f12">
                <p class="line_one">我的代金券</p>
            </div>
        </a>
        <a href="/mall/point" class="my_youhui rg youhui2">
            <img src="<?= FE_BASE_URI ?>wap/ucenter/images/coins.png" alt="">
            <div class="youhui_content f12">
                <p class="line_one">我的积分</p>
            </div>
        </a>
    </div>
<?php } ?>

<ul class="options flex-content f15">
    <li class="ops clearfix">
        <a href="/user/user/myorder" class="clearfix">
            <div class="lf"style="background-position: 0 0">我的理财</div>
            <div class="rg">
                <span class="f15" style="color: #ff0000"><?= isset($ua) ? StringUtils::amountFormat2(bcadd($ua->freeze_balance, $ua->investment_balance, 2))  : '' ?></span><?= isset($ua) ? '元'  : '' ?>
                <img src="<?= FE_BASE_URI ?>wap/ucenter/images/pointer.png" alt="" style="width: 0.253rem;height:0.293rem;">
            </div>
        </a>
    </li>
    <li class="ops clearfix">
        <a href="/user/user/mingxi" class="clearfix">
            <div class="lf" style="background-position: 0 -0.506rem">交易明细</div>
            <div class="rg">
                &nbsp;<img src="<?= FE_BASE_URI ?>wap/ucenter/images/pointer.png" alt="" style="width: 0.253rem;height:0.293rem;">
            </div>
        </a>
    </li>
    <?php if (Yii::$app->params['feature_credit_note_on']) {  ?>
        <li class="ops clearfix" style="border: none">
            <a href="/credit/trade/assets" class="clearfix">
                <div class="lf" style="background-position: 0 -1.012rem">我的转让</div>
                <div class="rg">
                    &nbsp;<img src="<?= FE_BASE_URI ?>wap/ucenter/images/pointer.png" alt="" style="width: 0.253rem;height:0.293rem;">
                </div>
            </a>
        </li>
    <?php } ?>

</ul>
<a href="/user/invite">
<div class="out_ops flex-content">
    <div class="lf f15" style="background-position: 0 -1.518rem">邀请好友</div>
    <div class="rg f15">
        &nbsp;<img src="<?= FE_BASE_URI ?>wap/ucenter/images/pointer.png" alt="" style="width: 0.253rem;height:0.293rem;">
    </div>
</div>
</a>
<a href="/site/help">
    <div class="out_ops flex-content">
        <div class="lf f15" style="background-position: 0 -2.03rem">帮助中心</div>
        <div class="rg f15">
            &nbsp;<img src="<?= FE_BASE_URI ?>wap/ucenter/images/pointer.png" alt="" style="width: 0.253rem;height:0.293rem;">
        </div>
    </div>
</a>
<a href="tel://<?= Yii::$app->params['contact_tel'] ?>">
    <p class="customer_service f15">客服电话：<?= Yii::$app->params['contact_tel'] ?></p>
    <p class="customer_service f15">（8:30-20:00）</p>
</a>

<!--footer-->
<?php if (!defined('IN_APP')) { ?>
    <div style="height: 50px;"></div>
    <div class="navbar-fixed-bottom footer flex-content">
        <div class="footer-title">
            <div class="footer-inner">
                <a href="/?v=1#t=1" class="nav-bar"><span class="shouye"></span>首页</a>
            </div>
        </div>
        <div class="footer-title">
            <div class="footer-inner">
                <a href="/deal/deal/index" class="nav-bar"><span class="licai"></span>理财</a>
            </div>
        </div>
        <div class="footer-title">
            <div class="footer-inner">
                <a class="nav-bar special-bar" href="/user/user" style="color: #f44336"><span class="zhanghu"></span>账户</a>
            </div>
        </div>
    </div>
<?php } ?>

<script type="text/javascript">
    $(function () {
        FastClick.attach(document.body);
        if(document.body.clientWidth<376 && $('#zonge').html().length>13){
            $('#zonge,#shouyi').removeClass('f24').addClass('f20');
        }
        if(document.body.clientWidth<376 && $('#shouyi').html().length>13){
            $('#shouyi,#zonge').removeClass('f24').addClass('f20');
        }
        if(document.body.clientWidth<376 && $('#keyong').html().length>10){
            $('#keyong').removeClass('f24').addClass('f20');
        }
        if(document.body.clientWidth<376 && $('#daijin').html().length>6){
            $('#daijin,#jifen').removeClass('f24').addClass('f20');
        }
        if(document.body.clientWidth<376 && $('#jifen').html().length>6){
            console.log(123)
            $('#daijin,#jifen').removeClass('f24').addClass('f20');
        }
    })

    function tixian()
    {
        var xhr = $.get('/user/user/check-kuaijie', function (data) {
            if (data.code) {
                toastCenter(data.message, function() {
                    if (data.tourl) {
                        location.href = data.tourl;
                    }
                });
            } else {
                location.href='/user/userbank/tixian';
            }
        });

        xhr.fail(function () {
            toastCenter('系统繁忙,请稍后重试!');
        });
    }

    function recharge()
    {
        var xhr = $.get('/user/user/check-kuaijie', function (data) {
            if (data.code) {
                toastCenter(data.message, function() {
                    if (data.tourl) {
                        location.href = data.tourl;
                    }
                });
            } else {
                location.href='/user/userbank/recharge';
            }
        });

        xhr.fail(function () {
            toastCenter('系统繁忙,请稍后重试!');
        });
    }
</script>

