<?php
    use common\models\user\User;
    use common\models\order\OnlineOrder;
    $this->registerCss(<<<CSS
    .div_list3 {float: left; width: 31.623931623931625%; margin-left: 2.564102564102564%;min-height: 30px;}
    .div_list3_first {float: left; width: 31.623931623931625%; min-height: 30px;}
CSS
);
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                汇总统计
                <small style="color: red;">数据实时更新</small>
            </h3>
        </div>
    </div>
    <div class="row-fluid">
        <div style="float: left;width: 30%;"><h3>资金统计</h3></div>
        <div style="float: right; width: 70%;">
            <a href="/datatj/datatj/affiliation" class="btn blue btn-block" style="display: block;margin: 0 10px 0 0; float: right;  width: 130px;">分销商数据统计</a>
            <a href="/datatj/datatj/daytj" class="btn blue btn-block" style="display: block;margin: 0 10px 0 0; float: right; width: 100px;">日历史数据</a>
            <a href="/datatj/datatj/monthtj" class="btn blue btn-block" style="display: block; margin: 0 10px 0 0;  float: right; width: 100px;">月历史数据</a>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span4">平台累计交易额：<span style="color: red;"><?= number_format($totalTotalInve, 2) ?></span> 元</div>
        <div class="span4">线上累计交易额：<?= number_format($totalOnlineInve, 2) ?>元</div>
        <div class="span4">线下累计交易额：<?= number_format($totalOfflineInve, 2) ?>元</div>
    </div>
    <div class="row-fluid">
        <div class="span4">本月交易额：<span style="color: red;"><?= number_format($monthTotalInvestment, 2) ?></span> 元</div>
        <div class="span4">线上本月交易额：<?= number_format($monthOnlineInvestment, 2) ?>元</div>
        <div class="span4">线下本月交易额：<?= number_format($monthOfflineInvestment, 2) ?>元</div>
    </div>
    <div class="row-fluid">
        <div class="span12">今日线上交易额：<?= number_format($todayOnlineInvestment, 2) ?>元</div>
    </div>
    <div class="row-fluid">
        <div class="span4">温盈宝累计销售额：<?= number_format($totalInvestmentInWyb, 2) ?> 元</div>
        <div class="span4">温盈金累计销售额：<?= number_format($totalInvestmentInWyj, 2) ?>元</div>
    </div>
    <div class="row-fluid">
        <div class="span4">贷后余额： <?= number_format($remainMoney, 2) ?>元</div>
        <div class="span4">平台可用余额：<?= number_format($usableMoney, 2) ?>元</div>
    </div>
    <div class="row-fluid">
        <div class="span4">今日充值金额：<?= number_format($todayRechargeMoney, 2) ?>元</div>
        <div class="span4">今日提现金额：<?= number_format($todayDraw, 2) ?>元</div>
    </div>
    <div class="row-fluid">
        <div class="span4">今日温盈宝销售额：<?= number_format($todayInvestmentInWyb, 2) ?>元</div>
        <div class="span4">今日温盈金销售额：<?= number_format($todayInvestmentInWyj, 2) ?>元</div>
    </div>
    <div class="row-fluid">
        <div class="span4">累计充值手续费：<?= number_format($totalRechargeCost, 2) ?>元</div>
        <div class="span4">今日充值手续费：<?= number_format($toadyRechargeCost, 2) ?>元</div>
    </div>
    <div class="row-fluid">
        <?php if($investorData) {
            foreach ($investorData as $k => $v) {
                $from = intval($v['f']);
                $money = number_format($v['m'], 2);
                if ($from === OnlineOrder::INVEST_FROM_WAP) {
                    echo '<div class="' . (($k%3 === 0) ? 'div_list3_first' : 'div_list3') . '">wap投资：' . $money . ' 元</div>';
                } else if ($from === OnlineOrder::INVEST_FROM_WX) {
                    echo '<div class="' . (($k%3 === 0) ? 'div_list3_first' : 'div_list3') . '">微信投资：' . $money . ' 元</div>';
                } else if ($from === OnlineOrder::INVEST_FROM_APP) {
                    echo '<div class="' . (($k%3 === 0) ? 'div_list3_first' : 'div_list3') . '">app投资：' . $money . ' 元</div>';
                } else if ($from ===OnlineOrder::INVEST_FROM_PC) {
                    echo '<div class="' . (($k%3 === 0) ? 'div_list3_first' : 'div_list3') . '">pc投资：' . $money . ' 元</div>';
                } else {
                    echo '<div class="' . (($k%3 === 0) ? 'div_list3_first' : 'div_list3') . '">未知来源投资：' . $money . ' 元</div>';
                }
            }
        } ?>
    </div>
    <div class="row-fluid">
        <h3>用户统计</h3>
    </div>
    <div class="row-fluid">
        <div class="span4">平台累计注册用户数：<span style="color: red;"><?= intval($totalReg) ?></span>人</div>
        <div class="span4">平台累计实名认证用户数：<span style="color: red;"><?= intval($totalIdVerified) ?></span>人</div>
    </div>
    <div class="row-fluid">
        <div class="span4">平台累计绑卡用户数：<span style="color: red;"><?= intval($totalQpayEnabled) ?></span>人</div>
        <div class="span4">平台累计投资人数：<span style="color: red;"><?= intval($totalInvestor) ?></span>人</div>
    </div>
    <div class="row-fluid">
        <div class="span4">平台今日注册用户数：<?= intval($todayReg) ?>人</div>
        <div class="span4">平台今日实名认证用户数：<?= intval($todayIdVerified) ?>人</div>
    </div>
    <div class="row-fluid">
        <div class="span4">平台今日绑卡用户数：<?= intval($qpayEnabled) ?>人</div>
        <div class="span4">平台非今日注册于今日投资新增人数：<?= intval($newInvestor) ?>人</div>
        <div class="span4">平台当日注册当日投资人数：<?= intval($newRegisterAndInvestor) ?>人</div>
    </div>
    <div class="row-fluid">
        <div class="span4">平台今日已投用户登录数：<?= intval($investAndLogin) ?>人</div>
        <div class="span4">平台今日未投用户登录数：<?= intval($notInvestAndLogin) ?>人</div>
    </div>
    <div class="row-fluid">
        <?php if($registerData) {
            foreach ($registerData as $k => $v) {
                $from = intval($v['f']);
                $count = intval($v['c']);
                if ($from === User::REG_FROM_WAP) {
                    echo '<div class="' . (($k%3 === 0) ? 'div_list3_first' : 'div_list3') . '">wap注册：' . $count . ' 人</div>';
                } else if ($from === User::REG_FROM_WX) {
                    echo '<div class="' . (($k%3 === 0) ? 'div_list3_first' : 'div_list3') . '">微信注册：' . $count . ' 人</div>';
                } else if ($from === User::REG_FROM_APP) {
                    echo '<div class="' . (($k%3 === 0) ? 'div_list3_first' : 'div_list3') . '">app注册：' . $count . ' 人</div>';
                } else if ($from === User::REG_FROM_PC) {
                    echo '<div class="' . (($k%3 === 0) ? 'div_list3_first' : 'div_list3') . '">pc注册：' . $count . ' 人</div>';
                } else {
                    echo '<div class="' . (($k%3 === 0) ? 'div_list3_first' : 'div_list3') . '">未知来源注册：' . $count . ' 人</div>';
                }
            }
        } ?>
    </div>
    <div class="row-fluid">
        <?php if($investorData) {
            foreach ($investorData as $k => $v) {
                $from = intval($v['f']);
                $count = intval($v['c']);
                if ($from === OnlineOrder::INVEST_FROM_WAP) {
                    echo '<div class="' . (($k%3 === 0) ? 'div_list3_first' : 'div_list3') . '">wap投资：' . $count . ' 人</div>';
                } else if ($from === OnlineOrder::INVEST_FROM_WX) {
                    echo '<div class="' . (($k%3 === 0) ? 'div_list3_first' : 'div_list3') . '">微信投资：' . $count . ' 人</div>';
                } else if ($from ===OnlineOrder::INVEST_FROM_APP) {
                    echo '<div class="' . (($k%3 === 0) ? 'div_list3_first' : 'div_list3') . '">app投资：' . $count . ' 人</div>';
                } else if ($from === OnlineOrder::INVEST_FROM_PC) {
                    echo '<div class="' . (($k%3 === 0) ? 'div_list3_first' : 'div_list3') . '">pc投资：' . $count . ' 人</div>';
                } else {
                    echo '<div class="' . (($k%3 === 0) ? 'div_list3_first' : 'div_list3') . '">未知来源投资：' . $count . ' 人</div>';
                }
            }
        } ?>
    </div>
    <div class="row-fluid">
        <h3>项目统计</h3>
    </div>
    <div class="row-fluid">
        <div class="span4">平台累计融资项目数：<span style="color: red"><?= intval($totalSuccessFound) ?></span>个</div>
        <div class="span4">平台本月融资项目数：<?= intval($monthSuccessFound) ?>个</div>
        <div class="span4">平台今日融资项目数：<?= intval($todaySuccessFound) ?>个</div>
    </div>
    <div class="row-fluid">
        <h3>代金券统计</h3>
    </div>
    <div class="row-fluid">
        <div class="span4">平台累计已发放代金券：<?= number_format($totalCoupon, 2) ?>元</div>
        <div class="span4">平台累计已使用代金券：<?= number_format($usedCoupon, 2) ?>元</div>
        <div class="span4">平台累计未使用代金券：<?= number_format($unusedCoupon, 2) ?>元</div>
    </div>
</div>
<?php $this->endBlock(); ?>
