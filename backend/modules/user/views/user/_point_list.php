<?php

use common\utils\StringUtils;
use common\models\mall\PointRecord;
use yii\grid\GridView;

?>


<div class="float-left">
    <a class="btn green" href="/user/point/add?userId=<?= $user->id ?>">
        发放积分
    </a>
</div>

<?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{items} <center><div class="pagination point_page">{pager}</div></center>',
        'tableOptions' => ['class' => 'table-bordered table-advance table table-hover table-striped'],
        'columns' => [
            [
                'label' => '流水号',
                'value' => function($data) {
                    return $data->sn;
                }
            ],
            [
                'label' => '交易类型',
                'value' => function($data) {
                    return PointRecord::getTypeName($data->ref_type);
                }
            ],
            [
                'label' => '项目名称',
                'value' => function($data) use ($orders) {
                    return PointRecord::TYPE_LOAN_ORDER === $data->ref_type && isset($orders[$data->ref_id]) ? $orders[$data->ref_id]->loan->title : '---';
                }
            ],
            [
                'label' => '积分数额',
                'format' => 'html',
                'value' => function($data) {
                    if ($data->incr_points) {
                        return '<span class="red">+'.StringUtils::amountFormat2($data->incr_points).'</span>';
                    } else {
                        return '<span class="green">-'.StringUtils::amountFormat2($data->decr_points).'</span>';
                    }
                },
                'contentOptions' => ['class' => 'money'],
                'headerOptions' => ['class' => 'money'],
            ],
            [
                'label' => '积分总额',
                'value' => function($data) {
                    return StringUtils::amountFormat2($data->final_points);
                },
                'contentOptions' => ['class' => 'money'],
                'headerOptions' => ['class' => 'money'],
            ],
            [
                'label' => '用户等级',
                'value' => function($data) {
                    return null !== $data->userLevel ? 'VIP'.$data->userLevel : '---';
                }
            ],
            [
                'label' => '明细描述',
                'value' => function($data) {
                    return $data->remark ? $data->remark : '---';
                }
            ],
            [
                'label' => '创建时间',
                'value' => function($data) {
                    return $data->recordTime;
                }
            ],
        ],
    ])
?>

<script>
    $(function() {
        $('.point_page ul li').on('click', 'a', function(e) {
            e.preventDefault();
            getPointList($(this).attr('href'));
        })
    })
</script>