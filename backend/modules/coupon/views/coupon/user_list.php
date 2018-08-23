<?php
use common\utils\StringUtils;
use yii\grid\GridView;
use yii\grid\CheckboxColumn;
?>
<div class="float-left">
    <a class="btn green" href="javascript:openwin('/coupon/coupon/allow-issue-list?uid=<?= $user->id ?>&tabClass=<?= $tabClass ?>' , 800, 400)">
        发放优惠券
    </a>
    <span style="padding-left: 20px;">可用金额(元)：<?= isset($sumCoupon) ? StringUtils::amountFormat2($sumCoupon) : '0' ?></span>
    <span style="padding-left: 20px;">已用金额(元)：<?= isset($CouponUsed) ? StringUtils::amountFormat2($CouponUsed) : '0' ?></span>

</div>
<div class="portlet-body">

    <table class="table">
        <tr>

            <td>
                <span class="title">状态</span>
                <select name="isUsed" id="coupon_search_form_type" class="m-wrap span3" >
                    <option value="">--全部--</option>
                    <option value="1" <?= ($isUsed === '1') ? "selected='selected'" : "" ?> >未使用</option>
                    <option value="2" <?= ($isUsed === '2') ? "selected='selected'" : "" ?> >已使用</option>
                    <option value="3" <?= ($isUsed === '3') ? "selected='selected'" : "" ?> >已过期</option>
                </select>
            </td>
            <td>
                <span class="title">券类型</span>
                <select name="type" class="m-wap span3" id="type">
                    <option value="">--全部--</option>
                    <option value="0" <?= $type == '0' ? 'selected' : '' ?> >代金券</option>
                    <option value="1" <?= $type == '1' ? 'selected' : '' ?> >加息券</option>
                </select>
            </td>
            <td>
                <div align="right" class="search-btn">
                    <button class="btn blue btn-block coupon_search" style="width: 100px;">搜索 <i
                                class="m-icon-swapright m-icon-white"></i></button>
                </div>
            </td>
        </tr>
    </table>
</div>
<?= GridView::widget([
    'id' => 'grid_view_coupon',
    'dataProvider' => $dataProvider,
    'layout' => '{items} <center><div class="pagination coupon_pager">{pager}</div></center>',
    'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover'],
    'showFooter' => true,
    'columns' => [
        [
            'class' => CheckboxColumn::className(),
            'name' => 'ids',
            'footer' => '<button id="batchDel">删除</button>',
        ],
        [
            'class' => 'yii\grid\SerialColumn',
            'header' => '序号',
        ],
        [
            'label' => '名称',
            'format' =>'html',
            'value' => function ($data) {
                return '<a href="/coupon/coupon/edit?id=' . $data->couponType->id . '">' . $data->couponType->name . '</a>'; ;
            }
        ],
        [
            'label' => '面值(元)',
            'value' => function ($data) {
                return StringUtils::amountFormat2($data->couponType->amount);
            },
            'contentOptions' => ['class' => 'money'],
            'headerOptions' => ['class' => 'money'],
        ],
        [
            'label' => '加息利率(%)',
            'value' => function ($data) {
                return StringUtils::amountFormat2($data->couponType->bonusRate);
            },
            'contentOptions' => ['class' => 'money'],
            'headerOptions' => ['class' => 'money'],
        ],
        [
            'label' => '加息天数',
            'value' => function ($data) {
                return StringUtils::amountFormat2($data->couponType->bonusDays);
            },
            'contentOptions' => ['class' => 'money'],
            'headerOptions' => ['class' => 'money'],
        ],
        [
            'label' => '起投金额(元)',
            'value' => function ($data) {
                return StringUtils::amountFormat2($data->couponType->minInvest);
            },
            'contentOptions' => ['class' => 'money'],
            'headerOptions' => ['class' => 'money'],
        ],
        [
            'label' => '领取时间',
            'value' => function ($data) {
                return date('Y-m-d H:i:s', $data->created_at);
            }
        ],
        [
            'label' => '截止日期',
            'value' => function ($data) {
                return $data->expiryDate;
            }
        ],
        [
            'label' => '使用状态',
            'format' => 'html',
            'value' => function ($data) {
                if ($data->isUsed) {
                    $status = '<font color="#35aa47">已使用</font>';
                    return $status;
                } elseif (date('Y-m-d') > $data->expiryDate) {
                    $status = '<font color="#d84a38">已过期</font>';
                    return $status;
                } else {
                    return '未使用';
                }
            },
        ],
        [
            'label' => '使用时间',
            'value' => function ($data) {
                return $data->isUsed ? date('Y-m-d H:i', $data->order->created_at) : '---';
            },
        ],
        [
            'label' => '发券操作员',
            'format' => 'html',
            'value' => function ($data) {
                return $data->admin_id ? $data->admin->real_name : '---';
            },
        ],
        [
            'label' => '编辑',
            'format' => 'html',
            'value' => function ($data) {
                if (!$data->isUsed && $data->expiryDate > date('Y-m-d')) {
                    return '<a class="btn btn-primary check-ump-info" href="/coupon/coupon/del?id=' . $data->id . '&tabClass=' .  Yii::$app->request->get('tabClass') . '">删除</a>';
                }
            }
        ],
    ],
])

?>

<script>
    $("input[name='ids_all']").on('click', function () {
        $("input[name='ids[]']").prop("checked", this.checked);
    });

    $("input[name='ids_all']").on('click', function () {
        var subs = $("input[name='ids[]']");
        $("#all").prop("checked", subs.length == subs.filter(":checked").length ? true : false);
    });

    $("#batchDel").click(function () {
        var chk_value = [];
        $('input[name="ids[]"]:checked').each(function () {
            chk_value.push($(this).val());
        });
        if (chk_value.length > 0) {
            layer.open({
                content: '确定删除所选' + chk_value.length + '张优惠券吗？'
                , btn: ['确定', '我再想想']
                , yes: function () {
                    //按钮【确定】的回调
                    openLoading();//打开loading
                    $.post(
                        '/coupon/coupon/batch-del',
                        {'ids': chk_value},
                        function (data) {
                            //  data.status == 0 选择的都是已经删除的或者使用过的优惠券
                            //  data.status == 1 删除成功
                            //  data.status == 2 删除失败
                            if (data.status == 0) {
                                var content = '请选择有效的优惠券!';
                            }
                            if (data.status == 2) {
                                var content = '优惠券删除失败!';
                            }
                            if (data.status ==1) {
                                var content = '优惠券删除成功!';
                            }
                            layer.open({
                                content: content
                                , btn: ['确定'],
                                yes:function () {
                                    window.location.href = location.href + "&tabClass=coupon_nav";
                                }
                            });
                        },
                        'json'
                    )
                }, cancel: function () {
                    //右上角和【我再想想】关闭回调
                    layer.closeAll();
                }
            });
        } else {
            layer.open({
                content: '请选择要删除的优惠券！'
                , btn: ['确定']
            });
        }
    })

    $(function(){
        $('.coupon_pager ul li').on('click', 'a', function(e) {
            e.preventDefault();
            getCouponList($(this).attr('href'));
        });
        $('.coupon_search').on('click', function(){
            var isUsed = $('#coupon_search_form_type').val();
            var type = $('#type').val();
            getCouponList('/coupon/coupon/list-for-user?uid=<?= $user->id?>&isUsed='+isUsed+'&type='+type);
        });
    })
</script>
