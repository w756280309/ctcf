<?=
\yii\grid\GridView::widget([
    'id' => 'grid_view_recharge',
    'dataProvider' => $dataProvider,
    'layout' => '{items} <center><div class="pagination recharge_pager">{pager}</div></center>',
    'tableOptions' => ['class' => 'recharge_record_list table table-hover table-striped'],
    'columns' => [
        [
            'label' => '流水号',
            'value' => function ($record) {
                return $record['sn'];
            }
        ],
        [
            'label' => '金额',
            'value' => function ($record) {
                return number_format($record['fund'], 2);
            },
            'contentOptions' => ['class' => 'money'],
            'headerOptions' => ['class' => 'money'],
        ],
        [
            'label' => '银行',
            'value' => function ($record) use ($banks) {
                if ($record['pay_type'] == \common\models\user\RechargeRecord::PAY_TYPE_NET) {
                    return isset($banks[$record['bank_id']]) ? $banks[$record['bank_id']]['bankName'] : '---';
                } else {
                    return $record['bank_name'];
                }

            }
        ],
        [
            'label' => '交易时间',
            'value' => function ($record) {
                return date('Y-m-d H:i:s',$record['created_at']);
            }
        ],
        [
            'label' => '状态',
            'value' => function ($record) {
                if (0 == $record['status']) {
                    $desc = "充值未处理";
                } elseif (1 == $record['status']) {
                    $desc = "充值成功";
                } else {
                    $desc = "充值失败";
                }

                if (3 == $record['pay_type']) {
                    return $desc."-线下pos";
                } else {
                    return $desc."-线上充值";
                }
            }
        ],
        [
            'label' => '联动状态',
            'format' => 'raw',
            'value' => function ($record) {
                return '<a class="btn btn-primary get_order_status" sn="'.$record['sn'].'">查询流水在联动状态</a>';
            }
        ],
    ],
])
?>

<script>
    $(function(){
        $('.recharge_pager ul li').on('click', 'a', function(e) {
            e.preventDefault();
            getRechargeList($(this).attr('href'));
        });

        //点击获取流水状态
        $('.get_order_status').bind('click', function () {
            var _this = $(this);
            var sn = _this.attr('sn');
            if (sn) {
                $.get('/user/rechargerecord/get-order-status?sn=' + sn, function (data) {
                    if (data.code) {
                        _this.parent().html(data.message);
                    } else {
                        newalert(0, data.message);
                    }
                });
            }
        });
    })
</script>

