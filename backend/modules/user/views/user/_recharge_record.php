<?php

use common\models\user\RechargeRecord;
use yii\grid\GridView;

?>

<?=
    GridView::widget([
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
                    $payType = (int) $record['pay_type'];

                    if (RechargeRecord::PAY_TYPE_NET === $payType) {
                        return isset($banks[$record['bank_id']]) ? $banks[$record['bank_id']]['bankName'] : '---';
                    }

                    return $record['bank_name'];
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
                    switch ($record['status']) {
                        case RechargeRecord::SETTLE_NO:
                            $desc = "充值未处理";
                            break;
                        case RechargeRecord::STATUS_YES:
                            $desc = "充值成功";
                            break;
                        default:
                            $desc = "充值失败";
                    }

                    switch ($record['pay_type']) {
                        case RechargeRecord::PAY_TYPE_POS:
                            $desc .= '-线下pos';
                            break;
                        case RechargeRecord::PAY_TYPE_NET:
                            $desc .= '-网银充值';
                            break;
                        case RechargeRecord::PAY_TYPE_QUICK:
                            $desc .= '-快捷充值';
                            break;
                    }

                    return $desc;
                }
            ],
            [
                'label' => '联动状态',
                'format' => 'raw',
                'value' => function ($record) {
                    return '<a class="btn btn-primary get_order_status" sn="'.$record['sn'].'">查询流水在联动状态</a>';
                }
            ],
            [
                'label' => '充值结果修复',
                'format' => 'raw',
                'value' => function ($record) {
                    $rechargeStatus = (int) $record['status'];

                    //只修复10天内的充值订单
                    if (
                        $record['created_at'] >= strtotime('-10 day')
                        && RechargeRecord::STATUS_FAULT === $rechargeStatus
                    ) {
                        return '<button class="btn btn-default repair_data" style="display: none;" id="'.$record['sn'].'" data="'.$record['sn'].'">修复</button>';
                    }

                    return '';
                }
            ],
        ],
    ])
?>

<script>
    $(function() {
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

                        if ('成功' === data.message) {
                            $('#'+sn).show();
                        }
                    } else {
                        newalert(0, data.message);
                    }
                });
            }
        });

        //点击修复数据
        $('.repair_data').bind('click', function () {
            var _this = $(this);
            var sn = _this.attr('data');
            if (sn) {
                var confirm =  window.confirm('只有系统充值失败且联动充值成功时，才需要修复数据，确认修复吗?');
                if (confirm) {
                    _this.attr('disabled', true);
                    var request = $.ajax({
                        url: '/user/rechargerecord/repair-data?sn=' + sn,
                        method: "POST",
                        data: {_csrf: "<?= Yii::$app->request->csrfToken?>"}
                    });
                    request.done(function (data) {
                        alert(data.message);
                        if (data.success) {
                            location.reload();
                        }
                        _this.removeAttr('disabled');
                    });
                    request.fail(function (data) {
                        _this.removeAttr('disabled');
                    });
                }
            }
        });
    })
</script>

