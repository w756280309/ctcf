<?php
$this->title = '交易明细';

$this->registerCssFile(ASSETS_BASE_URI.'css/pagination.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerCssFile(ASSETS_BASE_URI.'css/useraccount/transactiondetail.css', ['depends' => 'frontend\assets\FrontAsset']);

use common\models\user\MoneyRecord;
use common\utils\StringUtils;
use common\widgets\Pager;
?>

<div class="transactionDetail-box">
    <div class="transactionDetail-header">
        <div class="transactionDetail-header-icon"></div>
        <span class="transactionDetail-header-font">交易明细</span>
    </div>
    <div class="transactionDetail-content">
        <table>
            <tr>
                <th width="120" class="table-text-left">类型</th>
                <th width="130" class="text-align-lf">时间</th>
                <th width="110" class="text-align-rg">变动金额(元)</th>
                <th width="130" class="text-align-rg">可用余额(元)</th>
                <th width="260" class="table-text-right"><p>详情</p></th>
            </tr>
            <?php foreach ($lists as $key => $val) : ?>
                <tr class="<?= 0 === $key%2 ? '' : 'td-back-color' ?>">
                    <td class="table-text-left"><?= Yii::$app->params['mingxi'][$val->type] ?></td>
                    <td class="text-align-lf"><?= date('Y-m-d H:i:s', $val->created_at) ?></td>
                    <td class="text-align-rg <?= $val->in_money > $val->out_money ? 'color-red' : 'color-green' ?>">
                        <?= $val->in_money > $val->out_money ? ('+' . StringUtils::amountFormat3($val->in_money)) : ('-' . StringUtils::amountFormat3($val->out_money)) ?>
                    </td>
                    <td class="text-align-rg"><?= number_format($val->balance, 2) ?></td>
                    <td class="table-text-right">
                        <?php if ($val->type === MoneyRecord::TYPE_ORDER || $val->type === MoneyRecord::TYPE_HUIKUAN) { ?>
                            <a class="table-link color-blue" href="<?= isset($desc[$key]['sn']) ? '/deal/deal/detail?sn='.$desc[$key]['sn'] : 'javascript:void(0)' ?>">
                                <?= $desc[$key]['desc'] ?>
                            </a>
                        <?php } else { ?>
                            <p>流水号：<?= $desc[$key]['desc'] ?></p>
                        <?php } ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <center><?= Pager::widget(['pagination' => $pages]); ?></center>

        <?php if (!$lists) : ?>
            <div class="table-kong"></div>
            <div class="table-kong"></div>
            <div class="table-kong"></div>
            <p class="without-font">暂无交易明细</p>
        <?php endif; ?>
    </div>
</div>