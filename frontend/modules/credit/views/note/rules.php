<?php
$this->title = '转让规则';

use frontend\assets\FrontAsset;

$this->registerCssFile(ASSETS_BASE_URI.'css/deal/productcontract.css?v=20160926', ['depends' => FrontAsset::class]);
?>

<div class="contract-box clearfix">
    <div class="contract-container">
        <div class="contract-container-box">
            <p class="contract-title">转让规则</p>
            <div class="rules-content">
                <h1>转让规则：</h1>
                <p>1、持有30天后即可进行转让，付息日和还款日前3天不可转让；</p>
                <p>2、转让周期3天，即72小时，最高折让率3%，转让的起投金额为1000元，递增金额1000元；</p>
                <p>3、受让人购买时，购买当天利息归出让人；</p>
                <p>4、购买转让的不可再次转让；</p>
                <p>5、手续费3‰，在成交后直接从成交金额中扣除，不成交平台不向用户收取手续费。</p>

                <h1>转让收益计算公式：</h1>
                <h2>受让人</h2>
                <p>支付利息＝（转让本金 / 未还本金）* 当期总利息 / 当期天数 * 当期持有天数</p>
                <p>成交金额＝（购买人支付利息 + 转让本金）*（1－折让率）</p>
                <p>总收益（还款计划中的收益）＝（转让本金 / 未还本金）* 剩余期数总利息</p>
                <p>当期收益＝（转让本金 / 未还本金）* 当期总利息 / 当期天数 * 当前剩余天数</p>
                <p>每期收益＝（转让本金 / 未还本金）* 每期总利息</p>
                <p>每期应还本金＝（转让本金 / 未还本金）* 每期未还本金</p>
                <h2>出让人</h2>
                <p>当期持有期利息＝（转让本金 / 未还本金）* 当期总利息 / 当期天数 * 当期持有天数</p>
                <p>手续费＝转让本金 * 3‰</p>
                <p>应收金额＝购买方成交金额 －手续费</p>
                <p>每期预期收益＝（未还本金 - 转让本金）/ 未还本金 * 每期总利息</p>
                <p>每期应还本金＝转让方每期未还本金－购买方每期应还本金</p>
            </div>
        </div>
    </div>
</div>