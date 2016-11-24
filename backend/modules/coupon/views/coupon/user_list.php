<?php
use common\utils\StringUtils;
use yii\grid\GridView;
?>
<div class="float-left">
    <a class="btn green" href="javascript:openwin('/coupon/coupon/allow-issue-list?uid=<?= $user->id ?>' , 800, 400)">
        发放代金券
    </a>
</div>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => '{items} <center><div class="pagination coupon_pager">{pager}</div></center>',
    'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover'],
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'header' => '序号',
        ],
        [
            'label' => '名称',
            'value' => function ($data) {
                return $data->couponType->name;
            }
        ],
        [
            'label' => '面值(元)',
            'value' => function ($data) {
                return StringUtils::amountFormat2($data->couponType->amount);
            }
        ],
        [
            'label' => '起投金额(元)',
            'value' => function ($data) {
                return StringUtils::amountFormat2($data->couponType->minInvest);
            }
        ],
        [
            'label' => '领取时间',
            'value' => function ($data) {
                return date('Y-m-d H:i:s', $data->created_at);
            }
        ],
        [
            'label' => '截止日期',
            'value' => function ($data) {
                return $data->expiryDate;
            }
        ],
        [
            'label' => '使用状态',
            'value' => function ($data) {
                if ($data->isUsed) {
                    return '已使用';
                } elseif (date('Y-m-d') > $data->expiryDate) {
                    return '已过期';
                } else {
                    return '未使用';
                }
            }
        ],
    ],
])
?>
<script>
    $(function(){
        $('.coupon_pager ul li').on('click', 'a', function(e) {
            e.preventDefault();
            getCouponList($(this).attr('href'));
        })
    })
</script>
