<?php
    use yii\helpers\Html;
    use Xii\Crm\Model\Activity;

$this->title = '客服记录';

$this->params['breadcrumbs'][] = ['label' => '客户列表', 'url' => '/crm/account'];
$this->params['breadcrumbs'][] = ['label' => '客服记录', 'url' => '/crm/activity/index?accountId=' . $account->id];
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">客户信息</div>
            <div class="panel-body">
               <div class="col-md-6">
                   <dl class="dl-horizontal" style="margin-top: 1em;">
                       <dt>默认联系方式</dt>
                       <dd><?= !is_null($account->primaryContact) ? Html::encode($account->primaryContact->getNumber()) : '---'?></dd>
                   </dl>
                   <dl class="dl-horizontal" style="margin-top: 1em;">
                       <dt>姓名</dt>
                       <dd>
                           <?php
                           $identity = $account->getIdentity();
                           if (!is_null($identity)) {
                               echo Html::encode($identity->getCrmName());
                           } else {
                               echo '---';
                           }
                           ?>
                   </dl>
               </div>

                <div class="col-md-6">
                    <div class="panel-body">
                        <?php $form = \yii\widgets\ActiveForm::begin(['options' => ['id' => 'note_form']]) ?>
                        <?= $form->field($model, 'summary')->textarea() ?>
                        <?= \yii\helpers\Html::submitButton('添加', ['class' => 'btn btn-primary', 'id' => 'note_submit']) ?>
                        <?php $form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">客户记录</div>
            <div class="panel-body">
                <?= \yii\grid\GridView::widget([
                    'options' => ['style' => 'margin-top: 1em;'],
                    'dataProvider' => $dataProvider,
                    'layout' => '{summary}{items}<div class="pagination"><center>{pager}</center></div>',
                    'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover'],
                    'formatter' => [
                        'class' => 'Xii\\Crm\\TextFu\\Formatter',
                    ],
                    'columns'  => [
                        [
                            'label' => '类型',
                            'value' => function ($model) {
                                if ($model->type === Activity::TYPE_PHONE_CALL) {
                                    return '客服电话';
                                } elseif($model->type === Activity::TYPE_NOTE) {
                                    return '备注';
                                } elseif($model->type === Activity::TYPE_RECEPTION) {
                                    return '门店接待';
                                } else {
                                    return '---';
                                }
                            }
                        ],
                        'createTime',
                        [
                            'label' => '内容',
                            'format' => 'xii:empty-nice',
                            'value' => function ($model) {
                                return $model->content;
                            },
                        ],
                        'summary',
                    ]
                ])?>

            </div>
        </div>
    </div>
</div>



<script>
    $('#note_form').on('beforeSubmit', function(e){
        $('#note_submit').attr('disabled', true);
    });
</script>
