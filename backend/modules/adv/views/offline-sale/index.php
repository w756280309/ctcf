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
                <small>线下投资管理</small>
                <a href="/adv/offline-sale/create" id="sample_editable_1_new" class="btn green" style="float: right;">
                    添加线下投资记录 <i class="icon-plus"></i>
                </a>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/adv/offline-sale/index">线下投资管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">线下投资记录列表</a>
                </li>
            </ul>
        </div>
    </div>
    <h3 style="color: red"><?= Yii::$app->session->getFlash('error') ?></h3>
    <div class="row-fluid">
        <div class="span12">
            <div class="portlet-body">
                <?= \yii\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{items}',
                    'columns' => [
                        'id',
                        [
                            'attribute' => 'rankingPromoOfflineSale_id',
                            'format' => 'html',
                            'value' => function ($model) {
                                return $model->rankingPromo ? $model->rankingPromo->title : '';
                            },
                            'contentOptions' => ['style' => 'width:40%;']
                        ],
                        [
                            'attribute' => 'mobile',
                            'format' => 'html',
                            'value' => function ($model) {
                                return Html::encode($model->mobile);
                            }
                        ],
                        [
                            'attribute' => 'totalInvest',
                            'format' => 'html',
                            'value' => function ($model) {
                                return number_format($model->totalInvest, 2);
                            }
                        ],
                        [
                            'label' => '操作',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $html = '<a href="/adv/offine-sale/update?id=' . $model->id . '" class="btn mini green ajax_op"><i class="icon-edit"></i>编辑</a> |';
                                $html .= " <button onclick=\"if(confirm('确认删除')){window.location.href='/adv/offline-sale/delete?id=" . $model->id . "'}\" class=\"btn mini red ajax_op\">删除</button> ";
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
