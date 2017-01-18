<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;

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
            <form action="/growth/code/list" method="get" target="_self">
                <table class="table">
                    <tbody>
                        <tr>
                            <td><span class="title">兑换码</span></td>
                            <td>
                                <input type="text" class="m-wrap" style="margin-bottom: 0px" id="title" name='code'
                                       value="" placeholder="请输入兑换码"/>
                            </td>
                            <td>
                                <div class="search-btn" align="right">
                                    <button type='submit' class="btn blue btn-block button-search">搜索 <i class="m-icon-swapright m-icon-white"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <!--search end -->
        <div class="portlet-body">
            <table class="table table-striped table-bordered table-advance table-hover">
                <thead>
                <tr>
                    <th class="valign-middle">商品名称</th>
                    <th class="valign-middle">兑换码数量</th>
                    <th class="valign-middle">创建时间</th>
                    <th style="width: 30%" class="valign-middle"><center>操作</center></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $good) : ?>
                    <tr>
                        <td class="valign-middle"><?= Html::encode($good['name']) ?></td>
                        <td class="valign-middle"><?= $good['total'] ?></td>
                        <td class="valign-middle"><?= $good['createdAt'] ?></td>
                        <td class="valign-middle">
                            <center>
                                <a href="/growth/code/list?sn=<?= $good['sn'] ?>" class="btn mini green"><i class="icon-edit"></i>查看兑换码列表</a>
                                <?php if ($good['total'] > 0) { ?>
                                <a href="/growth/code/export-all?sn=<?= $good['sn'] ?>" class="btn mini green"  style="display: none;"><i class="icon-edit"></i>导出兑换码TXT</a>
                                <?php } ?>
                            </center>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="pagination" style="text-align:center;clear: both">
            <?= LinkPager::widget(['pagination' => $pages]); ?>
        </div>
    </div>
</div>
<?php $this->endBlock(); ?>