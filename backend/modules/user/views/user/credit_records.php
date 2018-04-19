<?php use common\models\product\OnlineProduct;?>
<?= \yii\grid\GridView::widget([
    'id' => 'grid_view_credit_order',
    'dataProvider' => $dataProvider,
    'layout' => '{items}',
    'tableOptions' => ['class' => 'credit_order_list table table-hover table-striped'],
    'columns' => [
        [
            'label' => '债权订单ID',
            'value' => function ($order){
                return $order['id'];
            }
        ],
        [
            'label' => '项目名称',
            'format' => 'raw',
            'value' => function ($order) use ($loan){
                $loan       = $loan[$order['loan_id']];
                $title      =  $loan['title'];
                $startDate  = date('Y-m-d', $loan['jixi_time']);
                $endDate    = date('Y-m-d', $loan['finish_date']);
                $expires    = $loan->isAmortized() ? $loan->expires . '月' : $loan->expires . '天';
                $loanRate   = \common\view\LoanHelper::getDealRate($loan).'%';
                if (!empty($loan->jiaxi)) {
                    $loanRate = $loanRate.'+'.\common\utils\StringUtils::amountFormat2($loan->jiaxi).'%';
                }
                $refundMethod = Yii::$app->params['refund_method'][$loan->refund_method];
                $backUrl = '/product/productonline/list?title=' . $title;
                $frontUrl = rtrim(Yii::$app->params['clientOption']['host']['frontend'], '/') . '/credit/note/detail?id='.$order['note_id'];
                $html = <<<HTML
<button type="button" class="btn btn-primary credit_title">
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
            <td><a style="white-space: nowrap;font-size: 12px;" href="$backUrl"  class="btn">项目基本信息</a></td>
            <td><a style="white-space: nowrap;font-size: 12px;" href="$frontUrl" target="_blank" class="btn">标的详情(前端)</a></td>
        </tr>
    </table>
</div>
</div>
HTML;
                return $html;
            }
        ],
        [
            'label' => '折让率',
            'value' => function ($order) {
                $discountRate = '---';
                if ($order['status'] == 1) {
                    $discountRate = number_format($order['discountRate'],2).'%';
                }
                return $discountRate;
            }
        ],
        [
            'label' => '剩余时间',
            'value' => function ($order) use ($loan){
                $remainTime = '---';
                if ($order['status'] == 1 && !empty($loan)) {
                    $timeJson =  OnlineProduct::getRemainingTime($loan[$order['loan_id']],$order['createTime']);
                    $remainingDuration = json_decode($timeJson,true);
                    if (isset($remainingDuration['months']) && $remainingDuration['months'] > 0) {
                        $month =  $remainingDuration['months'] . '个月';
                    }
                    if (isset($remainingDuration['days'])) {
                        if (!isset($remainingDuration['months']) || $remainingDuration['days'] > 0) {
                            $days =  $remainingDuration['days'] . '天';
                        }
                    }
                    $remainTime =  $month.$days;
                }
                return $remainTime;
            }
        ],
        [
            'label' => '预期剩余收益',
            'value' => function ($order) use ($userAsset) {
                $remainInterest = '---';
                if ($order['status'] == 1) {
                    $remainInterest = $userAsset[$order['id']] -> getRemainingInterest($order['createTime']);
                    $remainInterest = floatval($remainInterest / 100);
                }
                return $remainInterest;
            }
        ],
        [
            'label' => '应付利息',
            'value' => function ($order) use ($userAsset) {
                $currentInterest = '---';
                if ($order['status'] == 1) {
                    $currentInterest = $userAsset[$order['id']] -> getCurrentInterest($order['createTime']);
                    $currentInterest = floatval($currentInterest / 100);
                }
                return $currentInterest;
            }
        ],
        [
            'label' => '投资金额',
            'value' => function ($order){
                return number_format(bcdiv($order['principal'], 100, 2),2);
            },
            'contentOptions' => ['class' => 'money'],
            'headerOptions' => ['class' => 'money'],
        ],
        [
            'label' => '投资时间',
            'value' => function ($order){
                return $order['createTime'];
            }
        ],
        [
            'label' => '状态',
            'value' => function ($order){
                if($order['status'] == 0){
                    $res = "未处理";
                }elseif($order['status']==1){
                    $res = "投资成功";
                }elseif($order['status']==2){
                    $res = "失败";
                }else{
                    $res = "处理中";
                }
                return $res;
            }
        ],
    ],
])
?>
<div class="credit_order_pager pagination" style="text-align: center;">
    <?= \yii\widgets\LinkPager::widget(['pagination' => $pages]); ?>
</div>
<script>
    $(function(){
        $('.credit_order_pager ul li').on('click', 'a', function(e) {
            e.preventDefault();
            getCreditOrderList($(this).attr('href'));
        });
        $('.credit_title').click(function () {
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

