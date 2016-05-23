<?php
use yii\helpers\Html;

$this->title = '活动';
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                运营管理
                <small>活动管理</small>
                <a href="/adv/ranking/create" id="sample_editable_1_new" class="btn green" style="float: right;">
                    添加活动 <i class="icon-plus"></i>
                </a>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/adv/ranking/index">活动管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">活动列表</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="portlet-body">
                <?= \yii\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{items}',
                    'columns' => [
                        'id',
                        [
                            'header' => 'html',
                            'content' => function ($model) {
                                return Html::encode($model->title);
                            },
                            'contentOptions' => ['style' => 'width:40%;']
                        ],
                        [
                            'attribute' => 'startAt',
                            'value' => function ($model) {
                                return date('Y-m-d H:i:s', $model->startAt);
                            }
                        ],
                        [
                            'attribute' => 'endAt',
                            'value' => function ($model) {
                                return date('Y-m-d H:i:s', $model->endAt);
                            }
                        ],
                        [
                            'label' => '操作',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $html = '<a href="/adv/ranking/update?id=' . $model->id . '" class="btn mini green ajax_op"><i class="icon-edit"></i>编辑</a> |';
                                $html .= " <button onclick=\"if(confirm('确认删除')){window.location.href='/adv/ranking/delete?id=" . $model->id . "'}\" class=\"btn mini red ajax_op\">删除</button> ";
                                return $html;
                            }
                        ],
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>
<?php $this->endBlock(); ?>
