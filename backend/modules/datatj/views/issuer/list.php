<?php
use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                数据统计

                <a href="export?id=<?= $issuer->id ?>" class="btn green float-right">EXCEL导出</a>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="">数据统计</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href=""><?= $issuer->name ?>数据</a>
                </li>
            </ul>
        </div>

        <div class="portlet-body">
            <table class="table table-striped table-bordered table-advance table-hover">
                <thead>
                    <tr>
                        <th>项目名称</th>
                        <th>项目编号</th>
                        <th>项目状态</th>
                        <th>实际募集金额</th>
                        <th>起息日</th>
                        <th>还款本金</th>
                        <th>还款利息</th>
                        <th>预计还款时间</th>
                        <th>实际还款时间</th>
                        <th>查看还款详情</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($model as $key => $val) : ?>
                        <tr>
                            <td><?= $val->title ?></td>
                            <td><?= $val->issuerSn ?></td>
                            <td><?= \Yii::$app->params['deal_status'][$val->status] ?></td>
                            <td class="text-align-rg"><?= StringUtils::amountFormat2($val->funded_money) ?>元</td>
                            <td><?= empty($val->jixi_time) ? '---' : date('Y-m-d', $val->jixi_time) ?></td>
                            <td class="text-align-rg"><?= isset($plan[$key]) ? StringUtils::amountFormat2(array_sum(ArrayHelper::getColumn($plan[$key], 'totalBenjin'))).'元' : '---' ?></td>
                            <td class="text-align-rg"><?= isset($plan[$key]) ? StringUtils::amountFormat2(array_sum(ArrayHelper::getColumn($plan[$key], 'totalLixi'))).'元' : '---' ?></td>
                            <td>
                                <?php
                                    if (isset($plan[$key])) {
                                        $v = ArrayHelper::getColumn($plan[$key], 'refund_time');
                                        echo date('Y-m-d', end($v));
                                    } else {
                                        echo '---';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    $arr = $refundTime[$key];
                                    if (isset($arr) && OnlineProduct::STATUS_OVER === $val->status) {
                                        echo date('Y-m-d', end($arr));
                                    } else {
                                        echo '---';
                                    }
                                ?>
                            </td>
                            <td>
                                <center>
                                    <?php if (in_array($val->status, [OnlineProduct::STATUS_HUAN, OnlineProduct::STATUS_OVER])) { ?>
                                        <a href="javascript:void(0)" onclick="$('.plan<?= $key ?>').toggle();">查看</a>
                                    <?php } else { ?>
                                        暂无数据
                                    <?php } ?>
                                </center>
                            </td>
                        </tr>
                        <?php if (isset($plan[$key])) {  ?>
                            <tr class="plan<?= $key ?>" style="display: none;">
                                <td colspan="10">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>期数</th>
                                                <th>还款本金（元）</th>
                                                <th>还款利息（元）</th>
                                                <th>预计还款时间</th>
                                                <th>实际还款时间</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($plan[$key] as $val) : ?>
                                            <tr>
                                                <td><?= $val['qishu'] ?></td>
                                                <td><?= StringUtils::amountFormat2($val['totalBenjin']) ?></td>
                                                <td><?= StringUtils::amountFormat2($val['totalLixi']) ?></td>
                                                <td><?= date('Y-m-d', $val['refund_time']) ?></td>
                                                <td><?= isset($refundTime[$key][$val['qishu']]) ? date('Y-m-d', $refundTime[$key][$val['qishu']]) : '---' ?></td>
                                            </tr>
                                         <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pagination" style="text-align:center"><?=  LinkPager::widget(['pagination' => $pages]); ?></div>
        </div>
    </div>
</div>
<br>
<br>
<?php $this->endBlock(); ?>