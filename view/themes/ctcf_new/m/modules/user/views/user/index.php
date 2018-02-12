<?php

use common\view\UdeskWebIMHelper;

$this->title = '账户中心';
$this->showBottomNav = true;
UdeskWebIMHelper::init($this);
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=20170906">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/activeComHeader.css?v=20170906">
<!--<link rel="stylesheet" href="--><?//= FE_BASE_URI ?><!--wap/ucenter/css/homePage.css?v=20170629">-->
<!--<link rel="stylesheet" href="--><?//= ASSETS_BASE_URI ?><!--ctcf/common/ucenter/css/homePage.css?v=20180210">-->
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/ucenter/homePage.css?v=20180212">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/user/guide.min.css">
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/common.js"></script>

<?php if (!defined('IN_APP')) { ?>
    <div class="UtopTitle f18 flex-content">
        <a class="f14" href="/user/checkin">签到</a>
        账户中心
        <a class="f14" href="/system/system/setting">设置</a>
    </div>
<?php } ?>

<div id="login">
    <div class="top_one flex-content"></div>
    <?php if (!\Yii::$app->user->isGuest) { ?>
        <div class="remain flex-content"></div>
    <?php } ?>
    <div class="youihui flex-content clearfix">
        <a href="/user/coupon/list" class="my_youhui lf youhui1">
            <img src="<?= ASSETS_BASE_URI ?>ctcf/images/ucenter/m_coupon.png" alt="">
            <div class="youhui_content f12">
                <p class="line_two">我的代金券 (元)</p>
            </div>
        </a>
        <a href="/mall/point" class="my_youhui rg youhui2">
            <img src="<?= ASSETS_BASE_URI ?>ctcf/images/ucenter/m_jifen.png" alt="">
            <div class="youhui_content f12">
                <p class="line_two">我的积分</p>
            </div>
        </a>
    </div>
</div>

<ul class="options flex-content f15">
    <li class="ops clearfix">
        <a href="/user/user/myorder" class="clearfix">
            <div style="width:0.69333333rem;height:1.226667rem;line-height:1.226667rem;text-align:left;vertical-align: middle;float:left;">
                <img src="<?= ASSETS_BASE_URI ?>ctcf/images/ucenter/licai.png" style="height: 40%;" alt="">
            </div>
            <div class="lf"style="background-position: 0 0">我的理财</div>
            <div class="rg">
                <span class="f15" id="licai" style="color: #ff6707"></span>
                <img src="<?= FE_BASE_URI ?>wap/ucenter/images/pointer.png" alt="" style="width: 0.253rem;height:0.293rem;">
            </div>
        </a>
    </li>
    <li class="ops clearfix">
        <a href="/user/user/mingxi" class="clearfix">
            <div style="width:0.69333333rem;height:1.226667rem;line-height:1.226667rem;text-align:left;vertical-align: middle;float:left;">
                <img src="<?= ASSETS_BASE_URI ?>ctcf/images/ucenter/jiaoyi.png" style="height: 40%;" alt="">
            </div>
            <div class="lf" style="background-position: 0 -0.506rem">交易明细</div>
            <div class="rg">
                &nbsp;<img src="<?= FE_BASE_URI ?>wap/ucenter/images/pointer.png" alt="" style="width: 0.253rem;height:0.293rem;">
            </div>
        </a>
    </li>
    <?php if (Yii::$app->params['feature_credit_note_on']) {  ?>
        <li class="ops clearfix" style="border: none">
            <a href="/credit/trade/assets" class="clearfix">
                <div style="width:0.69333333rem;height:1.226667rem;line-height:1.226667rem;text-align:left;vertical-align: middle;float:left;">
                    <img src="<?= ASSETS_BASE_URI ?>ctcf/images/ucenter/my_attorn.png" style="height: 40%;" alt="">
                </div>
                <div class="lf" style="background-position: 0 -1.012rem">我的转让</div>
                <div class="rg">
                    &nbsp;<img src="<?= FE_BASE_URI ?>wap/ucenter/images/pointer.png" alt="" style="width: 0.253rem;height:0.293rem;">
                </div>
            </a>
        </li>
    <?php } ?>
</ul>
<a href="/user/user/myofforder">
    <div class="out_ops flex-content">
        <div style="width:0.69333333rem;height:1.226667rem;line-height:1.226667rem;text-align:left;vertical-align: middle;float:left;">
            <img src="<?= ASSETS_BASE_URI ?>ctcf/images/ucenter/door_licai.png" style="height: 40%;" alt="">
        </div>
        <div class="lf f15"style="background-position: 0 0">门店理财</div>
        <div class="rg">
            <span class="f15" id="off_licai" style="color: #ff6707"></span>
            <img src="<?= FE_BASE_URI ?>wap/ucenter/images/pointer.png" alt="" style="width: 0.253rem;height:0.293rem;">
        </div>
    </div>
</a>
<a href="/user/invite">
    <div class="out_ops flex-content">
        <div style="width:0.69333333rem;height:1.226667rem;line-height:1.226667rem;text-align:left;vertical-align: middle;float:left;">
            <img src="<?= ASSETS_BASE_URI ?>ctcf/images/ucenter/invest_friend.png" style="height: 45%;" alt="">
        </div>
        <div class="lf f15" style="background-position: 0 -1.518rem">邀请好友</div>
        <div class="rg f15">
            &nbsp;<img src="<?= FE_BASE_URI ?>wap/ucenter/images/pointer.png" alt="" style="width: 0.253rem;height:0.293rem;">
        </div>
    </div>
</a>
<a href="/risk/risk/">
    <div class="out_ops flex-content">
        <div style="width:0.69333333rem;height:1.226667rem;line-height:1.226667rem;text-align:left;vertical-align: middle;float:left;">
            <img src="<?= ASSETS_BASE_URI ?>ctcf/images/ucenter/risk.png" style="height: 45%;" alt="">
        </div>
        <div class="lf f15 test">风险测评</div>
        <div class="rg f15">
            <span class="f11" <?php if (isset($riskContent['color'])) { ?>style="color:<?= $riskContent['color'] ?>" <?php } ?>><?= isset($riskContent['label']) ? $riskContent['label'] : '' ?></span>
            <img src="<?= FE_BASE_URI ?>wap/ucenter/images/pointer.png" alt="" style="width: 0.253rem;height:0.293rem;">
        </div>
    </div>
</a>
<ul class="options flex-content f15" style="margin-top: 8px;">
    <li class="ops clearfix">
        <a href="javascript:void(0)" id="btn_udesk_im" class="clearfix">
            <div style="width:0.69333333rem;height:1.226667rem;line-height:1.226667rem;text-align:left;vertical-align: middle;float:left;">
                <img src="<?= ASSETS_BASE_URI ?>ctcf/images/ucenter/service.png" style="height: 45%;" alt="">
            </div>
            <div class="lf f15" style="background-position: 0 -2.03rem">在线客服</div>
            <div class="rg f15">
                &nbsp;<img src="<?= FE_BASE_URI ?>wap/ucenter/images/pointer.png" alt="" style="width: 0.253rem;height:0.293rem;">
            </div>
        </a>
    </li>
    <li class="ops clearfix">
        <a href="/site/help" class="clearfix help">
            <div style="width:0.69333333rem;height:1.226667rem;line-height:1.226667rem;text-align:center;vertical-align: middle;float:left;">
                <img src="<?= ASSETS_BASE_URI ?>ctcf/images/ucenter/shapes1.png" style="height: 50%;" alt="">
            </div>
            <div class="lf f15">帮助中心</div>
            <div class="rg f15">
                &nbsp;<img src="<?= FE_BASE_URI ?>wap/ucenter/images/pointer.png" alt="" style="width: 0.253rem;height:0.293rem;">
            </div>
        </a>
    </li>
</ul>

<a href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>">
    <p class="customer_service f15">客服电话：<?= Yii::$app->params['platform_info.contact_tel'] ?></p>
    <p class="customer_service f15">（9:00-20:00）</p>
</a>

<!--footer-->
<?php if (!defined('IN_APP') && $this->showBottomNav) { ?>
    <div style="height: 50px;"></div>
    <?= $this->renderFile('@wap/views/layouts/footer.php')?>
<?php } ?>

<script type="text/javascript">
    $(function () {
        FastClick.attach(document.body);

        var xhr = $.get('/user/user/is-login');
        xhr.done(function(data) {
            if ('undefined' !== typeof data.html) {
                $('#login').html(data.html);
                adaptive();
            }

            if ('undefined' !== typeof data.sumLicai && data.sumLicai) {
                $('#licai').html(WDJF.numberFormat(data.sumLicai, true));
                $('#licai').after('元');
            }
            $('#off_licai').html(WDJF.numberFormat(data.off_licai, true));
            $('#off_licai').after('元');
        });
    });

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

    function adaptive()
    {
        if(document.body.clientWidth < 376 && String($('#zonge').html()).length > 13) {
            $('#zonge, #shouyi').removeClass('f24').addClass('f20');
        }
        if(document.body.clientWidth < 376 && String($('#shouyi').html()).length > 13) {
            $('#shouyi, #zonge').removeClass('f24').addClass('f20');
        }
        if(document.body.clientWidth < 376 && String($('#keyong').html()).length > 10) {
            $('#keyong').removeClass('f24').addClass('f20');
        }
        if(document.body.clientWidth < 376 && String($('#daijin').html()).length > 6) {
            $('#daijin, #jifen').removeClass('f24').addClass('f20');
        }
        if(document.body.clientWidth < 376 && String($('#jifen').html()).length > 6) {
            $('#daijin, #jifen').removeClass('f24').addClass('f20');
        }
    }

</script>
<?php if (!Yii::$app->user->isGuest  && !$isBindCard) { ?>
<div class="mask-no-invest">
    <div class="popup-box">
        <div class="popup-box-top"></div>
        <i class="close-box"></i>
        <div class="red-racket f13">
            请先实名认证激活托管账户，激活后，用户资金只存在于第三方的托管账户，平台无法碰触，保证安全。
        </div>
        <a href="identity/index" class="popup-box-btn f16">立即前往激活账户</a>
    </div>
</div>
<?php } ?>
<script>
    $(function () {
        $(".close-box").on("click",function(){
            $(".mask-no-invest").hide();
        })
    })
</script>