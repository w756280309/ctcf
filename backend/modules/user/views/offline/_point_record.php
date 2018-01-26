<?php

use common\utils\StringUtils;
use common\models\mall\PointRecord;
use yii\grid\GridView;

?>

<div class="float-left">
    <a class="btn green" href="/user/offline/exchange-goods?id=<?= $id ?>&tabClass=<?= $tabClass ?>">
        兑换商品
    </a>

    <a class="btn green" href="<?= $user->online ? '/user/point/add?userId='.$user->online->id.'&tabClass='.$tabClass.'&isOffline=0&backUrl='. urlencode(Yii::$app->request->hostInfo.'/user/offline/detail?id='.$id) : '/user/point/add?userId='.$id.'&tabClass='.$tabClass.'&isOffline=1' ?>">
        发放积分
    </a>
</div>
<div class="portlet-body">

    <table class="table">
        <tr>
            <td>
                <span class="title">交易类型</span>
                <select name="ref_type" id="point_search_form_type" m-wrap span6>
                    <option value="">---全部---</option>
                    <?php
                        $types = ['offline_loan_order', 'offline_point_order', 'offline_order_delete', 'offline_order_point_cancel'];
                        foreach ($types as $type) {
                    ?>
                        <option value="<?= $type ?>" <?= ($type === $ref_type) ? "selected='selected'" : "" ?> >
                            <?= PointRecord::getTypeName($type) ?>
                        </option>
                     <?php } ?>
                </select>
            </td>
            <td>
                <div align="right" class="search-btn">
                    <button class="btn blue btn-block point_search" style="width: 100px;">搜索 <i
                                class="m-icon-swapright m-icon-white"></i></button>
                </div>
            </td>
        </tr>
    </table>
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
        $('.point_search').on('click', function(){
            var ref_type = $('#point_search_form_type').val();
            getPointList('/user/offline/points?id=<?= $user->id ?>&ref_type='+ref_type);
        });
    })
</script>
