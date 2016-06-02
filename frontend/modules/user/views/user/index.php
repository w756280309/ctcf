<?php
    $this->title = '账户中心首页';
    use Yii;
    use common\models\order\OnlineRepaymentPlan as Plan;
?>
<p>是否实名<?= $user->isIdVerified()?></p>
<p>手机号<?= $user->mobile?></p>
<p>是否绑卡<?= $user->isQpayEnabled()?></p>
<p>累计投资<?= $user->getTotalInvestment()?></p>
<p>累计充值<?= $user->getTotalRecharge()?></p>
<p>累计提现<?= $user->getTotalDraw()?></p>
<p>已赚利息<?= $user->getProfit()?></p>
<p>待收利息<?= $user->getPendingProfit()?></p>

<p>可用余额<?= $user->lendAccount->available_balance?></p>
<p>理财资产<?= $user->lendAccount->investment_balance?></p>
<p>冻结资金<?= $user->lendAccount->freeze_balance?></p>
<table>
    <tr>
        <td>项目名称</td>
        <td>投资金额</td>
        <td>预期收益</td>
        <td>项目期限</td>
        <td>状态</td>
    </tr>
    <?php foreach($orders as $model) { ?>
    <tr>
        <td><?=$model->loan->title?></td>
        <td><?=$model->order_money?></td>
        <td><?=Plan::getTotalLixi($model->loan, $model)?></td>
        <td>
        <?= $model->loan->expires ?>
        <?= $model->loan->refund_method ? "天" : "个月" ?>
        </td>
        <td><?= Yii::$app->params['deal_status'][$model->loan->status] ?></td>
    </tr>
    <?php } ?>
</table>