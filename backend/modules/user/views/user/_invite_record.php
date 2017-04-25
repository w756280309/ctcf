<?php
use common\utils\SecurityUtils;
use yii\grid\GridView;

?>

<div class="float-left">
    <a class="btn green" href="add-invite?userId=<?= $user->id ?>">
        补充邀请关系
    </a>
</div>
<div class="portlet-body">
    <?php
        $inviter = $user->fetchInviter();
        $isInvited = null !== $inviter;
    ?>
    <table class="table">
        <tr>
            <td>
                <strong>邀请人姓名：</strong>
                <?= $isInvited ? '<a href="/user/user/detail?id=' . $inviter->id . '">' . $inviter->real_name . '</a>' : '----' ?>
            </td>
            <td>
                <strong>邀请人手机号：</strong>
                <?= $isInvited ? '<a href="/user/user/detail?id=' . $inviter->id . '">' . SecurityUtils::decrypt($inviter->safeMobile) . '</a>' : '----' ?>
            </td>
            <td width="40%"></td>
        </tr>
    </table>
</div>
<?=
    GridView::widget([
        'id' => 'grid_view_invite',
        'dataProvider' => $dataProvider,
        'layout' => '{items} <center><div class="pagination invite_record_pager">{pager}</div></center>',
        'tableOptions' => ['class' => 'invite_record_list table table-hover table-striped'],
        'columns' => [
            [
                'label' => '姓名',
                'format' => 'html',
                'value' => function ($record) {
                    return '<a href="/user/user/detail?id='.$record->invitee_id.'">'.($record->invitee->real_name ?: '---').'</a>';
                }
            ],
            [
                'label' => '手机号',
                'value' => function ($record) {
                    return SecurityUtils::decrypt($record->invitee->safeMobile);
                }
            ],
            [
                'label' => '可用余额',
                'value' => function ($record) {
                    return number_format($record->invitee->lendAccount->available_balance, 2);
                },
                'contentOptions' => ['class' => 'money'],
                'headerOptions' => ['class' => 'money'],
            ],
            [
                'label' => '充值总额',
                'value' => function ($record) use ($rechargeData) {
                    return isset($rechargeData[$record->invitee_id]) ? number_format($rechargeData[$record->invitee_id]['recharge_sum'], 2) : 0;
                },
                'contentOptions' => ['class' => 'money'],
                'headerOptions' => ['class' => 'money'],
            ],
            [
                'label' => '标的投资总额',
                'value' => function ($record) use ($loanData) {
                    return isset($loanData[$record->invitee_id]) ? number_format($loanData[$record->invitee_id]['loan_sum'], 2) : 0;
                },
                'contentOptions' => ['class' => 'money'],
                'headerOptions' => ['class' => 'money'],
            ],
            [
                'label' => '注册时间',
                'value' => function ($record) {
                    return date('Y-m-d H:i:s', $record->invitee->created_at);
                }
            ],
            [
                'label' => '操作',
                'format' => 'html',
                'value' => function ($record) {
                    return '<a href="/user/user/detail?id='.$record->invitee_id.'">查看详情</a>';
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

