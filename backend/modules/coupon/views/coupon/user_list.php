<?php

$this->title = '用户代金券列表';

use common\utils\StringUtils;
use yii\grid\GridView;
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <div class="row-fluid">
        <div>
            <h3 class="page-title">
                用户代金券列表
                <div class="float-right">
                    <a class="btn green" href="javascript:openwin('/coupon/coupon/allow-issue-list?uid=<?= $user->id ?>' , 800, 400)">
                        发放代金券
                    </a>
                    <a class="btn green" href="/user/user/detail?id=<?= $user->id ?>&type=<?= $user->type ?>">
                        返回用户详情页
                    </a>
                </div>
            </h3>
        </div>

        <p>用户名: <?= $user->real_name ? $user->real_name : '---' ?> 手机号: <?= $user->mobile ?></p>
        <div class="portlet-body">
            <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{items} <center><div class="pagination">{pager}</div></center>',
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
                            }
                        ],
                        [
                            'label' => '起投金额(元)',
                            'value' => function ($data) {
                                return StringUtils::amountFormat2($data->couponType->minInvest);
                            }
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
                            'value' => function ($data) {
                                if ($data->isUsed) {
                                    return '已使用';
                                } elseif (date('Y-m-d') > $data->expiryDate) {
                                    return '已过期';
                                } else {
                                    return '未使用';
                                }
                            }
                        ],
                    ],
                ])
            ?>
        </div>
    </div>
</div>
<?php $this->endBlock(); ?>