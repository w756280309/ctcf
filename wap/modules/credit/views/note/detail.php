<?php
$this->title = '转让详情';

use common\utils\StringUtils;

$this->registerCssFile(ASSETS_BASE_URI.'css/credit/detail.css', ['depends' => 'wap\assets\WapAsset']);
$this->registerJs('var remainTime = '.strtotime($respData['endTime']).';', 1);
$this->registerJsFile(ASSETS_BASE_URI.'js/credit/detail.js', ['depends' => 'wap\assets\WapAsset']);
?>

<div class="row daojishi">
    <div class="col-xs-12">
        <div><!--距离结束：2天7时22分18秒--></div>
    </div>
</div>
<div class="row column">
    <div class="col-xs-12 column-title">
        <div>【转让】</div><span><?= $loan->title ?></span>
    </div>
    <div class="row xuxian">
        <div class="col-xs-12">
            <div></div>
        </div>
    </div>
    <div class="container">
        <ul class="row column-content rate-steps">
            <li class="col-xs-6">
                <div class="xian">
                    <?= StringUtils::amountFormat2($order->yield_rate * 100) ?><span class="column-lu">%</span>
                </div>
                <span class="qing">预期年化收益率</span>
            </li>
            <li class="col-xs-6">
                <div>
                    <?php
                        $remainingDuration = $loan->remainingDuration;

                        echo (isset($remainingDuration['months']) ? $remainingDuration['months'].'<span class="column-lu">个月</span>'
                            : '').$remainingDuration['days'].'<span class="column-lu">天</span>';
                    ?>
                </div>
                <span class="qing">剩余期限</span>
            </li>
        </ul>
    </div>
</div>
<div class="row bili">
    <div class="col-xs-12">
        <div class="per">
            <?php $progress = $respData['isClosed'] ? 100 : bcdiv(bcmul($respData['tradedAmount'], 100), $respData['amount'], 0); ?>
            <div class="progress-bar progress-bar-red" style="width:<?= $progress ?>%"></div>
        </div>
    </div>
</div>
<div class="row shuju">
    <div class="col-xs-9" style="padding: 0;padding-left: 30px">
        <span><?= $respData['isClosed'] ? 0 : StringUtils::amountFormat2(bcdiv(bcsub($respData['amount'], $respData['tradedAmount']), 100, 2)) ?>元</span><i>/<?= StringUtils::amountFormat1('{amount}{unit}', bcdiv($respData['amount'], 100, 2)) ?></i>
        <div>可投余额/转让金额</div>
    </div>
    <div class="col-xs-3" style="padding: 0;padding-right: 30px">
        <div class="shuju-bili"><?= $progress ?><em>%</em></div>
    </div>
</div>
<div class="row message">
    <div class="col-xs-12 xian2">
        <div class="m1">折让率：<span><?= StringUtils::amountFormat3($respData['discountRate']) ?>%</span></div>
        <div class="m2">转让起息日：<span>购买日次日</span></div>
        <div class="m3">产品到期日：<?= date('Y-m-d', $loan->finish_date) ?></div>
        <?php if ($loan->isNatureRefundMethod()) { ?>
            <div class="m4"><div></div>还款方式：
                <span><?= Yii::$app->params['refund_method'][$loan->refund_method] ?></span>
                <img src="<?= ASSETS_BASE_URI ?>images/credit/tip.png" alt="">
            </div>
            <div class="row" id="chart-box" hidden="true">
                <div class="col-xs-12">
                    <div><img src="<?= ASSETS_BASE_URI ?>images/credit/jiao.png" alt="">付息时间固定日期，按自然月在每个月还款，按自然季度是3、6、9、12月还款，按自然半年是6、12月还款，按自然年是12月还款</div>
                </div>
            </div>
        <?php } else { ?>
            <div class="m4"><div></div>还款方式：
                <span><?= Yii::$app->params['refund_method'][$loan->refund_method] ?></span>
            </div>
        <?php } ?>
        <?php if (!empty($loan->kuanxianqi)) { ?>
            <div style="padding-right: 12px;">融资方可提前<?= $loan->kuanxianqi ?>天内任一天还款，客户收益按实际天数计息。</div>
        <?php } ?>
    </div>
</div>
<div class="row link-box">
    <a class="link-en" href="/deal/deal/detail?sn=<?= $loan->sn ?>">
        <div class="header-box">
            <span class="header-icon"><img src="<?= ASSETS_BASE_URI ?>images/credit/xiang1.png"></span>
            <span class="header-name">查看原项目</span>
            <span class="header-you"><img src="<?= ASSETS_BASE_URI ?>images/credit/youbiao.png"></span>
        </div>
    </a>
    <a class="link-en" href="/credit/note/orders?id=<?= $respData['id'] ?>">
        <div class="header-box">
            <span class="header-icon"><img src="<?= ASSETS_BASE_URI ?>images/credit/xiang2.png"></span>
            <span class="header-name">转让记录</span>
            <span class="header-you"><img src="<?= ASSETS_BASE_URI ?>images/credit/youbiao.png"></span>
        </div>
    </a>
    <a class="link-en" href="">
        <div class="header-box">
            <span class="header-icon"><img src="<?= ASSETS_BASE_URI ?>images/credit/xiang3.png"></span>
            <span class="header-name">转让规则</span>
            <span class="header-you"><img src="<?= ASSETS_BASE_URI ?>images/credit/youbiao.png"></span>
        </div>
    </a>
</div>
<div id="x-purchase" class="row rengou" style="cursor: pointer">
    <div class="col-xs-12">
        <?php if (!$respData['isClosed']) { ?>
            <a href="">立即认购</a>
        <?php } else { ?>
            <a href="javascript:;" class="red-gray">转让完成</a>
        <?php } ?>
    </div>
</div>