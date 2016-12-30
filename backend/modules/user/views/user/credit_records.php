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
            'value' => function ($order) use ($loan){
                return $loan[$order['loan_id']]['title'];
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
        })
    })
</script>

