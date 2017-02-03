<?php

use common\utils\StringUtils;
use yii\grid\GridView;

?>

<?=
GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => '{items} <center><div class="pagination coin_page">{pager}</div></center>',
    'tableOptions' => ['class' => 'money_record_list table-bordered table-advance table table-hover table-striped'],
    'columns' => [
        [
            'label' => '项目名称',
            'value' => function($data) use ($isOffline) {
                if ($isOffline) {
                    $loanName = $data->offlineOrder->loan->title;
                } else {
                    $loanName = $data->onlineOrder->loan->title;
                }

                return $loanName;
            },
            'contentOptions' => ['class' => 'span6'],
            'headerOptions' => ['class' => 'span6'],
        ],
        [
            'label' => $isOffline ? '订单ID' : '订单流水',
            'value' => function($data) use ($isOffline) {
                return $isOffline ? $data->offlineOrder->id : $data->onlineOrder->sn;
            },
        ],
        [
            'label' => '财富值增量',
            'value' => function($data) {
                return StringUtils::amountFormat2($data->incrCoins).($data->incrCoins < 0 ? '(删除数据)' : '');
            },
            'contentOptions' => ['class' => 'money'],
            'headerOptions' => ['class' => 'money'],
        ],
        [
            'label' => '财富值总额',
            'value' => function($data) {
                return StringUtils::amountFormat2($data->finalCoins);
            },
            'contentOptions' => ['class' => 'money'],
            'headerOptions' => ['class' => 'money'],
        ],
        [
            'label' => $isOffline ? '认购日期' : '认购时间',
            'value' => function($data) use ($isOffline) {
                return $isOffline ? $data->offlineOrder->orderDate : date('Y-m-d H:i:s', $data->onlineOrder->created_at);
            },
        ],
        [
            'label' => '创建时间',
            'value' => function($data) {
                return $data->createTime;
            },
        ],
    ],
])
?>

<script>
    $(function() {
        $('.coin_page ul li').on('click', 'a', function(e) {
            e.preventDefault();
            getCoinList($(this).attr('href'));
        })
    })
</script>