<?php

use yii\widgets\LinkPager;

$this->title = '线下数据';
$bid = (int)Yii::$app->request->get('bid');

?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                线下数据
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/offline/offline/list">线下数据</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/offline/offline/list">线下数据</a>
                </li>
            </ul>
        </div>
        <!--search start-->
        <div class="portlet-body">
            <form action="/offline/offline/list" method="get" target="_self">
                <table class="table">
                    <tbody>
                    <tr style="text-align: right">
                        <td colspan="7" style="text-align: right"><a href="/offline/offline/add" class="btn blue btn-block" style="width: 100px;display: inline-block;">录入新数据</a></td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <span class="title" style="display: inline-block;line-height:27px;">分销商</span>
                            <select name="bid" style="margin-left:5px;">
                                <option value="">全部</option>
                                <?php foreach($branches as $branch) : ?>
                                    <option value="<?= $branch->id ?>" <?php if($bid === $branch->id){ ?> selected="selected" <?php } ?>><?= $branch->branchName ?></option>
                                <?php endforeach;?>
                            </select>
                        </td>
                        <td>
                            <button class="btn blue btn-block" style="width: 100px;float: right;">
                                查询 <i class="m-icon-swapright m-icon-white"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <div>
                                总交易量：<span><?= $totalmoney*10000 ?></span>元
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>

        <!--search end -->
        <div class="portlet-body">
            <table class="table table-striped table-bordered table-advance table-hover">
                <thead>
                <tr>
                    <th style="text-align: center">序号</th>
                    <th style="text-align: center">分销商</th>
                    <th style="text-align: center">产品名称</th>
                    <th style="text-align: center">客户姓名</th>
                    <th style="text-align: center">联系电话</th>
                    <th style="text-align: center">认购金额（万元）</th>
                    <th style="text-align: center">认购日期</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $key => $order) : ?>
                    <tr>
                        <td style="text-align: center">
                            <?= $order->id ?>
                        </td>
                        <td style="text-align: center">
                            <?= $order->branch->branchName ?>
                        </td>
                        <td style="text-align: center">
                            <?= $order->loan->title ?>
                        </td>
                        <td style="text-align: center">
                            <?= $order->realName ?>
                        </td>
                        <td style="text-align: center">
                            <?= $order->mobile ?>
                        </td>
                        <td style="text-align: center">
                            <?= number_format($order->money, 2) ?>
                        </td>
                        <td style="text-align: center">
                            <?= $order->orderDate ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="pagination" style="text-align:center;"><?=  LinkPager::widget(['pagination' => $pages]); ?></div>
    </div>
</div>
<?php $this->endBlock(); ?>