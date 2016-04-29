<?php $this->beginBlock('blockmain'); ?>
<style>
    .stats-perf-daily {
        margin-top: 1em;
    }
    .stats-perf-daily td {
        text-align: right;
    }
    .stats-perf-daily td.tcol-id {
        text-align: left;
    }
</style>
<div class="container-fluid">
    <table class="stats-perf-daily table table-striped">
        <tr>
            <td class="tcol-id">日期</td>
            <td>注册数</td>
            <td>实名认证</td>
            <td>绑卡</td>
            <td>投资人数</td>
            <td>新增投资人数</td>
            <td>POS充值</td>
            <td>线上充值</td>
            <td>提现</td>
            <td>温盈金</td>
            <td>温盈宝</td>
            <td>投资金额</td>
        </tr>
        <?php foreach($perfs as $perf): ?>
        <tr>
            <td class="tcol-id"><?= $perf->bizDate ?></td>
            <td><?= $perf->reg ?></td>
            <td><?= $perf->idVerified ?></td>
            <td><?= $perf->qpayEnabled ?></td>
            <td><?= $perf->investor ?></td>
            <td><?= $perf->newInvestor ?></td>
            <td><?= $perf->chargeViaPos ?></td>
            <td><?= $perf->chargeViaEpay ?></td>
            <td><?= $perf->drawAmount ?></td>
            <td><?= $perf->investmentInWyj ?></td>
            <td><?= $perf->investmentInWyb ?></td>
            <td><?= $perf->totalInvestment ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php $this->endBlock(); ?>
