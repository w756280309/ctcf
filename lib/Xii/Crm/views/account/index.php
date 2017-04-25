<?php

use yii\grid\GridView;
use yii\helpers\Html;

$this->title = '客户列表';

$this->registerCss("

");

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
<div class="row">
    <?= GridView::widget([
        'options' => [
            'id' => 'xc-account-list',
        ],
        'dataProvider' => $dataProvider,
        'layout' => '{summary}{items}<div class="pagination" style="text-align:center; clear: both;">{pager}</div>',
        'columns' => [
            [
                'attribute' => '姓名',
                'value' => function ($model) use ($data) {
                    return isset($data[$model->id]['name']) ? $data[$model->id]['name'] : '--';
                }
            ],
            [
                'attribute' => '手机',
                'format' => 'html',
                'value' => function ($model) use ($data) {
                    return '<a href="/crm/activity/index?accountId='.$model->id.'">' . (isset($data[$model->id]['mobile']) ? $data[$model->id]['mobile'] : '--') . '</a>';
                }
            ],
            [
                'attribute' => '固定电话',
                'format' => 'html',
                'value' => function ($model) use ($data) {
                    return '<a href="/crm/activity/index?accountId='.$model->id.'">' . (isset($data[$model->id]['landline']) ? $data[$model->id]['landline'] : '--') . '</a>';
                }
            ],
            [
                'attribute' => '年龄',
                'value' => function ($model) use ($data) {
                    return isset($data[$model->id]['age']) ? $data[$model->id]['age'] : '--';
                }
            ],
            [
                'attribute' => '性别',
                'value' => function ($model) use ($data) {
                    return isset($data[$model->id]['gender']) ? $data[$model->id]['gender'] : '--';
                }
            ],
            [
                'attribute' => '投资次数',
                'value' => function ($model) use ($data) {
                    return isset($data[$model->id]['investCount']) ? $data[$model->id]['investCount'] : '0';
                }
            ],
            [
                'attribute' => '累计投资金额',
                'value' => function ($model) use ($data) {
                    return isset($data[$model->id]['investTotal']) ? $data[$model->id]['investTotal'] : '0.00';
                }
            ],
            [
                'attribute' => '累计年化金额',
                'value' => function ($model) use ($data) {
                    return isset($data[$model->id]['annualInvestment']) ? $data[$model->id]['annualInvestment'] : '0.00';
                }
            ],
            [
                'attribute' => '理财资产',
                'value' => function ($model) use ($data) {
                    return isset($data[$model->id]['investmentBalance']) ? $data[$model->id]['investmentBalance'] : '0.00';
                }
            ],
            [
                'attribute' => '账户余额',
                'value' => function ($model) use ($data) {
                    return isset($data[$model->id]['availableBalance']) ? $data[$model->id]['availableBalance'] : '0.00';
                }
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
