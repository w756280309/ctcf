<?php
$this->title = '我的理财';

$this->registerCssFile(ASSETS_BASE_URI.'css/useraccount/mytrade.css?v=20160804', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerCssFile(ASSETS_BASE_URI.'css/pagination.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/useraccount/my_trade.js', ['depends' => 'frontend\assets\FrontAsset']);

use common\utils\StringUtils;
use common\widgets\Pager;
use common\models\order\OnlineRepaymentPlan;
?>

<div class="myCoupon-box">
    <div class="myCoupon-header">
        <div class="myCoupon-header-icon"></div>
        <span class="myCoupon-header-font">我的理财</span>
    </div>
    <div class="myCoupon-content">
        <div class="list-single">
            <a class="a_first <?= 1 === $type ? 'select' : '' ?>" href="/user/user/myorder">收益中的项目</a>
            <a class="a_second <?= 2 === $type ? 'select' : '' ?>" href="/user/user/myorder?type=2">待成立的项目</a>
            <a class="a_third <?= 3 === $type ? 'select' : '' ?>" href="/user/user/myorder?type=3">已还清的项目</a>
        </div>
        <?php if (in_array($type, [1, 3])) { ?>
        <?php if (1 === $type) { ?>
            <div class="display_number">
                <p class="p_left">待回款总金额：<span><?= StringUtils::amountFormat3($stats['unpaidAount']) ?></span>元</p>
                <p class="p_left p_left_mg">已回款总金额：<span><?= StringUtils::amountFormat3($stats['repaidAount']) ?></span>元</p>
                <p class="p_right">共计：<span><?= $tj['count'] ?></span>笔</p>
            </div>
        <?php } else { ?>
            <div class="display_number">
                <p class="p_left">已还清总金额：<span><?= StringUtils::amountFormat3($stats['repaidAount']) ?></span>元</p>
                <p class="p_right">共计：<span><?= $tj['count'] ?></span>笔</p>
            </div>
        <?php } ?>
        <table>
            <?php if (1 === $type) { ?>
                <tr>
                    <th class="text-first" width="230">项目名称</th>
                    <th class="text-align-lf" width="100">到期时间</th>
                    <th class="text-align-ct" width="70">预期年化</th>
                    <th class="text-align-rg" width="110">投资金额(元)</th>
                    <th class="text-align-rg" width="100">预期收益(元)</th>
                    <th class="text-align-ct" width="90">还款计划</th>
                    <th class="text-align-ct" width="60">合同</th>
                </tr>
            <?php } else { ?>
                <tr>
                    <th class="text-first" width="230">项目名称</th>
                    <th class="text-align-lf" width="100">还款时间</th>
                    <th class="text-align-ct" width="70">预期年化</th>
                    <th class="text-align-rg" width="110">投资金额(元)</th>
                    <th class="text-align-rg" width="100">实际收益(元)</th>
                    <th class="text-align-ct" width="100">还款计划</th>
                    <th class="text-align-ct" width="50">合同</th>
                </tr>
            <?php } ?>

            <?php foreach ($model as $key => $val) : ?>
                <tr class="tr-click">
                    <td class="text-second">
                        <a href="<?= empty($val['note_id']) ? '/deal/deal/detail?sn='.$val['loan']['sn'] : '/credit/note/detail?id='.$val['note_id'] ?>">
                            <?= (empty($val['note_id']) ? '' : '【转让】').$val['loan']['title'] ?>
                        </a>
                    </td>
                    <td class="text-align-lf"><?= $val['order'] ? $val['order']->getLastPaymentDate() : '' ?></td>
                    <td class="text-align-ct"><?= StringUtils::amountFormat2($val['order']->yield_rate * 100) ?>%</td>
                    <td class="text-align-rg"><?= StringUtils::amountFormat3(bcdiv($val['amount'], 100, 2)) ?></td>
                    <td class="text-align-rg"><?= StringUtils::amountFormat3($val['order'] ? $val['order']->getProceeds() : 0) ?></td>
                    <td class="text-align-ct">
                        <span class="tip-cursor">
                            <span class="tip-font"><?= $plan[$key]['yihuan'] ?>/<?= count($plan[$key]['obj']) ?></span>
                            <span class="tip-icon-enna tip-icon-top"></span>
                        </span>
                    </td>
                    <td class="text-align-ct">
                        <a href="<?= empty($val['asset_id']) ? '/order/order/agreement?pid='.$val['loan']['id'].'&order_id='.$val['order_id'] : '' ?>" target="_blank">查看</a>
                    </td>
                </tr>
                <?php if (!empty($plan[$key]['obj'])) : ?>
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
                                    <?php foreach ($plan[$key]['obj'] as $key => $val) : ?>
                                        <tr>
                                            <td class="text-inner-first"><?= $key + 1 ?></td>
                                            <td class="text-align-lf"><?= $val->refund_time ? date('Y-m-d', $val->refund_time) : '' ?></td>
                                            <td class="text-align-rg"><?= StringUtils::amountFormat3($val->benjin) ?></td>
                                            <td class="text-inner-second"><?= StringUtils::amountFormat3($val->lixi) ?></td>
                                            <td class="text-align-ct"><?= in_array($val->status, [OnlineRepaymentPlan::STATUS_YIHUAN, OnlineRepaymentPlan::STATUS_TIQIAM]) ? '已还' : '未还' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>

        <?php if (empty($model)) : ?>
            <div class="no-data"></div>
            <div class="no-data"></div>
            <p class="without-font">暂无投资明细</p>
            <a class="link-tender" href="/licai/">立即投资</a>
        <?php endif; ?>
        <?php } elseif (2 === $type) { ?>
        <div class="display_number">
            <p class="p_left">待成立项目总金额：<span><?= StringUtils::amountFormat3($tj['totalAmount']) ?></span>元</p>
            <p class="p_right">共计：<span><?= $tj['count'] ?></span>笔</p>
        </div>
        <table>
            <tr>
                <th class="text-first" width="240">项目名称</th>
                <th class="text-align-ct" width="110">项目期限</th>
                <th class="text-align-ct" width="120">预期年化</th>
                <th class="text-third" width="120">投资金额(元)</th>
                <th class="text-align-ct" width="">募集进度</th>
            </tr>
            <?php foreach ($model as $val) : ?>
                <tr class="tr-click">
                    <td class="text-second"><a href="/deal/deal/detail?sn=<?= $val->loan->sn ?>"><?= $val->loan->title ?></a></td>
                    <td class="text-align-ct">
                        <?php $ex = $val->loan->getDuration() ?><?= $ex['value'] ?><?= $ex['unit']?>
                    </td>
                    <td class="text-align-ct"><?= rtrim(rtrim(number_format($val->yield_rate * 100, 2), '0'), '.') ?>%</td>
                    <td class="text-third"><?= StringUtils::amountFormat3($val->order_money) ?></td>
                    <td class="text-align-ct">
                        <?= $val->loan->getProgressForDisplay()?>%
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php if (empty($model)) : ?>
            <div class="no-data"></div>
            <div class="no-data"></div>
            <p class="without-font">暂无投资明细</p>
            <a class="link-tender" href="/licai/">立即投资</a>
        <?php endif; ?>
        <?php } ?>
    </div>
    <center><?= Pager::widget(['pagination' => $pages]); ?></center>
</div>
