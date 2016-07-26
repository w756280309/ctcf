<?php
$this->title = '分销商统计';
use yii\grid\GridView;
use yii\helpers\Html;
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                分销商统计
            </h3>
            <a class="btn green btn-block" style="width: 140px;" href="/datatj/datatj/affiliation-export">分销商统计导出</a>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/datatj/datatj/huizongtj">数据统计</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/datatj/datatj/affiliation">分销商统计</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">分销商统计</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="portlet-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '{items} ',
            'columns' => [
                [
                    'attribute' => 'id',
                    'label' => 'ID',
                ],
                [
                    'attribute' => 'name',
                    'label' => '名称',
                    'value' => function ($data) {
                        return Html::encode($data['name']);
                    }
                ],
                [
                    'attribute' => 'uc',
                    'label' => '注册用户数（人）',
                    'value' => function ($data) {
                        return intval($data['uc']);
                    }
                ],
                [
                    'attribute' => 'oc',
                    'label' => '投资用户数（人）',
                    'value' => function ($data) {
                        return intval($data['oc']);
                    }
                ],
                [
                    'attribute' => 'm',
                    'label' => '投资金额（元）',
                    'value' => function ($data) {
                        return number_format($data['m'], 2);
                    }
                ],
            ],
            'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover']
        ]) ?>
        <div class="pagination" style="text-align:center;clear: both">
            <?= \yii\widgets\LinkPager::widget(['pagination' => $pages]); ?>
        </div>
    </div>
    <?php $this->endBlock(); ?>

