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
    <div style="float: right">
        <a href="/datatj/datatj/monthtj" class="btn blue btn-block" style="display: block;">月历史数据</a>
        <a href="/datatj/datatj/daytj" class="btn blue btn-block" style="display: block;margin-top: 10px;">日历史数据</a>
    </div>
    <div class="row-fluid">
        <h3>资金统计</h3>
    </div>
    <div class="row-fluid">
        <div class="span4">平台累计交易额：<span style="color: red;"><?= number_format($totalTotalInve, 2) ?></span> 元</div>
        <div class="span4">本月交易额：<?= number_format($monthTotalInvestment, 2) ?>元</div>
        <div class="span4">今日交易额：<?= number_format($todayTotalInve, 2) ?>元</div>
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
        <h3>用户统计</h3>
    </div>
    <div class="row-fluid">
        <div class="span4">平台累计注册用户数：<span style="color: red;"><?= intval($totalReg) ?></span>人</div>
        <div class="span4">平台累计实名认证用户数：<span style="color: red;"><?= intval($totalQpayEnabled) ?></span>人</div>
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
        <div class="span4">平台今日新增投资人数：<?= intval($newInvestor) ?>人</div>
        <div class="span4">平台当日注册当日投资人数：<?= intval($newRegisterAndInvestor) ?>人</div>
    </div>
    <div class="row-fluid">
        <h3>项目统计</h3>
    </div>
    <div class="row-fluid">
        <div class="span4">平台累计融资项目数：<span style="color: red"><?= intval($totalSuccessFound) ?></span>个</div>
        <div class="span4">平台本月融资项目数：<?= intval($monthSuccessFound) ?>个</div>
        <div class="span4">平台今日融资项目数：<?= intval($todaySuccessFound) ?>个</div>
    </div>
</div>
<?php $this->endBlock(); ?>
