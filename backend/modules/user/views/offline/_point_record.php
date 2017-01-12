<?php

use common\models\mall\PointRecord;
use \yii\grid\GridView;

?>

<div class="float-left">
    <a class="btn green" href="/user/offline/exchange-goods?id=<?= $id ?>">
        兑换商品
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
                return $record['incr_points'] > 0 ? $record['incr_points'] : -$record['decr_points'];
            }
        ],
        [
            'label' => '剩余积分',
            'value' => function ($record) {
                return $record['final_points'];
            },
        ],
        [
            'label' => '商品名称',
            'value' => function ($record) {
                return $record['offGoodsName'];
            },
        ],
        [
            'label' => '交易时间',
            'value' => function ($record) {
                return $record['recordTime'];
            }
        ],
    ],
])
?>
<script>
    $(function(){
        $('.point_record_pager ul li').on('click', 'a', function(e) {
            e.preventDefault();
            getPointRecord($(this).attr('href'));
        });
    })
</script>
