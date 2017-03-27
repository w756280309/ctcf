<?php
$columns = [];
foreach ($attributes as $attr) {
    $columns[] = [
        'attribute' => $attr,
        'value' => function($model) use ($attr) {
            return $model->$attr;
        }
    ];
}
$columns[] = [
    'header' => '备注',
    'value' => function ($model) {
        if($model->hasErrors()) {
            return current($model->getFirstErrors()) . '[异常数据]';
        } else {
            return '正常';
        }
    }
]
?>
<div class="portlet-body">
    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{summary}{items}<div class="pagination"><center>{pager}</center></div>',
        'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover'],
        'columns' => $columns,
    ]) ?>
</div>