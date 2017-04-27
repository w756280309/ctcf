<?php
$this->title = '客服记录';

$this->params['breadcrumbs'][] = ['label' => '客户列表', 'url' => '/crm/account'];
$this->params['breadcrumbs'][] = ['label' => '客服记录', 'url' => '/crm/activity/index?accountId=' . $account->id];
?>
<ul class="nav nav-tabs">
    <li class="active"><a href="#">客户信息</a></li>
</ul>

<dl class="dl-horizontal" style="margin-top: 1em;">
    <dt>默认联系方式</dt>
    <dd><?= !is_null($account->primaryContact) ? \yii\helpers\Html::encode($account->primaryContact->obfsNumber) : '---'?></dd>
</dl>

<div class="row">
    <div class="col-md-8">
        <?php $form = \yii\widgets\ActiveForm::begin(['options' => ['id' => 'note_form']]) ?>
        <?= $form->field($model, 'content')->textarea() ?>
        <?= \yii\helpers\Html::submitButton('添加备注', ['class' => 'btn btn-primary', 'id' => 'note_submit']) ?>
        <?php $form->end() ?>
    </div>
</div>


<ul class="nav nav-tabs" style="margin-top: 1em;">
    <li class="active"><a href="#">客户记录</a></li>
</ul>

<?= \yii\grid\GridView::widget([
    'options' => ['style' => 'margin-top: 1em;'],
    'dataProvider' => $dataProvider,
    'layout' => '{summary}{items}<div class="pagination"><center>{pager}</center></div>',
    'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover'],
    'columns'  => [
        'createTime',
        'content',
    ]
])?>

<script>
    $('#note_form').on('beforeSubmit', function(e){
        $('#note_submit').attr('disabled', true);
    });
</script>
