<?php
    use common\models\user\User;
    use common\models\order\OnlineOrder;
    $this->registerCss(<<<CSS
    .div_list3 {float: left; width: 31.623931623931625%; margin-left: 2.564102564102564%;min-height: 30px;}
    .div_list3_first {float: left; width: 31.623931623931625%; min-height: 30px;}
CSS
);
    $pc_cat = Yii::$app->params['pc_cat'];
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid" style="font-family: monospace;">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                汇总统计
                <small style="color: red;">上次更新时间：<?= $lastUpdateTime ?></small>
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
        <div class="span4">平台累计交易额：<div class="pull-right"><span style="color: red;"><?= number_format($totalTotalInve, 2) ?></span>元</div></div>
        <div class="span4">线上累计交易额：<div class="pull-right"><?= number_format($totalOnlineInve, 2) ?>元</div></div>
        <div class="span4">线下累计交易额：<div class="pull-right"><?= number_format($totalOfflineInve, 2) ?>元</div></div>
    </div>
    <div class="row-fluid">
        <div class="span4">本月交易额：<div class="pull-right"><span style="color: red;"><?= number_format($monthTotalInvestment, 2) ?></span>元</div></div>
        <div class="span4">线上本月交易额：<div class="pull-right"><?= number_format($monthOnlineInvestment, 2) ?>元</div></div>
        <div class="span4">线下本月交易额：<div class="pull-right"><?= number_format($monthOfflineInvestment, 2) ?>元</div></div>
    </div>
    <div class="row-fluid">
        <div class="span4">今日线上交易额：<div class="pull-right"><?= number_format($todayOnlineInvestment, 2) ?>元</div></div>
        <div class="span4">线上年化累计交易额：<div class="pull-right"><?= number_format($onlineAnnualTotalInvestment, 2) ?>元</div></div>
        <div class="span4">线下年化累计交易额：<div class="pull-right"><?= number_format($offlineAnnualTotalInvestment, 2) ?>元</div></div>
    </div>
    <div class="row-fluid">
        <div class="span4"><?= $pc_cat['2'] ?>累计销售额：<div class="pull-right"><?= number_format($totalInvestmentInWyb, 2) ?>元</div></div>
        <div class="span4"><?= $pc_cat['1'] ?>累计销售额：<div class="pull-right"><?= number_format($totalInvestmentInWyj, 2) ?>元</div></div>
    </div>
    <div class="row-fluid">
        <div class="span4">贷后余额： <div class="pull-right"><?= number_format($remainMoney, 2) ?>元</div></div>
        <div class="span4">贷后年化余额： <div class="pull-right"><?= number_format($annualInvestment, 2) ?>元</div></div>
        <div class="span4">平台可用余额：<div class="pull-right"><?= number_format($usableMoney, 2) ?>元</div></div>
    </div>
    <div class="row-fluid">
        <div class="span4">今日充值金额：<div class="pull-right"><?= number_format($todayRechargeMoney, 2) ?>元</div></div>
        <div class="span4">今日提现金额：<div class="pull-right"><?= number_format($todayDraw, 2) ?>元</div></div>
    </div>
    <div class="row-fluid">
        <div class="span4">今日温盈宝销售额：<div class="pull-right"><?= number_format($todayInvestmentInWyb, 2) ?>元</div></div>
        <div class="span4">今日温盈金销售额：<div class="pull-right"><?= number_format($todayInvestmentInWyj, 2) ?>元</div></div>
    </div>
    <div class="row-fluid">
        <div class="span4">累计充值手续费：<div class="pull-right"><?= number_format($totalRechargeCost, 2) ?>元</div></div>
        <div class="span4">今日充值手续费：<div class="pull-right"><?= number_format($toadyRechargeCost, 2) ?>元</div></div>
    </div>
    <div class="row-fluid">
        <div class="span4">平台累计兑付金额：<div class="pull-right"><?= number_format($totalRefundAmount, 2) ?>元</div></div>
        <div class="span4">平台累计收益金额：<div class="pull-right"><?= number_format($totalRefundInterest, 2) ?>元</div></div>
    </div>
        <?php if($investorData) {
            echo '<div class="row-fluid">';
            foreach ($investorData as $k => $v) {
                $from = intval($v['f']);
                $money = number_format($v['m'], 2);
                if ($from === OnlineOrder::INVEST_FROM_WAP) {
                    echo '<div class="span4">wap投资：<div class="pull-right">' . $money . '元</div></div>';
                } else if ($from === OnlineOrder::INVEST_FROM_WX) {
                    echo '<div class="span4">微信投资：<div class="pull-right">' . $money . '元</div></div>';
                } else if ($from === OnlineOrder::INVEST_FROM_APP) {
                    echo '<div class="span4">app投资：<div class="pull-right">' . $money . '元</div></div>';
                } else if ($from ===OnlineOrder::INVEST_FROM_PC) {
                    echo '<div class="span4">pc投资：<div class="pull-right">' . $money . '元</div></div>';
                } else {
                    echo '<div class="span4">未知来源投资：<div class="pull-right">' . $money . '元</div></div>';
                }
                if($k ==2)
                {
                    echo '</div><div class="row-fluid">';
                }
            }
            echo '</div>';
        } ?>
    <div class="row-fluid">
        <h3>用户统计</h3>
    </div>
    <div class="row-fluid">
        <div class="span4">平台累计注册用户数：<div class="pull-right"><span style="color: red;"><?= intval($totalReg) ?></span>人</div></div>
        <div class="span4">平台累计实名认证用户数：<div class="pull-right"><span style="color: red;"><?= intval($totalIdVerified) ?></span>人</div></div>
    </div>
    <div class="row-fluid">
        <div class="span4">平台累计绑卡用户数：<div class="pull-right"><span style="color: red;"><?= intval($totalQpayEnabled) ?></span>人</div></div>
        <div class="span4">平台累计投资人数：<div class="pull-right"><span style="color: red;"><?= intval($totalInvestor) ?></span>人</div></div>
    </div>
    <div class="row-fluid">
        <div class="span4">平台今日注册用户数：<div class="pull-right"><?= intval($todayReg) ?>人</div></div>
        <div class="span4">平台今日实名认证用户数：<div class="pull-right"><?= intval($todayIdVerified) ?>人</div></div>
    </div>
    <div class="row-fluid">
        <div class="span4">平台今日绑卡用户数：<div class="pull-right"><?= intval($qpayEnabled) ?>人</div></div>
        <div class="span4">老客新投人数：<div class="pull-right"><a title="平台非今日注册于今日投资新增人数" href="#"><?= intval($newInvestor) ?></a>人</div></div>
        <div class="span4">新客新投人数：<div class="pull-right"><a title="平台当日注册当日投资人数" href="#"><?= intval($newRegisterAndInvestor) ?></a>人</div></div>
    </div>
    <div class="row-fluid">
        <div class="span4">平台今日已投用户登录数：<div class="pull-right"><?= intval($investAndLogin) ?>人</div></div>
        <div class="span4">平台今日未投用户登录数：<div class="pull-right"><?= intval($notInvestAndLogin) ?>人</div></div>
    </div>
        <?php if($registerData) {
            echo '<div class="row-fluid">';
            foreach ($registerData as $k => $v) {
                $from = intval($v['f']);
                $count = intval($v['c']);
                if ($from === User::REG_FROM_WAP) {
                    echo '<div class="span4">wap注册：<div class="pull-right">' . $count . '人</div></div>';
                } else if ($from === User::REG_FROM_WX) {
                    echo '<div class="span4">微信注册：<div class="pull-right">' . $count . '人</div></div>';
                } else if ($from === User::REG_FROM_APP) {
                    echo '<div class="span4">app注册：<div class="pull-right">' . $count . '人</div></div>';
                } else if ($from === User::REG_FROM_PC) {
                    echo '<div class="span4">pc注册：<div class="pull-right">' . $count . '人</div></div>';
                } else {
                    echo '<div class="span4">未知来源注册：<div class="pull-right">' . $count . '人</div></div>';
                }
                if($k ==2)
                {
                    echo '</div><div class="row-fluid">';
                }
            }
            echo '</div>';
        } ?>
        <?php if($investorData) {
            echo '<div class="row-fluid">';
            foreach ($investorData as $k => $v) {
                $from = intval($v['f']);
                $count = intval($v['c']);
                if ($from === OnlineOrder::INVEST_FROM_WAP) {
                    echo '<div class="span4">wap投资：<div class="pull-right">' . $count . '人</div></div>';
                } else if ($from === OnlineOrder::INVEST_FROM_WX) {
                    echo '<div class="span4">微信投资：<div class="pull-right">' . $count . '人</div></div>';
                } else if ($from ===OnlineOrder::INVEST_FROM_APP) {
                    echo '<div class="span4">app投资：<div class="pull-right">' . $count . '人</div></div>';
                } else if ($from === OnlineOrder::INVEST_FROM_PC) {
                    echo '<div class="span4">pc投资：<div class="pull-right">' . $count . '人</div></div>';
                } else {
                    echo '<div class="span4">未知来源投资：<div class="pull-right">' . $count . '人</div></div>';
                }
                if($k ==2)
                {
                    echo '</div><div class="row-fluid">';
                }
            }
            echo '</div>';
        } ?>
    <div class="row-fluid">
        <h3>项目统计</h3>
    </div>
    <div class="row-fluid">
        <div class="span4">平台累计融资项目数：<div class="pull-right"><span style="color: red"><?= intval($totalSuccessFound) ?></span>个</div></div>
        <div class="span4">平台本月融资项目数：<div class="pull-right"><?= intval($monthSuccessFound) ?>个</div></div>
        <div class="span4">平台今日融资项目数：<div class="pull-right"><?= intval($todaySuccessFound) ?>个</div></div>
    </div>
    <div class="row-fluid">
        <div class="span4">平台累计已还清项目数：<div class="pull-right"><?= intval($onlineProPay) ?>个</div></div>
    </div>
    <div class="row-fluid">
        <h3>代金券统计</h3>
    </div>
    <div class="row-fluid">
        <div class="span4">平台累计已发放代金券：<div class="pull-right"><?= number_format($totalCoupon, 2) ?>元</div></div>
        <div class="span4">平台累计已使用代金券：<div class="pull-right"><?= number_format($usedCoupon, 2) ?>元</div></div>
        <div class="span4">平台累计未使用代金券：<div class="pull-right"><?= number_format($unusedCoupon, 2) ?>元</div></div>
    </div>
</div>
<?php $this->endBlock(); ?>
