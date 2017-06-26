<?php

$this->title = '计息审核';

use common\view\LoanHelper;
use common\utils\StringUtils;
/**
 * @var \common\models\product\OnlineProduct $loan
 */
?>

<?php $this->beginBlock('blockmain'); ?>
    <style>
        table.list {
            width: 100%;
        }
        tr td.list {
            border: 1px solid #666;
            padding: 10px;
            width: 100%;
            background-color: #f9f9f9;
        }
        thead th.list {
            border: 1px solid #666;
            background-color: #DDD;
            text-align: left;
        }
    </style>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div>
            <h3 class="page-title">
                贷款管理 <small>贷款管理模块【主要包含项目的管理以及项目分类管理】</small>
            </h3>

            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/product/productonline/list">贷款管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">计息审核</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="row-fluid">
        <h3><?= $loan->title?></h3>
        <div class="col-md-12">
           <h4>标的基本信息</h4>
            <table class="table table-bordered">
                <tr>
                    <td>计息日: <?= date('Y-m-d', $loan->jixi_time)?></td>
                    <td>还款方式: <?= Yii::$app->params['refund_method'][$loan->refund_method]?></td>
                    <td>截止日: <?= $loan->getEndDate() ?></td>
                    <td>项目期限: <?= $loan->expires?></td>
                    <td>实际募集金额: <?= number_format($loan->funded_money, 2)?></td>
                    <td>标的利率: <?= LoanHelper::getDealRate($loan) ?><i class="col-lu">%<?php if (!empty($loan->jiaxi)) { ?><em class="credit-jiaxi">+<?= StringUtils::amountFormat2($loan->jiaxi) ?>%</em><?php } ?></i></td>
                </tr>
            </table>
        </div>
        <div class="portlet-body">
            <h4>还款数据预览</h4>
            <?php foreach ($repayment as $term => $item) :?>
                <div class="portlet-body">
                    <table class="list">
                        <thead>
                            <tr>
                                <th class="list">第<?= $term ?>期:</th>
                            </tr>
                        </thead>
                        <tr>
                            <td class="list">
                                本期应还款时间：<?= $item['repaymentDate']?>
                                <br>
                                本期应还款本金：<?= $item['totalPrincipal'] ?>（元）&emsp;
                                本期应还款利息：<?= $item['totalInterest']?>（元）
                                <img src="/image/you.png" class="jiantou<?= $term ?>" onclick="tableShow('.jiantou<?= $term ?>')" data="<?= $term ?>" alt="" style="position: absolute; right: 30px; height:20px; width: 20px;">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table class="table table-striped table-bordered table-advance table-hover table-content<?= $term ?>" style="display: none;">
                                    <thead>
                                    <tr>
                                        <th>用户ID</th>
                                        <th style="text-align: right; padding-right: 70px">投资金额</th>
                                        <th>实际利率</th>
                                        <th style="text-align: right; padding-right: 70px">应还款本金（元）</th>
                                        <th style="text-align: right; padding-right: 70px">应还款利息（元）</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($item['orderData'] as $key => $val) : ?>
                                        <tr>
                                            <td><?= $val['userId'] ?></td>
                                            <td style="text-align: right; padding-right: 70px"><?= number_format($val['orderMoney'], 2) ?></td>
                                            <td><?= $val['rate']?></td>
                                            <td style="text-align: right; padding-right: 70px"><?= number_format($val['principal'], 2) ?></td>
                                            <td style="text-align: right; padding-right: 70px"><?= number_format($val['interest'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                <br/>
            <?php endforeach; ?>
        </div>
        <div class="form-actions" style="text-align:center">
            <form action="/product/productonline/jixi-examined?id=<?= $loan->id?>" method="post" id="jixi_examined_form">
                <input type="hidden" name="_csrf" id="csrf" value="<?= Yii::$app->request->getCsrfToken()?>">
                <input type="submit" value="审核通过" class="btn brn-primary blue" id="submit_btn">
            </form>
        </div>
    </div>
</div>
    <script>
        function tableShow (className)
        {
            var $btn = $(className);
            var data = $btn.attr('data');
            var $content = $('.table-content'+data);

            if ($content.css('display') === 'none') {
                $btn.attr('src', '/image/shang.png');
                $content.show();
            } else {
                $btn.attr('src', '/image/you.png');
                $content.hide();
            }
        }

        $('#submit_btn').click(function(e){
            e.preventDefault();
            if (confirm('审核通过之后即可进行确认计息操作，是否确认审核通过')) {
                $('#jixi_examined_form').submit();
            }
        });
    </script>
<?php $this->endBlock(); ?>