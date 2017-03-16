<?php

$this->title = '商家列表';

use yii\grid\GridView;
use yii\widgets\LinkPager;

?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                运营管理 <small>O2O商家管理</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/o2o/affiliator/list">商家列表</a>
                </li>
            </ul>
        </div>
        <div class="portlet-body">
            <form action="/o2o/affiliator/list" method="get">
                <table class="table search_form">
                    <tr>
                        <td>
                            <span class="title">商家</span>
                            <input type="text" class="m-wrap span4" name="name" value="<?= Yii::$app->request->get('name') ?>" />
                        </td>
                        <td style="text-align: right;">
                            <button class="btn blue btn-block" style="width: 100px;display: inline-block;">
                                查询 <i class="m-icon-swapright m-icon-white"></i>
                            </button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>

        <div class="portlet-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => '{items}',
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'header' => '序号',
                    ],
                    [
                        'label' => '商家',
                        'format' => 'html',
                        'value' => function ($data) {
                            return $data['name'];
                        },
                    ],
                    [
                        'label' => '已发放量',
                        'value' => function ($data) {
                            return $data['usedTotal'];
                        },
                    ],
                    [
                        'label' => '剩余发放量',
                        'value' => function ($data) {
                            return (int) ($data['total'] - $data['usedTotal']);
                        },
                    ],
                    [
                        'label' => '操作',
                        'format' => 'html',
                        'value' => function ($data) {
                            return '<center><a href="/o2o/card/list?affId=' . $data['id'] . '" class="btn mini green ajax_op"><i class="icon-edit"></i>查看兑换码列表</a></center>';
                        },
                        'headerOptions' => ['style' => 'text-align:center'],
                    ],
                ],
                'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover']
            ]) ?>
        </div>
        <div class="pagination" style="text-align:center;clear: both"><?= LinkPager::widget(['pagination' => $pages]); ?></div>
    </div>
</div>
<?php $this->endBlock(); ?>