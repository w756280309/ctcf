<?php
$this->title = '限额提醒';

$this->registerCssFile(ASSETS_BASE_URI.'css/useraccount/limitamount.css', ['depends' => 'frontend\assets\FrontAsset']);

use common\utils\StringUtils;
?>

<div class="limit-box">
    <div class="limit-header">
        <div class="limit-header-icon"></div>
        <span class="limit-header-font">银行限额</span>
    </div>
    <div class="limit-inner">
        <div class="limit-title">快捷充值各银行限额说明（仅限储蓄卡）</div>
        <ul class="limit-content clearfix">
            <li class="limit-right limit-bottom"><h4>银行</h4></li>
            <li class="limit-right limit-bottom"><h4>单笔</h4></li>
            <li class="limit-bottom"><h4>单日</h4></li>
            <?php $qcount = count($qpay) - 1; foreach ($qpay as $key => $val): ?>
                <li class="limit-right <?= $qcount === $key ? 'end-table' : 'limit-bottom' ?>"><?= $val->bank->bankName ?><?= $val->isDisabled ? '(暂停)' : '' ?></li>
                <li class="limit-right <?= $qcount === $key ? 'end-table' : 'limit-bottom' ?>"><?= StringUtils::amountFormat1('{amount}{unit}', $val->singleLimit) ?></li>
                <li class="<?= $qcount === $key ? 'end-table' : 'limit-bottom' ?>"><?= StringUtils::amountFormat1('{amount}{unit}', $val->dailyLimit) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>