<?php

use Xii\Crm\Model\Activity;
use Xii\Crm\Model\Note;
use Xii\Crm\Model\PhoneCall;
use Xii\Crm\Model\Visit;
use yii\helpers\Html;

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
                        <?= $form->field($model, 'content')->textarea() ?>
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
                            'format' => 'xii:empty-nice',
                            'value' => function ($model) {
                                switch ($model->ref_type) {
                                    case Activity::TYPE_NOTE:
                                        $name = '备注';break;
                                    case Activity::TYPE_PHONE_CALL:
                                        $name = '电话';break;
                                    case Activity::TYPE_BRANCH_VISIT:
                                        $name = '门店';break;
                                    default:
                                        $name = null;
                                }
                                return  $name;
                            }
                        ],
                        'createTime',
                        [
                            'label' => '内容',
                            'format' => 'xii:empty-nice',
                            'value' => function ($model) {
                                $obj = $model->getRefObj();
                                if (is_null($obj)) {
                                    return null;
                                }
                                if ($model->ref_type === Activity::TYPE_NOTE) {
                                    return $obj->content;
                                } elseif(in_array($model->ref_type, [Activity::TYPE_PHONE_CALL, Activity::TYPE_BRANCH_VISIT])) {
                                     if(empty($obj->content)) {
                                         return $obj->comment;
                                     } else {
                                         return $obj->content . '<hr/>' . $obj->comment;
                                     }
                                } else {
                                    return null;
                                }
                            },
                        ],
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
