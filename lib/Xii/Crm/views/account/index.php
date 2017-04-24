<?php

$this->title = '客户列表';
$this->params['breadcrumbs'][] = ['label' => '客户列表', 'url' => '/cms/account'];

use yii\grid\GridView;
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => '{items}',
    'columns' => [
        [
            'attribute' => '姓名',
            'value' => function ($model) {
                return $model->user
            }
        ],
        [
            'attribute' => '手机',
            'value' => function ($model) {
            }
        ],
        [
            'attribute' => '固定电话',
            'value' => function ($model) {
            }
        ],
        [
            'attribute' => '年龄',
            'value' => function ($model) {
            }
        ],
        [
            'attribute' => '性别',
            'value' => function ($model) {
            }
        ],
        [
            'attribute' => '投资次数',
            'value' => function ($model) {
            }
        ],
        [
            'attribute' => '累计投资金额',
            'value' => function ($model) {

            }
        ],
        [
            'attribute' => '累计年化金额',
            'value' => function ($model) {

            }
        ],
        [
            'attribute' => '理财资产',
            'value' => function ($model) {

            }
        ],
        [
            'attribute' => '账户余额',
            'value' => function ($model) {

            }
        ],
    ]
]) ?>
