<?php

$this->title = '节前理财送大礼';
$this->share = $share;

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=1.0">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/activeComHeader.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/Qingming-Festival/css/index.css?v=1.3">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>

<div class="flex-content">
    <?php if (!defined('IN_APP')) { ?>
        <div class="header">
            <ul class="clearfix">
                <li class="lf f16"><img src="<?= FE_BASE_URI ?>wap/wendumao/images/logo.png" alt="">楚天财富国资平台</li>
                <li class="rg f13"><a href="/">返回首页</a></li>
            </ul>
        </div>
    <?php } ?>

    <div class="treeBanner">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/Qingming-Festival/img/banner.png" alt="">
    </div>
    <div class="treeGifts">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/Qingming-Festival/img/gifts_01.png" alt="">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/Qingming-Festival/img/gifts_02.png" alt="">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/Qingming-Festival/img/gifts_03.png" alt="">
    </div>

    <div class="treeContent">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/Qingming-Festival/img/regular.png" alt="">
        <ol class="f14">
            <li class="com">此活动为限时活动，仅限2017年3月30日至4月1日三天，以出借成功时间为准；</li>
            <li class="com">本次活动面向所有楚天财富注册用户；</li>
            <li class="com">活动期间出借楚天财富平台产品累计年化金额达到指定额度，即可获得相应礼品（不含转让产品）；</li>
            <div class="giftsList special">
                <p class="f12">各款产品累计年化金额及对应礼品如下：</p>
                <table class="tablebox" cellpadding="0" cellspacing="0">
                    <tr class="trSpical" style="border: none;">
                        <td style="border-right: 1px solid #be1c2b;">累计年化金额（元）</td>
                        <td>礼品</td>
                    </tr>
                    <tr style="border: none;">
                        <td>3,000,000</td>
                        <td style="text-align: left;padding-left: 0.5rem;">iPhone7 128G红色特别版</td>
                    </tr>
                    <tr>
                        <td>1,000,000</td>
                        <td style="text-align: left;padding-left: 0.5rem;">上海迪士尼乐园家庭套票
                            <br>(2成人+1儿童）</td>
                    </tr>
                    <tr>
                        <td>500,000</td>
                        <td style="text-align: left;padding-left: 0.5rem;">日本象印不锈钢保温暖壶(1.5L)
                            <br>+保温杯(480ml)组合套装</td>
                    </tr>
                    <tr>
                        <td>100,000</td>
                        <td style="text-align: left;padding-left: 0.5rem;">沃尔玛超市70元购物卡</td>
                    </tr>
                    <tr>
                        <td>50,000</td>
                        <td style="text-align: left;padding-left: 0.5rem;">金龙鱼 玉米油 1.8L</td>
                    </tr>
                </table>
            </div>
            <li class="com">活动结束后3个工作日内，工作人员将与您联系确认领取奖品事宜，请保持通讯畅通。</li>
            <li class="goInvest special f18"><a href="/deal/deal/index">去理财</a></li>
            <li class="declear special">*本次活动最终解释权归楚天财富所有</li>
        </ol>
    </div>
</div>