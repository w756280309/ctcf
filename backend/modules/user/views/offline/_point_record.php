<?php

use common\utils\StringUtils;
use common\models\mall\PointRecord;
use yii\grid\GridView;

?>

<div class="float-left">
    <a class="btn green" href="/user/offline/exchange-goods?id=<?= $id ?>">
        兑换商品
    </a>

    <a class="btn green" href="/user/point/add?userId=<?= $id ?>&isOffline=1">
        发放积分
    </a>
</div>

<?=
    GridView::widget([
        'id' => 'grid_view_point_record',
        'dataProvider' => $dataProvider,
        'layout' => '{items} <center><div class="pagination point_record_pager">{pager}</div></center>',
        'tableOptions' => ['class' => 'point_record_list table-bordered table-advance table table-hover table-striped'],
        'columns' => [
            [
                'label' => '交易类型',
                'value' => function ($record) {
                    return PointRecord::getTypeName($record['ref_type']);
                }
            ],
            [
                'label' => '流水号',
                'value' => function ($record) {
                    return $record['sn'];
                }
            ],
            [
                'label' => '积分',
                'value' => function ($record) {
                    return $record['incr_points'] > 0 ? '+'.StringUtils::amountFormat2($record['incr_points']) : '-'.StringUtils::amountFormat2($record['decr_points']);
                },
                'contentOptions' => ['class' => 'money'],
                'headerOptions' => ['class' => 'money'],
            ],
            [
                'label' => '剩余积分',
                'value' => function ($record) {
                    return StringUtils::amountFormat2($record['final_points']);
                },
                'contentOptions' => ['class' => 'money'],
                'headerOptions' => ['class' => 'money'],
            ],
            [
                'label' => '商品名称',
                'value' => function ($record) {
                    return $record['offGoodsName'] ? $record['offGoodsName'] : '---';
                },
                'contentOptions' => ['class' => 'span4'],
                'headerOptions' => ['class' => 'span4'],
            ],
            [
                'label' => '发放积分描述',
                'value' => function ($record) {
                    return $record['remark'] ? $record['remark'] : '---';
                },
            ],
            [
                'label' => '认购日期',
                'value' => function ($record) use ($orders) {
                    return PointRecord::TYPE_OFFLINE_BUY_ORDER === $record->ref_type && isset($orders[$record->ref_id]) ? $orders[$record->ref_id]->orderDate : '---';
                }
            ],
            [
                'label' => '创建时间',
                'value' => function ($record) {
                    return $record['recordTime'];
                }
            ],
        ],
    ])
?>

<script>
    $(function() {
        $('.point_record_pager ul li').on('click', 'a', function(e) {
            e.preventDefault();
            getPointList($(this).attr('href'));
        });
    })
</script>
