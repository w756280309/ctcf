<?php

use yii\grid\GridView;

?>

<div class="float-left">
    <a class="btn green" href="add-invite?userId=<?= $user->id ?>">
        补充邀请关系
    </a>
</div>
<?=
    GridView::widget([
        'id' => 'grid_view_invite',
        'dataProvider' => $dataProvider,
        'layout' => '{items} <center><div class="pagination invite_record_pager">{pager}</div></center>',
        'tableOptions' => ['class' => 'invite_record_list table table-hover table-striped'],
        'columns' => [
            [
                'label' => '邀请关系',
                'value' => function ($record) use ($user) {
                    if ($record->user_id === $user->id) {
                        return '已邀好友';
                    }
                    return '邀请人';
                }
            ],
            [
                'label' => '姓名',
                'format' => 'html',
                'value' => function ($record) use ($userData) {
                    return '<a href="/user/user/detail?id='.$userData[$record->id]->id.'">'.($userData[$record->id]->real_name ?: '---').'</a>';
                }
            ],
            [
                'label' => '手机号',
                'value' => function ($record) use ($userData) {
                    return $userData[$record->id]->mobile;
                }
            ],
            [
                'label' => '可用余额',
                'value' => function ($record) use ($userData) {
                    return number_format($userData[$record->id]->lendAccount->available_balance, 2);
                },
                'contentOptions' => ['class' => 'money'],
                'headerOptions' => ['class' => 'money'],
            ],
            [
                'label' => '充值总额',
                'value' => function ($record) use ($rechargeData, $userData) {
                    $user = $userData[$record->id];
                    return isset($rechargeData[$user->id]) ? number_format($rechargeData[$user->id]['recharge_sum'], 2) : 0;
                },
                'contentOptions' => ['class' => 'money'],
                'headerOptions' => ['class' => 'money'],
            ],
            [
                'label' => '标的投资总额',
                'value' => function ($record) use ($loanData, $userData) {
                    $userId = $userData[$record->id]->id;
                    return isset($loanData[$userId]) ? number_format($loanData[$userId]['loan_sum'], 2) : 0;
                },
                'contentOptions' => ['class' => 'money'],
                'headerOptions' => ['class' => 'money'],
            ],
            [
                'label' => '注册时间',
                'value' => function ($record) use ($userData) {
                    return date('Y-m-d H:i:s', $userData[$record->id]->created_at);
                }
            ],
            [
                'label' => '操作',
                'format' => 'html',
                'value' => function ($record) use ($userData) {
                    return '<a href="/user/user/detail?id='.$userData[$record->id]->id.'">查看详情</a>';
                }
            ],
        ],
    ])
?>

<script>
    $(function() {
        $('.invite_record_pager ul li').on('click', 'a', function(e) {
            e.preventDefault();
            getInviteList($(this).attr('href'));
        })
    })
</script>

