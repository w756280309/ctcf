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
        <div class="portlet-body">
            <table class="table">
                <tbody>
                    <tr>
                        <td colspan="4" style="text-align: right;"><a href="/growth/code/add" class="btn blue" style="width: 100px;">添加兑换码</a></td>
                    </tr>
                </tbody>
            </table>
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