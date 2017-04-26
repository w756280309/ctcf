<?php

use yii\grid\GridView;
use yii\helpers\Html;
use Zii\Asset\Tablesaw\TablesawAsset;

$this->title = '客户列表';

$this->registerCss("
#xc-account-list table {
    width: 100%;
}
#xc-account-list th {
    word-break: keep-all;
}
");

TablesawAsset::register($this);

$this->params['breadcrumbs'][] = ['label' => '客户列表', 'url' => '/crm/account'];
?>
<form id="xc-account-search" class="form-inline" action="/crm/account/index" method="get" target="_self">
    <div class="form-group">
        <label for="">类型</label>
        <select class="form-control" name="isConverted">
            <option value="">全部</option>
            <option value="true" <?= $isConverted ? 'selected="selected"' : '' ?>>注册用户</option>
            <option value="false" <?= false === $isConverted && '' !== $isConverted ? 'selected="selected"' : '' ?>>潜在客户</option>
        </select>
    </div>
    <div class="form-group">
        <label for="">手机</label>
        <input class="form-control" type="text" name="mobile" value="<?= Html::encode(trim(Yii::$app->request->get('mobile'))) ?>">
    </div>
    <div class="form-group">
        <label for="">固定电话</label>
        <input class="form-control" type="text" name="landline" value="<?= Html::encode(trim(Yii::$app->request->get('landline'))) ?>">
    </div>
    <button type='submit' class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> 搜索</button>
</form>

<div style="overflow-x: hidden; margin-top: 1em;">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => [
            'id' => 'xc-account-list',
        ],
        'tableOptions' => [
            'class' => 'tablesaw',
            'data-tablesaw-mode-switch' => true,
            'data-tablesaw-mode' => 'stack',
            'data-tablesaw-mode-exclude' => 'columntoggle',
        ],
        'layout' => '{summary}{items}<div class="pagination">{pager}</div>',
        'formatter' => [
            'class' => 'Xii\\Crm\\TextFu\\Formatter',
        ],
        'columns' => [
            [
                'attribute' => '姓名',
                'headerOptions' => [
                    'data-tablesaw-priority' => 'persist',
                ],
                'format' => 'xii:empty-nice',
                'value' => function ($model) use ($data) {
                    return $data[$model->id]['name'];
                },
            ],
            [
                'attribute' => '手机',
                'format' => 'xii:empty-nice',
                'value' => function ($model) use ($data) {
                    return $data[$model->id]['mobile'];
                },
            ],
            [
                'attribute' => '固定电话',
                'format' => 'xii:empty-nice',
                'value' => function ($model) use ($data) {
                    return $data[$model->id]['landline'];
                },
            ],
            [
                'attribute' => '年龄',
                'format' => 'xii:empty-nice',
                'value' => function ($model) use ($data) {
                    return $data[$model->id]['age'];
                },
            ],
            [
                'attribute' => '性别',
                'format' => 'xii:empty-nice',
                'value' => function ($model) use ($data) {
                    return $data[$model->id]['gender'];
                },
            ],
            [
                'attribute' => '投资次数',
                'value' => function ($model) use ($data) {
                    return $data[$model->id]['investCount'];
                },
            ],
            [
                'attribute' => '累计投资',
                'headerOptions' => [
                    'style' => 'text-align: right;',
                    'scope' => 'col',
                ],
                'format' => 'xii:money',
                'value' => function ($model) use ($data) {
                    return $data[$model->id]['investTotal'];
                },
            ],
            [
                'attribute' => '累计年化',
                'headerOptions' => [
                    'style' => 'text-align: right;',
                ],
                'format' => 'xii:money',
                'value' => function ($model) use ($data) {
                    return $data[$model->id]['annualInvestment'];
                },
            ],
            [
                'attribute' => '理财资产',
                'headerOptions' => [
                    'style' => 'text-align: right;',
                ],
                'format' => 'xii:money',
                'value' => function ($model) use ($data) {
                    return $data[$model->id]['investmentBalance'];
                },
            ],
            [
                'attribute' => '账户余额',
                'headerOptions' => [
                    'style' => 'text-align: right;',
                ],
                'format' => 'xii:money',
                'value' => function ($model) use ($data) {
                    return $data[$model->id]['availableBalance'];
                },
            ],
            [
                'label' => '操作',
                'format' => 'html',
                'value' => function ($model) {
                    return '<a href="/crm/activity/index?accountId='.$model->id.'">查看</a>';
                },
            ],
        ]
    ]) ?>
</div>
