<?php
$this->params['breadcrumbs'][] = ['label' => '客户列表', 'url' => '/crm/account'];
$this->params['breadcrumbs'][] = ['label' => '客服记录', 'url' => '/crm/activity/index?accountId=' . $account->id];

$this->title = '客服记录';
?>
<div class="row">
    <h3>客户信息</h3>
    <dl class="dl-horizontal">
        <dt>默认联系方式</dt>
        <dd><?= !is_null($account->primaryContact) ? \yii\helpers\Html::encode($account->primaryContact->obfsNumber) : '---'?></dd>
    </dl>
</div>
<div class="row">
    <?php $form = \yii\widgets\ActiveForm::begin(['options' => ['id' => 'note_form']]) ?>
    <?= $form->field($model, 'content')->textarea() ?>
    <?= \yii\helpers\Html::submitButton('添加', ['class' => 'btn btn-primary', 'id' => 'note_submit']) ?>

    <?php $form->end() ?>
</div>
<div class="row">
    <h3>客服记录</h3>
    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{summary}{items}<div class="pagination"><center>{pager}</center></div>',
        'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover'],
        'columns'  => [
            'createTime',
            'content',
        ]
    ])?>
</div>

<script>
    $('#note_form').on('beforeSubmit', function(e){
        $('#note_submit').attr('disabled', true);
    });
</script>