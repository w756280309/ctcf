<?php

use common\utils\StringUtils;
use yii\grid\GridView;
use yii\widgets\LinkPager;

?>

<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
    <div>
        <h3 class="page-title">
            贷款管理 <small>贷款管理模块【主要包含项目的管理以及项目分类管理】</small>
        </h3>

        <ul class="breadcrumb">
            <li>
                <i class="icon-home"></i>
                <a href="/product/productonline/list">贷款管理</a>
                <i class="icon-angle-right"></i>
            </li>
            <li>
                <a href="javascript:void(0);">转让列表</a>
            </li>
        </ul>
    </div>
    <div class="portlet-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '{items}',
            'columns' => [
                [
                    'label' => '姓名',
                    'value' => function ($data) {
                        return $data['user']->real_name;
                    },
                ],
                [
                    'label' => '手机号',
                    'value' => function ($data) {
                        return $data['user']->mobile;
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
                ],
                [
                    'label' => '投资金额',
                    'value' => function ($data) {
                        return StringUtils::amountFormat3($data['amount']);
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
                ],
                [
                    'label' => '投资时间',
                    'value' => function ($data) {
                        return $data['settleTime'];
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
                ],
            ],
            'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover']
        ]) ?>
    </div>
    <div class="pagination" style="text-align:center;clear: both"><?= LinkPager::widget(['pagination' => $pages]); ?></div>
<?php $this->endBlock(); ?>