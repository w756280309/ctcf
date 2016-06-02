<?php
$this->title = '我的代金券';

use yii\widgets\LinkPager;
?>

可用代金券:<?= empty($data['totalAmount']) ? 0 : rtrim(rtrim(number_format($data['totalAmount'], 2), '0'), '.') ?>元&nbsp;
共<?= $data['count'] ?>个<br><br>

<table>
    <tr>
        <th>面值(元)</th>
        <th>使用规则</th>
        <th>使用期限</th>
        <th>状态</th>
    </tr>

    <?php foreach ($model as $val) : ?>
    <tr>
        <td><?= rtrim(rtrim(number_format($val->couponType->amount, 2), '0'), '.') ?></td>
        <td>
            单笔投资满<?= \Yii::$app->functions->toFormatMoney(rtrim(rtrim($val->couponType->minInvest, '0'), '.')) ?>可用；
            <?php
               if (empty($val->couponType->loanCategories)) {
                   echo '所有';
               } else {
                   $arr = array_filter(explode(',', $val->couponType->loanCategories));

                   foreach ($arr as $k => $v) {
                       $arr[$k] = \Yii::$app->params['pc_cat'][$v];
                   }

                   echo implode('、', $arr);
               }
            ?>项目可用；
        </td>
        <td><?= $val->couponType->useStartDate ?>至<?= $val->couponType->useEndDate ?></td>
        <td>
            <?php
                if ($val->isUsed) {
                    echo '已使用';
                } elseif (date('Y-m-d') > $val->couponType->useEndDate) {
                    echo '已过期';
                } else {
                    echo '未使用';
                }
            ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?= LinkPager::widget(['pagination' => $pages]); ?>

<br>