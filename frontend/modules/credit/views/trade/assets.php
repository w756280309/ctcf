<?php

$this->title = '我的转让';

use common\models\order\OnlineRepaymentPlan as Plan;
use common\utils\StringUtils;
use common\widgets\Pager;
use frontend\assets\FrontAsset;
use yii\web\JqueryAsset;

$this->registerCssFile(ASSETS_BASE_URI.'css/useraccount/usercenter.css', ['depends' => FrontAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/useraccount/transfering.css?v=160926', ['depends' => FrontAsset::class]);
$this->registerCssFile(ASSETS_BASE_URI.'css/pagination.css');
$this->registerJsFile(ASSETS_BASE_URI.'js/useraccount/transfering.js?v=160927', ['depends' => JqueryAsset::class]);
?>

<div class="wdjf-body">
    <div class="wdjf-ucenter clearfix">
        <div class="leftmenu">
            <?= $this->renderFile('@frontend/views/left.php') ?>
        </div>
        <div class="rightcontent">
            <div class="myCoupon-box">
                <div class="myCoupon-header">
                    <div class="myCoupon-header-icon"></div>
                    <span class="myCoupon-header-font">我的转让</span>
                </div>
                <div class="myCoupon-content">
                    <div class="list-single">
                        <a class="a_first <?= 1 === $type ? 'select' : '' ?>" href="/credit/trade/assets">可转让的项目</a>
                        <a class="a_second <?= 2 === $type ? 'select' : '' ?>" href="/credit/trade/assets?type=2">转让中的项目</a>
                        <a class="a_third <?= 3 === $type ? 'select' : '' ?>" href="/credit/trade/assets?type=3">已转让的项目</a>
                    </div>
                    <?php if (1 === $type) { ?>
                        <div class="display_number">
                            <p class="p_left">可转让金额：<span><?= StringUtils::amountFormat3(bcdiv($creditAmount, 100, 2)) ?></span>元</p>
                            <p class="p_right">总计：<span><?= $totalCount ?></span>笔</p>
                        </div>
                        <table>
                            <tr>
                                <th class="text-first" width="120">项目名称</th>
                                <th class="text-align-ct" width="100">剩余期限</th>
                                <th class="text-third" width="110">预期年化</th>
                                <th class="text-third" width="130">可转让金额(元)</th>
                                <th class="text-align-ct" width="110">还款计划</th>
                                <th class="text-third" width="60">合同</th>
                                <th class="text-align-ct" width="60">操作</th>
                            </tr>
                            <?php foreach ($assets as $asset) { ?>
                                <tr class="tr-click">
                                    <td class="text-second"><a href="/deal/deal/detail?sn=<?= $asset['loan']->sn ?>"><?= $asset['loan']->title ?></a></td>
                                    <td class="text-align-ct">
                                        <?php
                                            $remainingDuration = $asset['loan']->remainingDuration;

                                            echo (isset($remainingDuration['months']) ? $remainingDuration['months'].'个月'
                                                : '').$remainingDuration['days'].'天';
                                        ?>
                                    </td>
                                    <td class="text-align-ct"><?= StringUtils::amountFormat2($asset['order']->yield_rate * 100) ?>%</td>
                                    <td class="text-third"><?= StringUtils::amountFormat3(bcdiv($asset['maxTradableAmount'], 100, 2)) ?></td>
                                    <td class="text-align-ct">
                                        <span class="tip-cursor">
                                        <span class="tip-font">
                                            <?php
                                                $tj = array_count_values(array_column($asset['plan'], 'status'));
                                                $weihuan = isset($tj[Plan::STATUS_WEIHUAN]) ? $tj[Plan::STATUS_WEIHUAN] : 0;
                                                $count = count($asset['plan']);

                                                echo ($count - $weihuan).'/'.$count
                                            ?>
                                        </span>&nbsp;
                                        <span class="tip-icon-enna tip-icon-top"></span>
                                        </span>
                                    </td>
                                    <td class="text-third">
                                        <a href="/order/order/agreement?pid=<?= $asset['loan_id'] ?>&order_id=<?= $asset['order_id'] ?>" target="_blank">查看</a>
                                    </td>
                                    <td class="text-align-ct"><a class="color-blue" href="/credit/note/new?asset_id=<?= $asset['id'] ?>" target="_blank">转让</a></td>
                                </tr>
                                <!--下拉显示信息-->
                                <tr class="tr-show">
                                    <td colspan="7">
                                        <div class="inner-box">
                                            <div class="tip-border-en">
                                                <div class="tip-border-inner">
                                                    <div class="border-demo"></div>
                                                </div>
                                            </div>
                                            <table>
                                                <tbody>
                                                <tr>
                                                    <th class="text-inner-first" width="90">期数</th>
                                                    <th class="text-align-lf" width="120">还款时间</th>
                                                    <th class="text-align-rg" width="130">还款本金(元)</th>
                                                    <th class="text-inner-second" width="180">还款利息(元)</th>
                                                    <th class="text-align-ct">还款状态</th>
                                                </tr>
                                                <?php foreach ($asset['plan'] as $plan) { ?>
                                                    <tr>
                                                        <td class="text-inner-first"><?= $plan['qishu'] ?></td>
                                                        <td class="text-align-lf"><?= date('Y-m-d', $plan['refund_time']) ?></td>
                                                        <td class="text-align-rg"><?= StringUtils::amountFormat3($plan['benjin']) ?></td>
                                                        <td class="text-inner-second"><?= StringUtils::amountFormat3($plan['lixi']) ?></td>
                                                        <td class="text-align-ct"><?= in_array($plan['status'], [Plan::STATUS_YIHUAN, Plan::STATUS_TIQIAM]) ? '已还' : '未还' ?></td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                        <?php if (empty($assets)) { ?>
                            <div class="table-kong"></div>
                            <div class="table-kong"></div>
                            <p class="without-font">暂无可转让的项目</p>
                            <a class="link-tender" href="/licai/">立即投资</a>
                        <?php } else { ?>
                            <center><?= Pager::widget(['pagination' => $pages]); ?></center>
                        <?php } ?>
                    <?php } elseif (2 === $type) { ?>
                        <div class="display_number">
                            <p class="p_left">待转让金额：<span><?= StringUtils::amountFormat3(bcdiv($tradingTotalAmount, 100, 2)) ?></span>元</p>
                            <p class="p_left p_left_mg">已转让金额：<span><?= StringUtils::amountFormat3(bcdiv($tradedTotalAmount, 100, 2)) ?></span>元</p>
                            <p class="p_right">总计：<span><?= $totalCount ?></span>笔</p>
                        </div>
                        <table>
                            <tr>
                                <th class="text-first" width="226">项目名称</th>
                                <th class="text-align-ct" width="80">转让日期</th>
                                <th class="text-third" width="90">转让金额(元)</th>
                                <th class="text-align-ct" width="90">折让率(%)</th>
                                <th class="text-third" width="100">已转让金额(元)</th>
                                <th class="text-align-ct" width="60">操作</th>
                            </tr>
                            <?php foreach ($notes as $note) { ?>
                                <tr class="tr-click">
                                    <td class="text-second"><a href="/credit/note/detail?id=<?= $note['id'] ?>"><?= $note['loan']->title ?></a></td>
                                    <td class="text-align-ct"><?= substr($note['createTime'], 0, 10) ?></td>
                                    <td class="text-third"><?= StringUtils::amountFormat3(bcdiv($note['amount'], 100, 2)) ?></td>
                                    <td class="text-align-ct"><?= $note['discountRate'] ?></td>
                                    <td class="text-third"><?= StringUtils::amountFormat3(bcdiv($asset['tradedAmount'], 100, 2)) ?></td>
                                    <td class="text-align-ct"><a class="color-blue cancel-note" href="javascript:void(0)" note-id="<?= $note['id'] ?>">撤销</a></td>
                                </tr>
                            <?php } ?>
                        </table>
                        <?php if (empty($notes)) { ?>
                            <div class="table-kong"></div>
                            <div class="table-kong"></div>
                            <p class="without-font">暂无转让中的项目</p>
                            <a class="link-tender" href="/licai/">立即投资</a>
                        <?php } else { ?>
                            <center><?= Pager::widget(['pagination' => $pages]); ?></center>
                        <?php } ?>
                    <?php } elseif (3 === $type) { ?>
                        <div class="display_number">
                            <p class="p_left">已转让金额：<span><?= StringUtils::amountFormat3(bcdiv($tradedTotalAmount, 100, 2)) ?></span>元</p>
                            <p class="p_right">总计：<span><?= $totalCount ?></span>笔</p>
                        </div>
                        <table>
                            <tr>
                                <th class="text-first" width="226">项目名称</th>
                                <th class="text-align-ct" width="80">完成日期</th>
                                <th class="text-third" width="100">已转让金额(元)</th>
                                <th class="text-align-ct" width="90">折让率(%)</th>
                                <th class="text-third" width="80">手续费(元)</th>
                                <th class="text-third" width="90">实际收入(元)</th>
                                <th class="text-align-ct" width="60">合同</th>
                            </tr>
                            <?php foreach ($notes as $note) { ?>
                                <tr class="tr-click">
                                    <td class="text-second"><a href="/credit/note/detail?id=<?= $note['id'] ?>"><?= $note['loan']->title ?></a></td>
                                    <td class="text-align-ct"><?= substr($note['closeTime'], 0, 10) ?></td>
                                    <td class="text-third"><?= StringUtils::amountFormat3(bcdiv($note['tradedAmount'], 100, 2)) ?></td>
                                    <td class="text-align-ct"><?= $note['discountRate'] ?></td>
                                    <td class="text-third">
                                        <?php
                                            $config = json_decode($note['config'], true);
                                            $fee = bcmul($config['fee_rate'], $note['amount']);
                                            StringUtils::amountFormat3(bcdiv($fee, 100, 2));
                                        ?>
                                    </td>
                                    <td class="text-third"><?= StringUtils::amountFormat3(bcdiv(bcsub($note['tradedAmount'], $fee), 100, 2)); ?></td>
                                    <td class="text-align-ct"><a class="color-blue" href="">查看</a></td>
                                </tr>
                            <?php } ?>
                        </table>
                        <?php if (empty($notes)) { ?>
                            <div class="table-kong"></div>
                            <div class="table-kong"></div>
                            <p class="without-font">暂无已转让的项目</p>
                            <a class="link-tender" href="/licai/">立即投资</a>
                        <?php } else { ?>
                            <center><?= Pager::widget(['pagination' => $pages]); ?></center>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<!--mask弹框-->
<div class="mask"></div>

<!--确认弹框-->
<div class="confirmBox">
    <div class="confirmBox-title">提示</div>
    <div class="confirmBox-top">
        <p>确认撤销当前转让中的项目？已转让的不能撤回，发起后立即生效。</p>
    </div>
    <div class="confirmBox-bottom">
        <div class="confirmBox-left">关闭</div>
        <div class="confirmBox-right">确认</div>
    </div>
</div>
