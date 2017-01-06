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
                ],
                [
                    'label' => '项目名称',
                    'value' => function ($data) {
                        return $data['loan']->title;
                    },
                ],
                [
                    'label' => '转让时间',
                    'value' => function ($data) {
                        return $data['createTime'];
                    }
                ],
                [
                    'label' => '发起转让金额',
                    'value' => function ($data) {
                        return StringUtils::amountFormat3($data['amount']);
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
                ],
                [
                    'label' => '折让率',
                    'value' => function ($data) {
                        return StringUtils::amountFormat3($data['discountRate']);
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
                ],
                [
                    'label' => '已转让金额',
                    'value' => function ($data) {
                        return StringUtils::amountFormat3($data['tradedAmount']);
                    },
                    'contentOptions' => ['class' => 'money'],
                    'headerOptions' => ['class' => 'money'],
                ],
                [
                    'label' => '状态',
                    'value' => function ($data) {
                        return $data['isClosed'] ? '转让完成' : '转让中';
                    },
                ],
                [
                    'label' => '操作',
                    'format' => 'html',
                    'value' => function ($data) {
                        return '<a href="/product/productonline/buytransfer?loan_id='.$data['id'].'">查看</a>';
                    }
                ],
            ],
            'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover']
        ]) ?>
    </div>
    <div class="pagination" style="text-align:center;clear: both"><?= LinkPager::widget(['pagination' => $pages]); ?></div>
<?php $this->endBlock(); ?>