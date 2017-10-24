<?php

use yii\widgets\LinkPager;
use yii\grid\GridView;

?>

<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                用户列表
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/user/user/listt">会员管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/user/user/listt">投资会员</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">用户列表</a>
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
                    'attribute' => 'usercode',
                    'label' => '会员ID',
                    'value' => function ($model) {
                        return $model['usercode'];
                    }
                ],
                [
                    'attribute' => 'mobile',
                    'label' => '手机号',
                    'value' => function ($model) {
                        return \common\utils\SecurityUtils::decrypt($model['safeMobile']);
                    }
                ],
                [
                    'attribute' => 'real_name',
                    'label' => '真实姓名',
                    'format' => 'html',
                    'value' => function ($model) {
                        return '<a href ="/user/user/detail?id='.$model['id'].'&type='.\common\models\user\User::USER_TYPE_PERSONAL.'">'.($model['real_name'] ?: '---').'</a>';
                    }
                ],
                [
                    'attribute' => 'createTime',
                    'label' => '注册时间',
                    'value' => function ($model) {
                        return $model['createTime'];
                    }
                ],
                [
                    'attribute' => 'available_balance',
                    'label' => '可用余额（元）',
                    'contentOptions' => ['class' => 'money'],
                    'value' => function ($model) {
                        return $model['available_balance'];
                    }
                ],
            ],
            'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover']
        ]) ?>
        <div class="pagination" style="text-align:center;clear: both">
            <?= LinkPager::widget(['pagination' => $pages]); ?>
        </div>
    </div>

<?php $this->endBlock(); ?>