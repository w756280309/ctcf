<?php

$this->title = '客户列表';
$this->params['breadcrumbs'][] = ['label' => '客户列表', 'url' => '/crm/account'];

use yii\grid\GridView;
?>
<div class="row">
    <?= GridView::widget([
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
                'value' => function ($model) use ($data) {
                    return isset($data[$model->id]['mobile']) ? $data[$model->id]['mobile'] : '--';
                }
            ],
            [
                'attribute' => '固定电话',
                'value' => function ($model) use ($data) {
                    return isset($data[$model->id]['landline']) ? $data[$model->id]['landline'] : '--';
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
        ]
    ]) ?>
</div>