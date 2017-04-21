<?php

$this->title = '分期列表';
use yii\helpers\Html;
use yii\web\YiiAsset;

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => YiiAsset::class]);
$this->registerCssFile('/vendor/kindeditor/4.1.11/themes/default/default.css');
$this->registerCssFile('/vendor/kindeditor/4.1.11/plugins/code/prettify.css');
$this->registerJsFile('/vendor/kindeditor/4.1.11/kindeditor-all-min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/vendor/kindeditor/4.1.11/lang/zh-CN.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/vendor/kindeditor/4.1.11/plugins/code/prettify.js', ['depends' => 'yii\web\YiiAsset']);
?>

<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
        <!-- BEGIN PAGE HEADER-->
        <div class="row-fluid">
            <div class="span12">
                <h3 class="page-title">
                    线下数据<small> 分期列表</small>
                    <a href="/offline/offline/addrpm?loan_id=<?= Yii::$app->request->get('id')?>" class="btn green float-right">
                        <i class="icon-plus"></i> 新增分期
                    </a>
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/offline/offline/loanlist">标的列表</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="#">分期列表</a>
                        <i class="icon-angle-right"></i>
                    </li>

                </ul>
            </div>
        </div>

        <ul class="nav nav-list"> <li class="divider"></li> </ul>
        <div class="portlet-body">
            <div class="portlet-body">
                <?= \yii\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{summary}{items}<div class="pagination"><center>{pager}</center></div>',
                    'columns' => [

                        [
                            'label' => '产品名称',
                            'attribute' => 'title',
                            'value' => function ($model) {
                                return $model->loan->title ? $model->loan->title : "---";
                            }
                        ],
                        ['attribute' => 'dueDate'],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'template' => '{delete} {edit}',//只需要展示删除和更新
                            'headerOptions' => ['width' => '120'],
                            'buttons' => [

                                'edit' => function($url, $model, $key){
                                    return Html::a('编辑',
                                        ['addrpm', 'id' => $key],
                                        [
                                            'class' => 'btn mini green',
                                        ]
                                    );
                                },
                            ],
                        ],
                    ]
                ]) ?>
            </div>
        </div>
    </div>

<?php $this->endBlock(); ?>