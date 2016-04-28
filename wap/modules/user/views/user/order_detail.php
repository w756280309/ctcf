<?php
$this->title = '投资详情';

use common\models\product\RateSteps;

$this->registerJsFile(ASSETS_BASE_URI .'js/fastclick.js', ['position' => 1, ]);
$this->registerJsFile(ASSETS_BASE_URI .'js/touzixiangqing.js', ['depends' => 'yii\web\JqueryAsset', 'position' => 1]);
$this->registerCssFile(ASSETS_BASE_URI .'css/touzixiangqing.css?v=20160427', ['depends' => 'frontend\assets\WapAsset']);
?>

<div class="container">
    <!--invest-title-->
    <div class="row" id="invest-box">
        <div class="col-xs-12">
            <div id="invest-title">
                <div class="invest-title">
                    <div class="invest-left"><?= $product->title ?></div>
                    <div class="invest-right">
                        <?php if (5 === $product->status) { ?>
                        <!--还款中-已还清-募集中-文字颜色-->
                        <div class="invest-right-title invest-orange"><?= Yii::$app->params['deal_status'][$product->status] ?></div>
                        <!--还款中-已还清-募集中-图片-->
                        <img src="<?= ASSETS_BASE_URI ?>images/licai-huang.png" alt="">
                        <?php } elseif (6 === $product->status) { ?>
                        <!--还款中-已还清-募集中-文字颜色-->
                        <div class="invest-right-title invest-gray"><?= Yii::$app->params['deal_status'][$product->status] ?></div>
                        <!--还款中-已还清-募集中-图片-->
                        <img src="<?= ASSETS_BASE_URI ?>images/licai-hui.png" alt="">
                        <?php } else { ?>
                        <!--还款中-已还清-募集中-文字颜色-->
                        <div class="invest-right-title invest-red"><?= Yii::$app->params['deal_status'][$product->status] ?></div>
                        <!--还款中-已还清-募集中-图片-->
                        <img src="<?= ASSETS_BASE_URI ?>images/licai-jian.png" alt="">
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--投资信息-->
    <div class="row" id="information-box">
        <div class="col-xs-12">
            <div class="information-title">
                <div class="information-left">投资信息</div>
                <div class="information-right"><img src="<?= ASSETS_BASE_URI ?>images/arrowShang.png" alt=""></div>
            </div>
        </div>
    </div>
    <!--投资信息详情-->
    <div class="row" id="information-content">
        <div class="col-xs-12">
            <ul class="information-content">
                <?php if ($product->isFlexRate) {
                    $rate = RateSteps::getRateForAmount(RateSteps::parse($product->rateSteps), $totalFund);
                    if (false !== $rate) {
                ?>
                <li>
                    <div class="rate-steps-notes">因为该项目的累计投资金额已达<?= rtrim(rtrim(number_format($totalFund, 2), '0'), '.') ?>元，本订单享受年化率<?= rtrim(rtrim(number_format($rate, 2), '0'), '.') ?>%</div>
                </li>
                <?php } } ?>
                <?php if (in_array($product->status, [2, 3, 7])) { ?>
                <li>
                    <div class="information-content-left">本金</div>
                    <div class="information-content-right"><?= rtrim(rtrim(number_format($deal->order_money, 2), '0'), '.') ?>元</div>
                </li>
                <li>
                    <div class="information-content-left">募集进度</div>
                    <div class="information-content-right"><?= number_format($product->finish_rate*100, 0) ?>%</div>
                </li>
                <li>
                    <div class="information-content-left">还款方式</div>
                    <div class="information-content-right"><?= Yii::$app->params['refund_method'][$deal->refund_method] ?></div>
                </li>
                <?php } elseif (5 === $product->status) { ?>
                <li>
                    <div class="information-content-left">本金</div>
                    <div class="information-content-right"><?= rtrim(rtrim(number_format($deal->order_money, 2), '0'), '.') ?>元</div>
                </li>
                <li>
                    <div class="information-content-left">预期收益</div>
                    <div class="information-content-right"><?= rtrim(rtrim(number_format($profit, 2), '0'), '.') ?>元</div>
                </li>
                <li>
                    <div class="information-content-left">还款方式</div>
                    <div class="information-content-right"><?= Yii::$app->params['refund_method'][$deal->refund_method] ?></div>
                </li>
                <?php if ($plan && !$hkDate) { ?>
                <li>
                    <div class="information-content-left">下次还款</div>
                    <div class="information-content-right"><?= date('Y.m.d', $hkDate) ?></div>
                </li>
                <?php } ?>
                <?php } else { ?>
                <li>
                    <div class="information-content-left">本金</div>
                    <div class="information-content-right"><?= rtrim(rtrim(number_format($deal->order_money, 2), '0'), '.') ?>元</div>
                </li>
                <li>
                    <div class="information-content-left">预期收益</div>
                    <div class="information-content-right"><?= rtrim(rtrim(number_format($profit, 2), '0'), '.') ?>元</div>
                </li>
                <li>
                    <div class="information-content-left">还款方式</div>
                    <div class="information-content-right"><?= Yii::$app->params['refund_method'][$deal->refund_method] ?></div>
                </li>
                <li>
                    <div class="information-content-left">最后还款</div>
                    <div class="information-content-right"><?= date('Y.m.d', $hkDate) ?></div>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <?php if (!in_array($product->status, [2, 3, 7])) { ?>
    <?php if ($plan) { ?>
    <!--还款计划-->
    <div class="row" id="repayment-box">
        <div class="col-xs-12">
            <div class="repayment-title">
                <div class="repayment-left">还款计划</div>
                <div class="repayment-right"><img src="<?= ASSETS_BASE_URI ?>images/arrowShang.png" alt=""></div>
            </div>
        </div>
    </div>
    <!--还款计划详情-->
    <div class="row" id="repayment-content">
        <div class="col-xs-12">
            <ul class="repayment-content">
                <?php foreach($plan as $val): ?>
                <?php if (0 !== (int) $val['benjin']) { ?>
                <li>
                    <div>第<?= $val['qishu'] ?>期</div>
                    <div><?= date('Y.m.d', $val['refund_time']) ?></div>
                    <div>本金</div>
                    <div><?= rtrim(rtrim(number_format($val['benjin'], 2), '0'), '.') ?>元</div>
                    <!--还款计划-文字颜色-->
                    <p class="<?= (1 === $val['status']) ? 'repayment-green' : 'repayment-red' ?>"><?= (1 === $val['status']) ? '已还' : '未还' ?></p>
                </li>
                <?php } ?>
                <li>
                    <div>第<?= $val['qishu'] ?>期</div>
                    <div><?= date('Y.m.d', $val['refund_time']) ?></div>
                    <div>利息</div>
                    <div><?= rtrim(rtrim(number_format($val['lixi'], 2), '0'), '.') ?>元</div>
                    <!--还款计划-文字颜色-->
                    <p class="<?= (1 === $val['status']) ? 'repayment-green' : 'repayment-red' ?>"><?= (1 === $val['status']) ? '已还' : '未还' ?></p>
                </li>
                <?php endforeach; ?>
                <!--下拉按钮-->
                <div class="repayment-down"><img src="<?= ASSETS_BASE_URI ?>images/arrowShang.png" alt=""></div>
            </ul>
        </div>
    </div>
    <?php } ?>
    <!--认购合同-->
    <div class="row" id="subscription-box">
        <div class="col-xs-12">
            <a href="/order/order/agreement?id=<?= $product->id ?>&deal_id=<?= $deal->id ?>" class="subscription-title">
                <div class="subscription-left">认购合同</div>
                <div class="subscription-right"><img src="<?= ASSETS_BASE_URI ?>images/arrowShang.png" alt=""></div>
            </a>
        </div>
    </div>
    <?php } ?>
</div>