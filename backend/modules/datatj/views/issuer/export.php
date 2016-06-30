<?php
use common\utils\StringUtils;
?>

<table border='1'>
    <tr>
        <th>期数</th>
        <th>融资方</th>
        <th>发行方</th>
        <th>项目名称</th>
        <th>项目编号</th>
        <th>项目状态</th>
        <th>募集金额（元）</th>
        <th>实际募集金额（元）</th>
        <th>开始融资时间</th>
        <th>满标时间</th>
        <th>起息日</th>
        <th>还款本金</th>
        <th>还款利息</th>
        <th>预计还款时间</th>
        <th>实际还款时间</th>
    </tr>
    <?php foreach($model as $key => $val) : ?>
        <?php if (isset($plan[$key])) {  ?>
            <?php foreach ($plan[$key] as $v) : ?>
                <tr>
                    <td><?= $v['qishu'] ?></td>
                    <td><?= $val->borrower->org_name ?></td>
                    <td><?= $issuer->name ?></td>
                    <td><?= $val->title ?></td>
                    <td><?= $val->issuerSn ?></td>
                    <td><?= \Yii::$app->params['deal_status'][$val->status] ?></td>
                    <td><?= StringUtils::amountFormat2($val->money) ?></td>
                    <td class="text-align-rg"><?= StringUtils::amountFormat2($val->funded_money) ?></td>
                    <td><?= empty($val->start_date) ? '---' : date('Y-m-d', $val->start_date) ?></td>
                    <td><?= empty($val->full_time) ? '---' : date('Y-m-d', $val->full_time) ?></td>
                    <td><?= empty($val->jixi_time) ? '---' : date('Y-m-d', $val->jixi_time) ?></td>
                    <td><?= $v['totalBenjin'] ?></td>
                    <td><?= $v['totalLixi'] ?></td>
                    <td><?= date('Y-m-d', $v['refund_time']) ?></td>
                    <td><?= isset($refundTime[$key][$v['qishu']]) ? date('Y-m-d', $refundTime[$key][$v['qishu']]) : '---' ?></td>
                </tr>
            <?php endforeach; ?>
        <?php } else { ?>
            <tr>
                <td>---</td>
                <td><?= $val->borrower->org_name ?></td>
                <td><?= $issuer->name ?></td>
                <td><?= $val->title ?></td>
                <td><?= $val->issuerSn ?></td>
                <td><?= \Yii::$app->params['deal_status'][$val->status] ?></td>
                <td><?= StringUtils::amountFormat2($val->money) ?></td>
                <td class="text-align-rg"><?= StringUtils::amountFormat2($val->funded_money) ?></td>
                <td><?= empty($val->start_date) ? '---' : date('Y-m-d', $val->start_date) ?></td>
                <td><?= empty($val->full_time) ? '---' : date('Y-m-d', $val->full_time) ?></td>
                <td><?= empty($val->jixi_time) ? '---' : date('Y-m-d', $val->jixi_time) ?></td>
                <td>---</td>
                <td>---</td>
                <td>---</td>
                <td>---</td>
            </tr>
        <?php } ?>
    <?php endforeach; ?>
</table>