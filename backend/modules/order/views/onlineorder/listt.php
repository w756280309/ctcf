<?php
use common\models\product\OnlineProduct;
use common\models\offline\OfflineOrder;
use common\models\offline\OfflineUser;
use common\models\order\OnlineOrder;
$status = Yii::$app->request->get('status');
$loan_status = Yii::$app->request->get('loan_status');
?>
<?php if (!$user instanceof OfflineUser) { ?>
<div class="portlet-body">

    <table class="table">
        <tr>
            <td>
                <span class="title">投资状态</span>
                <select name="status" id="loan_order_form_type" m-wrap span6>
                    <option value="">---全部---</option>
                    <option value="1" <?= ($status === '1') ? "selected='selected'" : "" ?> >投标成功</option>
                    <option value="0" <?= ($status === '0') ? "selected='selected'" : "" ?> >投标失败</option>
                    <option value="2" <?= ($status === '2') ? "selected='selected'" : "" ?> >撤标</option>
                </select>
            </td>
            <td>
                <span class="title">标的状态</span>
                <select name="loan_status" id="loan_status" m-wrap span6>
                    <option value="">---全部---</option>
                    <option value="0">
                        未上线
                    </option>
                    <?php foreach ($loanStatus as $key => $val): ?>
                        <option value="<?= $key ?>"
                            <?php
                            if ($loan_status == $key) {
                                echo 'selected';
                            }
                            ?> >
                            <?= $val ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <span class="title">开始时间</span>
                <input id="loan_order_form_start" type="text" value="<?= Yii::$app->request->get('start') ?>"
                       name="start" onclick='WdatePicker();' class="m-wrap span8"/>
            </td>
            <td>
                <span class="title">结束时间</span>
                <input id="loan_order_form_end" type="text" value="<?= Yii::$app->request->get('end') ?>" name="end"
                       onclick='WdatePicker();' class="m-wrap span8"/>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <div align="right" class="search-btn">
                    <button class="btn blue btn-block loan_order_search" style="width: 100px;">搜索 <i
                            class="m-icon-swapright m-icon-white"></i></button>
                </div>
            </td>
        </tr>
    </table>
</div>
<?php } ?>
        <!--search end -->
<?= \yii\grid\GridView::widget([
    'id' => 'grid_view_loan_order',
    'dataProvider' => $dataProvider,
    'layout' => '{items} <center><div class="pagination loan_order_pager">{pager}</div></center>',
    'tableOptions' => ['class' => 'loan_order_list table table-hover table-striped'],
    'columns' => [
        [
            'label' => !$user instanceof OfflineUser ? '流水号' : '标的序号',
            'value' => function ($record){

                return !$record instanceof OfflineOrder ? $record->sn : $record->loan->sn;
            }
        ],
        [
            'label' => '项目名称',
            'format' => 'raw',
            'value' => function ($record){
                $offline = !$record instanceof OfflineOrder;
                $loan = $record->loan;
                $title = $loan->title;
                $startDate = $loan->jixi_time ? ($offline ? date('Y-m-d', $loan->jixi_time) : $loan->jixi_time) : '---';
                $endDate = $loan->finish_date ? ($offline ? date('Y-m-d', $loan->finish_date) : $loan->finish_date) : '---';
                $expires = $offline ? ($loan->isAmortized() ? $loan->expires . '月' : $loan->expires . '天') :
                    ($loan->expires . $loan->unit);
                $loanRate = $offline ? \common\view\LoanHelper::getDealRate($loan).'%': $loan->yield_rate;
                if (!empty($loan->jiaxi)) {
                    $loanRate = $loanRate.'+'.\common\utils\StringUtils::amountFormat2($loan->jiaxi).'%';
                }
                $refundMethod = $offline ? Yii::$app->params['refund_method'][$loan->refund_method] : '---';
                $productList = '/product/productonline/list?title=' . $title;    //跳转到贷款管理列表页，并通过项目名称筛选出该标的
                $detailUrl = $offline ? rtrim(Yii::$app->params['clientOption']['host']['frontend'], '/') . '/deal/deal/detail?sn='.$loan->sn :
                    rtrim(Yii::$app->params['clientOption']['host']['backend'], '/') . '/offline/offline/loanlist?sn='.$loan->sn;

                $html = <<<HTML
<button type="button" class="btn btn-primary loan_title">
  $title
</button>
 <div class="loan_detail popover right">
    <div class="arrow"></div>
    <div class="popover-content">
     <table>
        <tr><td colspan="2"><span>起息日:</span>$startDate</td></tr>
        <tr><td colspan="2"><span>到期日:</span>$endDate</td></tr>
        <tr><td colspan="2"><span>期限:</span>$expires</td></tr>
        <tr><td colspan="2"><span>项目利率:</span>$loanRate</td></tr>
        <tr><td colspan="2"><span>还款方式:</span>$refundMethod</td></tr>
        <tr>
            <td><a style="white-space: nowrap;font-size: 12px;" href="$productList"  class="btn">项目基本信息</a></td>
            <td><a style="white-space: nowrap;font-size: 12px;" href="$detailUrl" target="_blank" class="btn">标的详情(前端)</a></td>
        </tr>
    </table>
</div>
</div>
HTML;

                return $html;
            }
        ],
        [
            'label' => '购买渠道',
            'value' => function ($record){
                if (!$record instanceof OfflineOrder) {
                    switch ($record->investFrom) {
                        case OnlineOrder::INVEST_FROM_WAP:
                            $res = 'WAP投资';
                            break;
                        case OnlineOrder::INVEST_FROM_WX:
                            $res = '微信投资';
                            break;
                        case OnlineOrder::INVEST_FROM_PC:
                            $res = 'PC投资';
                            break;
                        case OnlineOrder::INVEST_FROM_APP:
                            $res = 'APP投资';
                            break;
                        case OnlineOrder::INVEST_FROM_OTHER:
                            $res = '未知来源';
                            break;
                        default :
                            $res = '未知来源';
                    }
                } else {
                    $res = '线下标的';
                }
                return $res;
            }
        ],
        [
            'label' => !$user instanceof OfflineUser ? '投资金额' : '投资金额(万)',
            'value' => function ($record){
                return isset($record->order_money) ? number_format($record->order_money, 2) :$record->order_money();
            },
            'contentOptions' => ['class' => 'money'],
            'headerOptions' => ['class' => 'money'],
        ],
        [
            'label' => '抵扣代金券',
            'value' => function ($record){
                return isset($record->couponAmount) ? number_format($record->couponAmount, 2) : '---';
            },
            'contentOptions' => ['class' => 'money'],
            'headerOptions' => ['class' => 'money'],
        ],
        [
            'label' => !$user instanceof OfflineUser ? '投资时间' : '认购日期',
            'value' => function ($record){
                return $record->getOrderDate();
            }
        ],
        [
            'label' => '标的状态',
            'value' => function ($record){
                return isset($record->loan->status) ? Yii::$app->params['deal_status'][$record->loan->status] : '---';
            }
        ],
        [
            'label' => '订单状态',
            'value' => function ($record){
                if (!$record instanceof OfflineOrder) {
                    if (isset($record->status)) {
                        if ($record->status === 0){
                            $res = '投标失败';
                        } elseif ($record->status === 1) {
                            $res = '投标成功';
                        } elseif ($record->status === 2) {
                            $res = '撤标';
                        } else {
                            $res = '---';
                        }
                    }
                } else {
                    $res = '---';
                }
                return $res;
            }
        ],
    ],
])
?>
<script>
    $(function(){
        $('.loan_order_pager ul li').on('click', 'a', function(e) {
            e.preventDefault();
            getLoanOrderList($(this).attr('href'));
        });
        $('.loan_order_search').on('click', function(){
            var status = $('#loan_order_form_type').val();
            var start = $('#loan_order_form_start').val();
            var end = $('#loan_order_form_end').val();
            var loan_status = $('#loan_status').val();
            getLoanOrderList('/order/onlineorder/detailt?id=<?= $user->id?>&start='+start+'&end='+end+'&status='+status+'&loan_status='+loan_status);
        });
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

