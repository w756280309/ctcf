<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-1-3
 * Time: 下午1:51
 */
$this->title = '门店理财';

$this->registerCssFile(ASSETS_BASE_URI.'css/useraccount/mytrade.css?v=20160804', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerCssFile(ASSETS_BASE_URI.'css/pagination.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/useraccount/my_trade.js', ['depends' => 'frontend\assets\FrontAsset']);

use common\utils\StringUtils;
use common\widgets\Pager;
use common\models\order\OnlineRepaymentPlan;
use common\models\offline\OfflineRepaymentPlan;
?>
<style>
    table .pl-box-10 {
        padding-left: 10px;
    }
    table .pr-box-10 {
        padding-right: 10px;
    }
</style>
<div class="myCoupon-box">
    <div class="myCoupon-header">
        <div class="myCoupon-header-icon"></div>
        <span class="myCoupon-header-font">门店理财</span>
    </div>
    <div class="myCoupon-content">
        <div class="list-single">
            <a class="a_first <?= 1 === $type ? 'select' : '' ?>" href="/user/user/myofforder">收益中的项目</a>
<!--            <a class="a_second --><?//= 2 === $type ? 'select' : '' ?><!--" href="/user/user/myofforder?type=2">募集中的项目</a>-->
            <a class="a_third <?= 3 === $type ? 'select' : '' ?>" href="/user/user/myofforder?type=3">已还清的项目</a>
        </div>
        <div class="display_number">
            <p class="p_right">共计：<span><?= $count ?></span>笔</p>
        </div>
        <table>
            <tr>
                <?php if (1 === $type) : ?>
                <th class="text-align-lf pl-box-10" width="220">项目名称</th>
                <th class="text-align-rg" width="100">到期时间</th>
                <th class="text-align-rg" width="80">预期年化</th>
                <th class="text-align-rg" width="110">投资金额(元)</th>
                <th class="text-align-rg" width="110">购买时间</th>
                <th class="text-align-rg" width="100">预期收益(元)</th>
                <th class="text-align-rg" width="90">还款计划</th>
                <?php endif; ?>
                <?php if (2 === $type) : ?>
                    <th class="text-align-lf pl-box-10" width="">项目名称</th>
                    <th class="text-align-rg" width="">项目期限</th>
                    <th class="text-align-rg" width="">预期年化</th>
                    <th class="text-align-rg" width="">投资金额(元)</th>
                    <th class="text-align-rg" width="">购买时间</th>
                <?php endif; ?>
                <?php if (3 === $type) : ?>
                    <th class="text-align-lf pl-box-10" width="">项目名称</th>
                    <th class="text-align-rg" width="">还款时间</th>
                    <th class="text-align-rg" width="">预期年化</th>
                    <th class="text-align-rg" width="">投资金额(元)</th>
                    <th class="text-align-rg" width="">购买时间</th>
                    <th class="text-align-rg" width="">实际收益(元)</th>
                    <th class="text-align-rg" width="">还款计划</th>
                <?php endif; ?>
            </tr>
            <?php foreach ($models as $key => $model) : ?>
                <tr class="tr-click">
                <?php if (1 === $type) : ?>
                    <td><?= $model->loan->title ?></td>
                    <td class="text-align-rg"><?= date('Y-m-d', strtotime($model->loan->finish_date)) ?></td>
                    <td class="text-align-rg"><?= bcmul($model->apr, 100, 2) . '%' ?></td>
                    <td class="text-align-rg"><?= bcmul($model->money, 10000, 2) ?></td>
                    <td class="text-align-rg"><?= $model->orderDate ?></td>
                    <td class="text-align-rg"><?= OfflineRepaymentPlan::find()->where(['order_id' => $model->id])->sum('lixi'); ?></td>
                    <td class="text-align-rg">
                        <span class="tip-cursor" style="margin:0 0 0 auto">
                            <span class="tip-font"><?= OfflineRepaymentPlan::find()->where(['order_id' => $model->id])->andWhere(['in', 'status', ['1', '2']])->count() ?>/<?= OfflineRepaymentPlan::find()->where(['order_id' => $model->id])->count() ?></span>
                            <span class="tip-icon-enna tip-icon-top"></span>
                        </span>
                    </td>
                <?php endif; ?>
                    <?php if (2 === $type) : ?>
                        <td><?= $model->loan->title ?></td>
                        <td class="text-align-rg"><?= $model->loan->expires . $model->loan->unit ?></td>
                        <td class="text-align-rg"><?= bcmul($model->apr, 100, 2) . '%' ?></td>
                        <td class="text-align-rg"><?= bcmul($model->money, 10000, 2) ?></td>
                        <td class="text-align-rg"><?= $model->orderDate ?></td>
                        <td class="text-align-rg"><?= OfflineRepaymentPlan::find()->where(['order_id' => $model->id])->sum('lixi'); ?></td>
                    <?php endif; ?>
                    <?php if (3 === $type) : ?>
                        <td><?= $model->loan->title ?></td>
                        <td class="text-align-rg"><?= date('Y-m-d', strtotime($model->loan->finish_date)) ?></td>
                        <td class="text-align-rg"><?= bcmul($model->apr, 100, 2) . '%' ?></td>
                        <td class="text-align-rg"><?= bcmul($model->money, 10000, 2) ?></td>
                        <td class="text-align-rg"><?= $model->orderDate ?></td>
                        <td class="text-align-rg"><?= OfflineRepaymentPlan::find()->where(['order_id' => $model->id])->sum('lixi'); ?></td>
                        <td class="text-align-rg">
                        <span class="tip-cursor" style="margin:0 0 0 auto">
                            <span class="tip-font"><?= OfflineRepaymentPlan::find()->where(['order_id' => $model->id])->andWhere(['in', 'status', ['1', '2']])->count() ?>/<?= OfflineRepaymentPlan::find()->where(['order_id' => $model->id])->count() ?></span>
                            <span class="tip-icon-enna tip-icon-top"></span>
                        </span>
                        </td>
                    <?php endif; ?>
                </tr>
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
                                <?php
                                    $plans = OfflineRepaymentPlan::find()->where(['order_id' => $model->id])->all();
                                    foreach ($plans as $k => $val){
                                ?>
                                    <tr>
                                        <td class="text-inner-first"><?= $val->qishu ?></td>
                                        <td class="text-align-lf"><?= $val->refund_time ?></td>
                                        <td class="text-align-rg"><?= StringUtils::amountFormat3($val->benjin) ?></td>
                                        <td class="text-inner-second"><?= StringUtils::amountFormat3($val->lixi) ?></td>
                                        <td class="text-align-ct"><?= $val->repaymentStatus ? '已还' : '未还' ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php if (empty($models)) : ?>
            <div class="no-data"></div>
            <div class="no-data"></div>
            <p class="without-font">暂无投资明细</p>
        <?php endif; ?>
    </div>
    <center><?= Pager::widget(['pagination' => $pages]); ?></center>
</div>
