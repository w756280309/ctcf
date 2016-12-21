<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;

$this->title = '页面META';
?>

<?php $this->beginBlock('blockmain'); ?>
    <style>
        .valign-middle {
            vertical-align: middle !important;
        }
    </style>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <h3 class="page-title">
                    页面META <small>运营模块</small>
                    <a href="/growth/page-meta/add" class="btn green float-right">
                        <i class="icon-plus"></i> 添加META
                    </a>
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/adv/adv/index">运营管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/growth/page-meta/list">页面META</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0);">META列表</a>
                    </li>
                </ul>
            </div>

            <div class="portlet-body">
                <table class="table table-striped table-bordered table-advance table-hover">
                    <thead>
                    <tr>
                        <th class="valign-middle">别名</th>
                        <th class="valign-middle">链接地址</th>
                        <th class="valign-middle">页面标题</th>
                        <th style="width: 30%">关键词</th>
                        <th style="width: 10%" class="valign-middle"><center>操作</center></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($meta as $oneMeta) : ?>
                        <tr>
                            <td class="valign-middle"><?= $oneMeta->alias ?></td>
                            <td class="valign-middle"><a href="<?= Html::encode($oneMeta->href) ?>" target="_blank"><?= Html::encode($oneMeta->url) ?></a></td>
                            <td class="valign-middle"><?= Html::encode($oneMeta->title) ?></td>
                            <td><?= Html::encode($oneMeta->keywords) ?></td>
                            <td class="valign-middle">
                                <center>
                                    <a href="/growth/page-meta/edit?id=<?= $oneMeta->id ?>" class="btn mini green"><i class="icon-edit"></i>编辑</a>
                                </center>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!--分页-->
            <div class="pagination"><center><?= LinkPager::widget(['pagination' => $pages]) ?></center></div>
        </div>
    </div>
<?php $this->endBlock(); ?>