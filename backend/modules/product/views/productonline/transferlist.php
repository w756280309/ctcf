<?php

use common\utils\StringUtils;
use yii\grid\GridView;
use yii\widgets\LinkPager;

?>
    <style>
        .left {
            text-align: left !important;
        }
        .juzhong {
            text-align: center !important;
        }
    </style>
<?php $this->beginBlock('blockmain'); ?>
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
                <a href="javascript:void(0);">转让列表</a>
            </li>
        </ul>
    </div>
    <div class="portlet-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '{items}',
            'columns' => [
                [
                    'label' => '姓名',
                    'format' => 'html',
                    'value' => function ($data) {
                        return '<a href="/user/user/detail?id=' . $data['user_id'] . '&type=1">' . $data['user']->real_name . '</a>';
                    },
                ],
                [
                    'label' => '手机号',
                    'value' => function ($data) {
                        return $data['user']->mobile;
                    },
                    'contentOptions' => ['class' => 'left'],
                    'headerOptions' => ['class' => 'left'],
                ],
                [
                    'label' => '项目名称',
                    'format' => 'raw',
                    'value' => function ($data){
                        $loan = $data['loan'];
                        $title = $loan->title;
                        $startDate = $loan->jixi_time ? date('Y-m-d', $loan->jixi_time) : '---';
                        $endDate = $loan->finish_date ? date('Y-m-d', $loan->finish_date) : '---';
                        $expires = $loan->isAmortized() ? $loan->expires . '月' : $loan->expires . '天';
                        $loanRate = \common\view\LoanHelper::getDealRate($loan).'%';
                        if (!empty($loan->jiaxi)) {
                            $loanRate = $loanRate.'+'.\common\utils\StringUtils::amountFormat2($loan->jiaxi).'%';
                        }
                        $refundMethod = Yii::$app->params['refund_method'][$loan->refund_method];
                        $detailUrl = rtrim(Yii::$app->params['clientOption']['host']['frontend'], '/') . '/deal/deal/detail?sn='.$loan->sn;
                        $html = <<<HTML
<button type="button" class="btn btn-primary loan_title">
  $title
</button>
 <div class="loan_detail popover right">
    <div class="arrow"></div>
    <div class="popover-content">
     <table>
        <tr><td><span>起息日:</span>$startDate</td></tr>
        <tr><td><span>到期日:</span>$endDate</td></tr>
        <tr><td><span>期限:</span>$expires</td></tr>
        <tr><td><span>项目利率:</span>$loanRate</td></tr>
        <tr><td><span>还款方式:</span>$refundMethod</td></tr>
        <tr><td> <a href="$detailUrl" target="_blank" class="btn">查看更多</a></td></tr>
    </table>
</div>
</div>
HTML;
                        return $html;
                    }
                ],
                [
                    'label' => '转让时间',
                    'value' => function ($data) {
                        return $data['createTime'];
                    },
                    'contentOptions' => ['class' => 'left'],
                    'headerOptions' => ['class' => 'left'],

                ],
                [
                    'label' => '发起转让金额',
                    'value' => function ($data) {
                        return StringUtils::amountFormat3(bcdiv($data['amount'], 100));
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
                ],
                [
                    'label' => '折让率',
                    'value' => function ($data) {
                        return StringUtils::amountFormat3($data['discountRate']);
                    },
                    'contentOptions' => ['class' => 'juzhong'],
                    'headerOptions' => ['class' => 'juzhong'],
                ],
                [
                    'label' => '已转让金额',
                    'value' => function ($data) {
                        return StringUtils::amountFormat3(bcdiv($data['tradedAmount'], 100));
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
                ],
                [
                    'label' => '状态',
                    'value' => function ($data) {
                        return $data['isClosed'] ? '转让完成' : '转让中';
                    },
                ],
                [
                    'label' => '操作',
                    'format' => 'html',
                    'value' => function ($data) {
                        return '<a href="/product/productonline/buytransfer?loan_id='.$data['id'].'">查看</a>';
                    }
                ],
            ],
            'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover']
        ]) ?>
    </div>
    <div class="pagination" style="text-align:center;clear: both"><?= LinkPager::widget(['pagination' => $pages]); ?></div>
        <script>
            $(function(){
                $('.loan_title').click(function () {
                    var _this = $(this);
                    var detail = _this.parent().find('.loan_detail').eq(0);
                    $('.loan_detail').not(detail).hide();
                    if (detail.css('display') == 'block') {
                        detail.hide();
                        _this.removeClass('btn-primary');
                    } else {
                        var left = parseInt(_this.offset().left) + parseInt(_this.width()) + 30;
                        var top = parseInt(_this.offset().top) - 130;
                        detail.css({'left':left, 'top':top});
                        detail.show();
                        _this.addClass('btn-primary');
                    }
                });
            })
        </script>
<?php $this->endBlock(); ?>


