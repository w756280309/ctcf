<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;

$this->title = '应用信息';
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
                     运营管理<small> 应用信息</small>
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/adv/adv/index">运营管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/growth/app-meta/index">应用信息</a>
                    </li>
                </ul>
            </div>

            <div class="portlet-body">
                <table class="table table-striped table-bordered table-advance table-hover">
                    <thead>
                    <tr>
                        <th class="valign-middle">ID</th>
                        <th class="valign-middle">名称</th>
                        <th class="valign-middle">值</th>
                        <th style="width: 10%" class="valign-middle"><center>操作</center></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($meta as $oneMeta) : ?>
                        <tr>
                            <td class="valign-middle"><?= Html::encode($oneMeta->id) ?></td>
                            <td class="valign-middle"><?= Html::encode($oneMeta->name) ?></td>
                            <td class="valign-middle"><?= Html::encode($oneMeta->value) ?></td>
                            <td class="valign-middle">
                                <center>
                                    <a href="/growth/app-meta/edit?id=<?= $oneMeta->id ?>" class="btn mini green"><i class="icon-edit"></i>编辑</a>
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