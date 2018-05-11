<?php

use common\models\code\GoodsType;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\grid\GridView;

$this->title = '商品列表';

?>

<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                兑换码管理 <small>运营模块</small>
                <a href="/growth/code/add" id="sample_editable_1_new" class="btn green" style="float: right;">
                    添加兑换码 <i class="icon-plus"></i>
                </a>
                <a href="/growth/code/goods-add" id="sample_editable_1_new" class="btn blue" style="float: right;">
                    添加商品 <i class="icon-plus"></i>
                </a>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/growth/code/goods-list">商品列表</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="#">商品列表</a>
                </li>
            </ul>
        </div>
        <!--search start-->
        <div class="portlet-body">
            <form action="" id="search_form" method="get" target="_self">
                <table class="table">
                    <tbody>
                        <tr>
                            <td><span class="title">商品名称</span></td>
                            <td>
                                <input type="text" class="m-wrap" style="margin-bottom: 0px"
                                    id="goods_name" name='goods_name'
                               value="<?= isset($request['goods_name']) ? Html::encode($request['goods_name']) : '' ?>"
                                    placeholder="请输入商品名称">
                            </td>
                            <td><span class="title">兑换码</span></td>
                            <td>
                                <input type="text" class="m-wrap" style="margin-bottom: 0px"
                                   id="code" name='code'
                                   value="<?= isset($request['code']) ? Html::encode($request['code']) : '' ?>"
                                   placeholder="请输入兑换码">
                            </td>
                            <td>
                                <div class="search-btn" align="right">
                                    <button type='submit' class="btn blue btn-block button-search">搜索
                                        <i class="m-icon-swapright m-icon-white"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <!--search end -->
            <div class="portlet-body">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{items}',
                    'columns' => [
                        [
                            'label' => 'id',
                            'value' => function ($data) {
                                return $data['id'];
                            }
                        ],
                        [
                            'label' => '商品名称',
                            'value' => function ($data) {
                                return $data['name'];
                            },
                        ],
                        [
                            'label' => '商品编号',
                            'value' => function ($data) {
                                return (int) $data['type'] === GoodsType::TYPE_COUPON ? GoodsType::getSnForDuiBa($data['sn']) : $data['sn'];
                            },
                            'contentOptions' => ['class' => 'left'],
                            'headerOptions' => ['class' => 'left'],
                        ],
                        [
                            'label' => '注册码数量',
                            'value' => function ($data) {
                                return $data['total'] > 0 ? $data['total'] : 0;
                            },
                            'contentOptions' => ['class' => 'left'],
                            'headerOptions' => ['class' => 'left'],
                        ],
                        [
                            'label' => '创建时间',
                            'value' => function ($data) {
                                return $data['createdAt'];
                            },
                            'contentOptions' => ['class' => 'left'],
                            'headerOptions' => ['class' => 'left'],
                        ],
                        [
                            'label' => '操作',
                            'format' => 'html',
                            'value' => function ($data) {
                                return '<a href="/growth/code/list?sn=' . $data['sn'] .'" class="btn mini green">
                                    <i class="icon-edit"></i>查看兑换码列表</a>';
                            },
                            'contentOptions' => ['class' => 'left'],
                            'headerOptions' => ['class' => 'left'],
                        ],

                    ],
                    'tableOptions' => ['class' => 'table table-striped table-bordered table-advance table-hover']
                ]) ?>
            </div>
        <div class="pagination" style="text-align:center;">
            <?= LinkPager::widget(['pagination' => $pages]); ?>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('.search-btn').bind('click', function (e) {
            e.preventDefault();

            var code = $('#code').val();
            var goodsName = $('#goods_name').val();

            var action = '';
            if ('' === goodsName && '' !== code) {
                action = '/growth/code/list';
            }

            $('#search_form').attr('action', action);
            $('#search_form').submit();
        });
    });
</script>
<?php $this->endBlock(); ?>