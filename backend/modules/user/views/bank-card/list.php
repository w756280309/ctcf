<?php
use common\models\user\QpayBinding;
use yii\grid\GridView;

echo GridView::widget([
    'id' => 'grid_view_bind_card',
    'dataProvider' => $dataProvider,
    'layout' => '{items} <center><div class="pagination bank_card_pager">{pager}</div></center>',
    'tableOptions' => ['class' => 'band_card_list table table-hover table-striped'],
    'columns' => [
        [
            'label' => '流水号',
            'value' => function ($data) {
                if ($data instanceof QpayBinding) {
                    return $data->binding_sn;
                } else {
                    return $data->sn;
                }
            }
        ],
        [
            'label' => '银行卡号',
            'value' => function ($data) {
                if ($data instanceof QpayBinding) {
                    return substr_replace($data->card_number, '**** **** **** ', 0, -4);
                } else {
                    return substr_replace($data->cardNo, '**** **** **** ', 0, -4);
                }
            }
        ],
        [
            'label' => '银行名称',
            'value' => function ($data) {
                if ($data instanceof QpayBinding) {
                    return $data->bank_name;
                } else {
                    return $data->bankName;
                }
            }
        ],
        [
            'label' => '创建时间',
            'value' => function ($data) {
                return date('Y-m-d H:i:s', $data->created_at);
            }
        ],
        [
            'label' => '状态',
            'value' => function ($data) {
                $arr = ['已申请', '成功', '失败', '处理中'];
                return ($data instanceof QpayBinding ? '绑卡' : '换卡').$arr[$data->status];
            }
        ],
        [
            'label' => '联动状态',
            'format' => 'html',
            'value' => function ($data) {
                return '<a class="btn btn-primary check-ump-info" href="/user/bank-card/ump-info?id='.$data->id.'&type='.($data instanceof QpayBinding ? 'b' : 'u').'">查询流水在联动状态</a>';
            }
        ],
    ],
])
?>


<script type="text/javascript">
    $(function () {
        //点击获取流水状态
        var allowClick = true;
        $('.check-ump-info').on('click', function (e) {
            e.preventDefault();

            if (!allowClick) {
                return;
            }

            var _this = $(this);
            var url = _this.attr('href');
            if (url) {
                allowClick = false;
                var xhr = $.get(url, function (data) {
                    _this.parent().html(data.message);
                    allowClick = true;
                });

                xhr.fail(function () {
                    allowClick = true;
                });
            } else {
                alert('获取查询链接失败');
            }
        });

        $('.bank_card_pager ul li').on('click', 'a', function(e) {
            e.preventDefault();
            getBindList($(this).attr('href'));
        })
    })
</script>
