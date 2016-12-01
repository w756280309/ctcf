<?php
$this->title = '账户中心';
$this->showBottomNav = true;
$this->showAvatar = true;

use yii\web\JqueryAsset;
use common\utils\StringUtils;
$this->registerJsFile(ASSETS_BASE_URI.'js/fastclick.js', ['depends' => JqueryAsset::class]);

?>
<link href="<?= ASSETS_BASE_URI ?>css/informationAndHelp.css" rel="stylesheet">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css?v=20160803">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/tip.css?v=20161116">
<script>
    $(function(){
        $('.footer-inner a').css({color: '#8c8c8c'});
        $('.footer-inner2 a').css({color: '#f44336'});
        $('.footer-inner1 a').css({color: '#8c8c8c'});
        $('.footer-inner1 .licai').css({background: 'url("<?= ASSETS_BASE_URI ?>images/footer2.png") no-repeat -113px -3px',backgroundSize: '200px'});
        $('.footer-inner2 .zhanghu').css({background: 'url("<?= ASSETS_BASE_URI ?>images/footer2.png") no-repeat -81px -57px',backgroundSize: '200px'});
        $('.footer-inner .shouye').css({background: 'url("<?= ASSETS_BASE_URI ?>images/footer2.png") no-repeat -145px -3px',backgroundSize: '200px'});
    })
</script>

<!--  账户中心页 start-->
<div class="row  border-bottom  earning accountcenter">
    <div class="row earn-tit">
        <div class="col-xs-1"></div>
        <div class="col-xs-5 col">资产总额 （元）</div>
        <div class="col-xs-6"></div>
    </div>
    <div class="row earn-num1">
        <div class="col-xs-1"></div>
        <div class="col-xs-7 col"><?= StringUtils::amountFormat3($ua->getTotalFund()) ?></div>
        <div class="col-xs-3"></div>
    </div>
    <div class="row accountcenter-center-left">
        <div class="col-xs-1"></div>
        <div class="col-xs-5">理财资产（元）</div>
        <div class="col-xs-6 dj-tip-refer" style="position: relative;">
            冻结资金（元）
            <img class="common_img sm-height_tips_img" src="<?= ASSETS_BASE_URI ?>images/credit/icon_instruction.png" style="width: 16px" alt="">
            <p class="sm-height_tips">
                <img src="<?= ASSETS_BASE_URI ?>images/credit/triangle.png" alt="">
                <span>投资资金在项目未满标时锁定的金额</span>
            </p>
        </div>

    </div>
    <div class="row accountcenter-center-right">
        <div class="col-xs-1"></div>
        <div class="col-xs-5"><?= StringUtils::amountFormat3($ua->investment_balance) ?></div>
        <div class="col-xs-6"><?= StringUtils::amountFormat3($ua->freeze_balance) ?></div>
    </div>
    <br>
    <div class="row accountcenter-center-left">
        <div class="col-xs-1"></div>
        <div class="col-xs-5">累计投资（元）</div>
        <div class="col-xs-6">累计收益（元）</div>
    </div>
    <div class="row accountcenter-center-right">
        <div class="col-xs-1"></div>
        <div class="col-xs-5"><?= StringUtils::amountFormat3($user->getTotalInvestment()) ?></div>
        <div class="col-xs-6"><?= StringUtils::amountFormat3($user->getProfit()) ?></div>
    </div>
</div>
<div class="row accountcenter-other">
    <div class="row border-bottom accountcenter-bottom">
        <div class="col-xs-1"></div>
        <div class="col-xs-5">
                <p>可用余额（元）</p>
                <p class="unmber_remain"><?= StringUtils::amountFormat3($ua->available_balance) ?></p>
        </div>
        <div class="col-xs-3 addcash" onclick="recharge()">充值</div>
        <div class="col-xs-3 rg-line drawcash" onclick="tixian()">提现</div>
    </div>
    <div class="clear"></div>
    <a class="row sm-height border-bottom margin-top block" href="/user/user/myorder" >
        <div class="col-xs-10 left-txt">我的理财</div>
        <div class="col-xs-1 arrow">
            <img src="<?= ASSETS_BASE_URI ?>images/arrow.png" alt="右箭头">
        </div>
        <div class="col-xs-1"></div>
    </a>

    <?php if (Yii::$app->params['feature_credit_note_on']) {  ?>
        <div class="clear"></div>
        <a class="row sm-height border-bottom block" href="/credit/trade/assets" >
            <div class="col-xs-10 left-txt">我的转让</div>
            <div class="col-xs-1 arrow">
                <img src="<?= ASSETS_BASE_URI ?>images/arrow.png" alt="右箭头">
            </div>
            <div class="col-xs-1"></div>
        </a>
    <?php } ?>

    <div class="clear"></div>
    <a class="row sm-height border-bottom block" href="/user/coupon/list" >
        <div class="col-xs-10 left-txt">我的代金券</div>
        <div class="col-xs-1 arrow">
            <img src="<?= ASSETS_BASE_URI ?>images/arrow.png" alt="右箭头">
        </div>
        <div class="col-xs-1"></div>
    </a>

    <!--<div class="clear"></div>
    <a class="row sm-height border-bottom block" href="/user/invite" >
        <div class="col-xs-10 left-txt">邀请好友</div>
        <div class="col-xs-1 arrow">
            <img src="<?= ASSETS_BASE_URI ?>images/arrow.png" alt="右箭头">
        </div>
        <div class="col-xs-1"></div>
    </a>-->

    <div class="clear"></div>
    <a class="row sm-height border-bottom block <?= (Yii::$app->params['mall_enabled']) ? '' : 'end-list'?>" href="/user/user/mingxi" >
        <div class="col-xs-10 left-txt">交易明细</div>
        <div class="col-xs-1 arrow">
            <img src="<?= ASSETS_BASE_URI ?>images/arrow.png" alt="右箭头">
        </div>
        <div class="col-xs-1"></div>
    </a>
    <div class="clear"></div>
    <?php if (Yii::$app->params['mall_enabled']) { ?>
    <a class="row sm-height border-bottom block end-list" href="/mall/portal" >
        <div class="col-xs-10 left-txt">积分商城</div>
    </a>
    <?php }?>
</div>
<form></form>
<!-- 账户中心页 end  -->

<script type="text/javascript">
    var err = '<?= isset($data['code']) ? $data['code'] : '' ?>';
    var mess = '<?= isset($data['message']) ? $data['message'] : '' ?>';
    var tourl = '<?= isset($data['tourl']) ? $data['tourl'] : '' ?>';

    function tixian()
    {
        if (err === '1') {
            $('.account div').eq(0).css('background', '#e8eaf0');
            toast(mess, function() {
                $('.account div').eq(0).css('background', '#fff');
                if (tourl !== '') {
                    location.href = tourl;
                }
            });
        } else {
            location.href='/user/userbank/tixian';
        }
    }

    function recharge()
    {
        if(err === '1') {
            $('.account div').eq(1).css('background', '#e8eaf0');
            toast(mess, function() {
                $('.account div').eq(1).css('background', '#fff');
                if (tourl !== '') {
                    location.href = tourl;
                }
            });
        } else {
            location.href='/user/userbank/recharge';
        }
    }

    $(function() {
        FastClick.attach(document.body);
        var flag = 0;

        $('.accountcenter-center-left').on('click', '.dj-tip-refer', function(event) {
            var event = event || window.event;
            if (!flag) {
                $('.sm-height_tips').show();
                flag = 1;
            } else {
                $('.sm-height_tips').hide();
                flag = 0;
            }
            event.stopPropagation();
        })

        $('html').on('click', 'body', function() {
            if (flag) {
                $('.sm-height_tips').hide();
                flag = 0;
            }
        });
    });

</script>
