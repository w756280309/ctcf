<?php
$this->title = '我要理财';

use common\models\product\OnlineProduct;
use common\widgets\Pager;
?>

<table>
    <tr>
        <th>计息方式</th>
        <th>标的名称</th>
        <th>年化利率</th>
        <th>项目期限</th>
        <th>融资金额</th>
        <th>进度</th>
        <th>可投余额</th>
        <th>项目状态</th>
    </tr>

    <?php foreach ($loans as $val) : ?>
    <tr>
        <td><?= Yii::$app->params['refund_method'][$val->refund_method] ?></td>
        <td><?= $val->title ?></td>
        <td><?= rtrim(rtrim(number_format(OnlineProduct::calcBaseRate($val->yield_rate, $val->jiaxi), 2), '0'), '.') ?>%</td>
        <td><?= $val->expires.(1 === (int) $val->refund_method ? "天" : "个月") ?></td>
        <td><?= rtrim(rtrim(number_format($val->money, 2), '0'), '.') ?>元</td>
        <td><?= number_format($val->finish_rate * 100) ?>%</td>
        <td><?= rtrim(rtrim(number_format($val->money - $val->funded_money, 2), '0'), '.') ?>元</td>
        <td><?= Yii::$app->params['deal_status'][$val->status] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<?= Pager::widget(['pagination' => $pages]); ?>

<br>
