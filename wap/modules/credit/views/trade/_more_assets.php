<?php
use common\utils\StringUtils;
?>

<?php foreach ($data as $val) { ?>
    <?php if (1 === $type) { ?>
        <a class="row col common-mar" href="/user/user/orderdetail?id=<?= $val['order_id'] ?>&asset_id=<?= $val['id'] ?>">
            <div class="col-xs-12 transferitem-list-title">
                <div class="inner-border"><?= $val['loan']->title ?></div>
            </div>
            <div class="col-xs-7 font-24 common-line-height common-pad-lf">可转让金额：<span class="font-28 common-color"><?= StringUtils::amountFormat2(bcdiv($val['maxTradableAmount'], 100, 2)) ?>元</span></div>
            <div class="col-xs-5 font-32 common-color text-align-ct common-line-height"><?= StringUtils::amountFormat2($val['order']->yield_rate * 100) ?>%</div>
            <div class="col-xs-7 font-24 common-line-height common-pad-lf">项目结束：<span class="font-28"><?= date('Y-m-d', $val['loan']->finish_date) ?></span></div>
            <div class="col-xs-5 font-24 text-align-ct common-line-height">预期年化率</div>
        </a>
    <?php } elseif (2 === $type) { ?>
        <a class="row col common-mar" href="/">
            <div class="col-xs-12 transferitem-list-title">
                <div class="inner-border"></div>
            </div>
            <div class="col-xs-7 font-24 common-line-height common-pad-lf">转让金额：<span class="font-28 common-color"><?= StringUtils::amountFormat2(bcdiv($val['amount'], 100, 2)) ?>元</span></div>
            <div class="col-xs-5 font-32 common-color text-align-ct common-line-height"><?= bcdiv($val['tradedAmount'], $val['amount'], 0) ?>%</div>
            <div class="col-xs-7 font-24 common-line-height common-pad-lf">转让日期：<span class="font-28"><?= substr($val['createTime'], 0, 10) ?></span></div>
            <div class="col-xs-5 font-24 text-align-ct common-line-height">转让进度</div>
        </a>
    <?php } elseif (3 === $type) { ?>
        <a class="row col common-mar" href="/">
            <div class="col-xs-12 transferitem-list-title">
                <div class="inner-border"><?= $val['loan']->title ?></div>
            </div>
            <div class="col-xs-7 font-24 common-line-height common-pad-lf">已转让金额：<span class="font-28 common-color"><?= StringUtils::amountFormat2(bcdiv($val['tradedAmount'], 100, 2)) ?>元</span></div>
            <div class="col-xs-5 font-32 common-color text-align-ct common-line-height">
                <?php
                    $config = json_decode($val['config'], true);
                    $fee = bcmul($config['fee_rate'], $val['amount']);
                    StringUtils::amountFormat2(bcdiv($fee, 100, 2));
                ?>
            元</div>
            <div class="col-xs-7 font-24 common-line-height common-pad-lf">完成日期：<span class="font-28"><?= substr($val['closeTime'], 0, 10) ?></span></div>
            <div class="col-xs-5 font-24 text-align-ct common-line-height">实际收入</div>
        </a>
    <?php } ?>
<?php } ?>