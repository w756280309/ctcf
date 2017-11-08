<?php
use common\utils\StringUtils;
use yii\grid\GridView;
?>
<div class="float-left">
    <a class="btn green" href="javascript:openwin('/coupon/coupon/allow-issue-list?uid=<?= $user->id ?>' , 800, 400)">
        发放代金券
    </a>
    <span style="padding-left: 20px;">可用金额(元)：<?= isset($sumCoupon) ? StringUtils::amountFormat2($sumCoupon) : '0' ?></span>
    <span style="padding-left: 20px;">已用金额(元)：<?= isset($CouponUsed) ? StringUtils::amountFormat2($CouponUsed) : '0' ?></span>

</div>
<div class="portlet-body">

    <table class="table">
        <tr>

            <td>
                <span class="title">状态</span>
                <select name="isUsed" id="coupon_search_form_type" m-wrap span6>
                    <option value="">---全部---</option>
                    <option value="1" <?= ($isUsed === '1') ? "selected='selected'" : "" ?> >未使用</option>
                    <option value="2" <?= ($isUsed === '2') ? "selected='selected'" : "" ?> >已使用</option>
                    <option value="3" <?= ($isUsed === '3') ? "selected='selected'" : "" ?> >已过期</option>
                </select>
            </td>
            <td>
                <span class="title">券类型</span>
                <select name="type" class="m-wap" id="type">
                    <option value="">--全部---</option>
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
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'header' => '序号',
        ],
        [
            'label' => '名称',
            'value' => function ($data) {
                return $data->couponType->name;
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
            'label' => '发券操作员',
            'format' => 'html',
            'value' => function ($data) {
                return $data->admin_id ? $data->admin->real_name : '---';
            },
        ],
    ],
])
?>
<script>

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

