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
<div class="portlet-body">

    <table class="table">
        <tr>
            <td>
                <span class="title">交易类型</span>
                <select name="ref_type" id="point_search_form_type" m-wrap span6>
                    <option value="">---全部---</option>
                    <?php
                    $types = ['loan_order', 'point_order', 'first_order_1', 'point_order_fail', 'point_fa_fang', 'mall_increase', 'point_batch', 'promo', 'check_in', 'wechat_connect', 'check_in_retention'];
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
                    return  in_array($data->ref_type, [PointRecord::TYPE_LOAN_ORDER, PointRecord::TYPE_FIRST_LOAN_ORDER_POINTS_1])  && isset($orders[$data->ref_id]) ? $orders[$data->ref_id]->loan->title : '---';
                }
            ],
            [
                'label' => '积分数额',
                'format' => 'html',
                'value' => function($data) {
                    if ($data->incr_points > 0) {
                        return '<span class="green">+'.StringUtils::amountFormat2($data->incr_points).'</span>';
                    } else {
                        return '<span class="red">-'.StringUtils::amountFormat2($data->decr_points).'</span>';
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
        });
        $('.point_search').on('click', function(){
            var ref_type = $('#point_search_form_type').val();
            getPointList('/user/user/point-list?userId=<?= $user->id ?>&ref_type='+ref_type);
        });
    })
</script>