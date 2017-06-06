<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;

$this->title = '渠道管理';
?>

<?php $this->beginBlock('blockmain'); ?>
    <style>
        .valign-middle {
            vertical-align: middle !important;
        }
    </style>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <h3 class="page-title">
                     运营管理<small> 渠道管理</small>
                    <a href="add" class="btn green float-right">
                        <i class="icon-plus"></i> 新增渠道
                    </a>
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/adv/adv/index">运营管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/growth/referral">渠道管理</a>
                    </li>
                </ul>
            </div>
            <!--search start-->
            <div class="portlet-body">
                <form action="/growth/referral" method="get" target="_self" id="loanFilter">
                    <table class="table">
                        <tbody>
                        <tr>
                            <td><span class="title">名称</span></td>
                            <td><input id="name" type="text" class="m-wrap span6" style="margin-bottom: 0px;width:300px" name='name' value="<?= Yii::$app->request->get('title') ?>" /></td>
                            <td><span class="title">渠道码</span></td>
                            <td><input id="code" type="text" class="m-wrap span6" style="margin-bottom: 0px;width:200px" name='code' value="<?= Yii::$app->request->get('sn') ?>" /></td>
                            <td>
                                <div align="right" style="margin-right: 20px">
                                    <button type='submit' class="btn blue" style="width: 100px;">查询 <i class="m-icon-swapright m-icon-white"></i></button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <!--search end -->
            <div class="portlet-body">
                <?= \yii\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{summary}{items}<div class="pagination"><center>{pager}</center></div>',
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'value' => $model->id,
                        ],
                        [
                            'attribute' => 'name',
                            'value' => function ($model) {
                                return $model->name ? $model->name : "---";
                            }
                        ],
                        [
                            'attribute' => 'code',
                            'value' => function ($model) {
                                return $model->code;
                            }
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => '操作',
                            'template' => '{edit} {delete}',//只需要展示删除和更新
                            'headerOptions' => ['width' => '140'],
                            'buttons' => [
                                'edit' => function($url, $model){
                                    return Html::a('编辑',
                                        ['edit', 'id' => $model->id],
                                        [
                                            'class' => 'btn mini green',
                                        ]
                                    );
                                },
                                'delete' => function($url, $model){
                                    return Html::a('删除',
                                        ['delete', 'id' => $model->id],
                                        [
                                            'class' => 'btn mini red',
                                            'data' => ['confirm' => '你确定要删除吗？',]
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