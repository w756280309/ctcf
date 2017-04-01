<?php

use common\utils\StringUtils;
use yii\helpers\Html;
use yii\grid\GridView;

?>

<?=
GridView::widget([
    'id' => 'grid_view_invite',
    'dataProvider' => $dataProvider,
    'layout' => '{items} <center><div class="pagination invite_record_pager">{pager}</div></center>',
    'tableOptions' => ['class' => 'invite_record_list table table-hover table-striped'],
    'columns' => [
        [
            'label' => '手机号',
            'format' => 'html',
            'value' => 'mobile',
        ],
        [
            'label' => '真实姓名',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::a($model->realName, ['/user/offline/detail', 'id' => $model->id]);
            }
        ],
        [
            'label' => '证件号',
            'format' => 'html',
            'value' => function ($model) {
                return StringUtils::obfsIdCardNo($model->idCard);
            }
        ],
        [
            'label' => '用户等级',
            'format' => 'html',
            'value' => function ($model) {
                return "VIP" . $model->getLevel();
            }
        ],
        [
            'label' => '操作',
            'format' => 'html',
            'value' => function ($model) {
                return "<a href='/user/offline/detail?id=" . $model->id . "' class='btn mini green'><i class='icon-edit'></i> 查看用户详情</a>";
            }
        ],
    ],
])
?>

<script>
    $(function () {
        $('.invite_record_pager ul li').on('click', 'a', function (e) {
            e.preventDefault();
            getInviteList($(this).attr('href'));
        })
    })
</script>

