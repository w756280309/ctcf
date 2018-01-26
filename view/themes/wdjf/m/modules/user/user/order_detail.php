<?php
$this->title = '投资详情';

if ($fromTransfer) {
    $this->backUrl = '/credit/trade/assets?type=1';
}

use common\models\product\RateSteps;
use common\models\order\OnlineRepaymentPlan;
use common\utils\StringUtils;
use wap\assets\WapAsset;
use yii\helpers\ArrayHelper;
use yii\web\JqueryAsset;

$this->registerJsFile(ASSETS_BASE_URI .'js/fastclick.js', ['position' => 1]);
$this->registerJsFile(ASSETS_BASE_URI .'js/touzixiangqing.js?v=20170306', ['depends' => JqueryAsset::class, 'position' => 1]);
$this->registerCssFile(ASSETS_BASE_URI .'css/touzixiangqing.css?v=20180102', ['depends' => WapAsset::class]);
?>

<div class="container">
    <!--invest-title-->
    <div class="row" id="invest-box">
        <div class="col-xs-12">
            <div id="invest-title">
                <div class="invest-title">
                    <?php
                        $isNote = isset($asset['note_id']);
                        if ($isNote) {
                            $url = '/credit/note/detail?id='.$asset['note_id'].'&fromType=4';
                        } else {
                            $url = '/deal/deal/detail?sn='.$product->sn;
                        }
                    ?>
                    <div class="invest-left"><a href="<?= $url ?>"><?= $isNote ? '【转让】' : '' ?><?= $product->title ?></a></div>
                    <div class="invest-right">
                        <?php if ($fromTransfer && $asset) { ?>
                            <a href='/credit/note/new?asset_id=<?= $asset['id'] ?>' class="credit-right-title credit-red">转让</a>
                        <?php } else { ?>
                            <?php if (5 === $product->status || ($product->is_jixi && in_array($product->status, ['3', '7']))) { ?>
                            <!--还款中-已还清-募集中-文字颜色-->
                            <div class="invest-right-title invest-orange"><?php
                                if ($product->status == 5 || ($product->is_jixi && in_array($product->status, ['3', '7']))) {
                                    echo '收益中';
                                } else {
                                    echo Yii::$app->params['deal_status'][$product->status];
                                }
                                ?></div>
                            <!--还款中-已还清-募集中-图片-->
                            <img src="<?= ASSETS_BASE_URI ?>images/licai-huang.png" alt="">
                            <?php } elseif (6 === $product->status) { ?>
                            <!--还款中-已还清-募集中-文字颜色-->
                            <div class="invest-right-title invest-gray"><?php
                                if ($product->status == 5 || ($product->is_jixi && in_array($product->status, ['3', '7']))) {
                                    echo '收益中';
                                } else {
                                    echo Yii::$app->params['deal_status'][$product->status];
                                }
                                ?></div>
                            <!--还款中-已还清-募集中-图片-->
                            <img src="<?= ASSETS_BASE_URI ?>images/licai-hui.png" alt="">
                            <?php } else { ?>
                            <!--还款中-已还清-募集中-文字颜色-->
                            <div class="invest-right-title invest-red"><?php
                                if ($product->status == 5 || ($product->is_jixi && in_array($product->status, ['3', '7']))) {
                                    echo '收益中';
                                } else {
                                    echo Yii::$app->params['deal_status'][$product->status];
                                }
                                ?></div>
                            <!--还款中-已还清-募集中-图片-->
                            <img src="<?= ASSETS_BASE_URI ?>images/licai-jian.png" alt="">
                            <?php } ?>
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
                    <div class="rate-steps-notes">因为该项目的累计投资金额已达<?= StringUtils::amountFormat2($totalFund) ?>元，本订单享受年化率<?= StringUtils::amountFormat2($rate) ?>%</div>
                </li>
                <?php } } ?>
                <?php if (in_array($product->status, [2, 3, 7]) && !$product->is_jixi) { ?>
                <li>
                    <div class="information-content-left">本金</div>
                    <div class="information-content-right"><?= StringUtils::amountFormat2($deal->order_money) ?>元</div>
                </li>
                <li>
                    <div class="information-content-left">认购日期</div>
                    <div class="information-content-right"><?= (null !== $deal->created_at) ? date('Y-m-d', $deal->created_at) : '---' ?></div>
                </li>
                <li>
                    <div class="information-content-left">募集进度</div>
                    <div class="information-content-right"><?= $product->getProgressForDisplay()?>%</div>
                </li>
                <li>
                    <div class="information-content-left">还款方式</div>
                    <div class="information-content-right"><?= Yii::$app->params['refund_method'][$deal->refund_method] ?></div>
                </li>
                <?php } elseif (5 === $product->status || ($product->is_jixi && in_array($product->status, ['3', '7']))) { ?>
                <li>
                    <div class="information-content-left">本金</div>
                    <div class="information-content-right"><?= StringUtils::amountFormat2(bcdiv($asset['amount'], 100 , 2)) ?>元</div>
                </li>
                <li>
                    <div class="information-content-left">认购日期</div>
                    <div class="information-content-right"><?= (null !== $deal->created_at) ? date('Y-m-d', $deal->created_at) : '---' ?></div>
                </li>
                <li>
                    <div class="information-content-left">起息日期</div>
                    <div class="information-content-right"><?= (null !== $product->jixi_time) ? date('Y-m-d', $product->jixi_time) : '---' ?></div>
                </li>
                <li>
                    <div class="information-content-left">预期收益</div>
                    <div class="information-content-right"><?= StringUtils::amountFormat3($profit) ?>元</div>
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
                    <div class="information-content-right"><?= StringUtils::amountFormat2($asset ? bcdiv($asset['amount'], 100 , 2) : $deal->order_money) ?>元</div>
                </li>
                <li>
                    <div class="information-content-left">认购日期</div>
                    <div class="information-content-right"><?= (null !== $deal->created_at) ? date('Y-m-d', $deal->created_at) : '---' ?></div>
                </li>
                <li>
                    <div class="information-content-left">起息日期</div>
                    <div class="information-content-right"><?= (null !== $product->jixi_time) ? date('Y-m-d', $product->jixi_time) : '---' ?></div>
                </li>
                <li>
                    <div class="information-content-left">预期收益</div>
                    <div class="information-content-right"><?= StringUtils::amountFormat3($profit) ?>元</div>
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
                    <?php
                        $terms = ArrayHelper::getColumn($plan, 'qishu');
                        $lastTerm = !empty($terms) ? end($terms) : null;
                    ?>
                    <?php foreach($plan as $key => $val) : ++$key; ?>
                        <?php
                            $hasRepaid = in_array($val['status'], [OnlineRepaymentPlan::STATUS_YIHUAN, OnlineRepaymentPlan::STATUS_TIQIAM]);
                            $requireCalcBonusProfit = $val['qishu'] === $lastTerm && bccomp($bonusProfit, 0, 2) > 0;
                        ?>
                        <?php if ($val['benjin'] > 0) { ?>
                            <li>
                                <div>第<?= $key ?>期</div>
                                <div><?= date('Y.m.d', $val['refund_time']) ?></div>
                                <div>本金</div>
                                <div><?= StringUtils::amountFormat2($val['benjin']) ?>元</div>
                                <!--还款计划-文字颜色-->
                                <p class="<?= $hasRepaid ? 'repayment-green' : 'repayment-red' ?>"><?= $hasRepaid ? '已还' : '未还' ?></p>
                            </li>
                        <?php } ?>
                        <li>
                            <div>第<?= $key ?>期</div>
                            <div><?= date('Y.m.d', $val['refund_time']) ?></div>
                            <div>利息</div>
                            <div>
                                <?php if ($requireCalcBonusProfit) { ?>
                                    <?= StringUtils::amountFormat3(bcsub($val['lixi'], $bonusProfit, 2)) ?>元
                                <?php } else { ?>
                                    <?= StringUtils::amountFormat3($val['lixi']) ?>元
                                <?php } ?>
                            </div>
                            <!--还款计划-文字颜色-->
                            <p class="<?= $hasRepaid ? 'repayment-green' : 'repayment-red' ?>"><?= $hasRepaid ? '已还' : '未还' ?></p>
                        </li>
                        <?php if ($requireCalcBonusProfit) { ?>
                            <li>
                                <div>第<?= $key ?>期</div>
                                <div><?= date('Y.m.d', $val['refund_time']) ?></div>
                                <div>加息收益</div>
                                <div><?= StringUtils::amountFormat3($bonusProfit) ?>元</div>
                                <!--还款计划-文字颜色-->
                                <p class="<?= $hasRepaid ? 'repayment-green' : 'repayment-red' ?>"><?= $hasRepaid ? '已还' : '未还' ?></p>
                            </li>
                        <?php  } ?>
                    <?php endforeach; ?>
                    <!--下拉按钮-->
                    <div class="repayment-down"><img src="<?= ASSETS_BASE_URI ?>images/arrowShang.png" alt=""></div>
                </ul>
            </div>
        </div>
    <?php } ?>
    <?php if (!empty($asset)) { ?>
        <!--认购合同-->
        <div class="row" id="subscription-box">
            <div class="col-xs-12">
                <a href="/order/order/contract?asset_id=<?= $asset['id']?>" class="subscription-title">
                    <div class="subscription-left">认购合同</div>
                    <div class="subscription-right"><img src="<?= ASSETS_BASE_URI ?>images/arrowShang.png" alt=""></div>
                </a>
        </div>
    </div>
    <?php } ?>
</div>
