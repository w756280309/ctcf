<?php
    use \common\view\LoanHelper;
    use \common\utils\StringUtils;
?>
<?=
\yii\grid\GridView::widget([
    'id' => 'grid_view_credit_note',
    'dataProvider' => $dataProvider,
    'layout' => '{items}',
    'tableOptions' => ['class' => 'credit_note_list table table-hover table-striped'],
    'columns' => [
        [
            'label' => '序号',
            'value' => function ($record) {
                return $record['id'];
            }
        ],
        [
            'label' => '项目名称',
            'format' => 'raw',
            'value' => function ($record) use ($loans) {
                $loan       = $loans[$record['loan_id']];
                $title      = $loan['title'];
                $startDate  = date('Y-m-d', $loan['jixi_time']);
                $endDate    = date('Y-m-d', $loan['finish_date']);
                $expires    = $loan->isAmortized() ? $loan->expires . '月' : $loan->expires . '天';
                $loanRate   = LoanHelper::getDealRate($loan).'%';
                if (!empty($loan->jiaxi)) {
                    $loanRate = $loanRate.'+'.StringUtils::amountFormat2($loan->jiaxi).'%';
                }
                $refundMethod = Yii::$app->params['refund_method'][$loan->refund_method];
                $backUrl = '/product/productonline/list?title=' . $title;
                $frontUrl = rtrim(Yii::$app->params['clientOption']['host']['frontend'], '/') . '/credit/note/detail?id='.$record['id'];
                $html = <<<HTML
<button type="button" class="btn btn-primary note_title">
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
            'label' => '转让时间',
            'value' => function ($record) {
                return $record['createTime'];
            }
        ],
        [
            'label' => '发起转让金额',
            'value' => function ($record) {
                return number_format(bcdiv($record['amount'], 100), 2);
            },
        ],
        [
            'label' => '已转让金额',
            'format'=> 'raw',
            'value' => function ($record) {
                $money =  number_format(bcdiv($record['tradedAmount'], 100), 2);

                $html = <<<HTML
<button type="button" class="btn btn-primary trade_amount">
  $money
</button>
 <div class="loan_detail popover right">
    <input type="hidden" name="note_id" value="{$record['id']}">
    <div class="arrow"></div>
    <div class="popover-content">
     <table>
    </table>
</div>
</div>
HTML;
                return $html;

            },
        ],
        [
            'label' => '状态',
            'value' => function ($record) {
                if ($record['tradedAmount'] == $record['amount']) {
                    return '已售罄';
                } elseif ($record['isClosed']) {
                    return '已结束';
                } else {
                    return '转让中';
                }
            }
        ],
    ],
])
?>
<div class="credit_note_pager pagination" style="text-align: center;">
    <?= \yii\widgets\LinkPager::widget(['pagination' => $pages]); ?>
</div>
<script>
    $(function(){
        $('.credit_note_pager ul li').on('click', 'a', function(e) {
            e.preventDefault();
            getCreditNoteList($(this).attr('href'));
        });
        $('.note_title').click(function () {
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
        $('.trade_amount').click(function () {
            var _this = $(this);
            var detail = _this.parent().find('.loan_detail').eq(0);
            var noteId = detail.find('input').eq(0).val();
            var tableObj = detail.find('table').eq(0);
            var tradeAmount = parseFloat(_this.html());
            var html   = '';
            $('.loan_detail').not(detail).hide();
            if (tradeAmount) {
                $.post('/user/user/invest-info',{id:noteId},function (data) {
                    var obj = eval('(' + data + ')');
                    $.each(obj,function (k,el) {
                        html +='<tr><td colspan="2" style="text-overflow:ellipsis; white-space:nowrap; overflow:hidden;"><span>手机:</span>'+el.mobile+'</td><td colspan="2" style="text-overflow:ellipsis; white-space:nowrap; overflow:hidden;"><span>投资额:</span>'+el.principal+'</td></tr>';
                    });
                    tableObj.html(html);
                });
                if (detail.css('display') == 'block') {
                    detail.hide();
                    _this.removeClass('btn-primary');
                } else {
                    var left;
                    var top;
                    _this.parent().css({position:"relative"});
                    setTimeout(function(){
                        left = _this.width() + 37;
                        top = detail.height()/2 - 8;
                        detail.css({'left':left, 'top':-top});
                        detail.show();
                    },500);
                    _this.addClass('btn-primary');
                }
            }
        });
    })
</script>
