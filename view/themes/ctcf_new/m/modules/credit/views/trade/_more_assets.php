<?php
use common\utils\StringUtils;
?>

<?php foreach ($data as $val) { ?>
    <?php if (1 === $type) { ?>
        <a class="row col common-mar" href="/user/user/orderdetail?asset_id=<?= $val['id'] ?>&from_transfer=1">
            <div class="col-xs-12 transferitem-list-title">
                <div class="inner-border"><?= empty($val['note_id']) ? '' : '【转让】' ?><?= $val['loan']->title ?></div>
            </div>
            <div class="col-xs-7 font-24 common-line-height common-pad-lf">可转让金额：<span class="font-28 common-color"><?= StringUtils::amountFormat2(bcdiv($val['maxTradableAmount'], 100, 2)) ?>元</span></div>
            <div class="col-xs-5 font-32 common-color text-align-ct common-line-height"><?= StringUtils::amountFormat2($val['order']->yield_rate * 100) ?>%</div>
            <div class="col-xs-7 font-24 common-line-height common-pad-lf">项目结束：<span class="font-28"><?= date('Y-m-d', $val['loan']->finish_date) ?></span></div>
            <div class="col-xs-5 font-24 text-align-ct common-line-height">预期年化率</div>
        </a>
    <?php } elseif (2 === $type) { ?>
        <a class="row col common-mar" href="/credit/note/detail?id=<?= $val['id'] ?>&fromType=2">
            <div class="col-xs-12 transferitem-list-title">
                <div class="inner-border">【转让】<?= $val['loan']->title ?></div>
            </div>
            <div class="col-xs-7 font-24 common-line-height common-pad-lf">转让金额：<span class="font-28 common-color"><?= StringUtils::amountFormat2(bcdiv($val['amount'], 100, 2)) ?>元</span></div>
            <div class="col-xs-5 font-32 common-color text-align-ct common-line-height"><?= bcmul(bcdiv($val['tradedAmount'], $val['amount'], 14), 100, 0) ?>%</div>
            <div class="col-xs-7 font-24 common-line-height common-pad-lf">转让日期：<span class="font-28"><?= substr($val['createTime'], 0, 10) ?></span></div>
            <div class="col-xs-5 font-24 text-align-ct common-line-height">转让进度</div>
        </a>
    <?php } elseif (3 === $type) { ?>
        <a class="row col common-mar" href="/credit/note/detail?id=<?= $val['id'] ?>&fromType=3">
            <div class="col-xs-12 transferitem-list-title">
                <div class="inner-border">【转让】<?= $val['loan']->title ?></div>
            </div>
            <div class="col-xs-7 font-24 common-line-height common-pad-lf">已转让金额：<span class="font-28 common-color"><?= StringUtils::amountFormat2(bcdiv($val['tradedAmount'], 100, 2)) ?>元</span></div>
            <div class="col-xs-5 font-32 common-color text-align-ct common-line-height"><?= StringUtils::amountFormat3(bcdiv(isset($actualIncome[$val['id']]) ? $actualIncome[$val['id']]['actualIncome'] : 0, 100, 2)) ?>元</div>
            <div class="col-xs-7 font-24 common-line-height common-pad-lf">完成日期：<span class="font-28"><?= substr($val['closeTime'], 0, 10) ?></span></div>
            <div class="col-xs-5 font-24 text-align-ct common-line-height">实际收入</div>
        </a>
    <?php } ?>
<?php } ?>