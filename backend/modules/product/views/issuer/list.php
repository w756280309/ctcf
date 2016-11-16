<?php

use yii\widgets\LinkPager;

$this->title = '发行方管理';

?>

<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <h3 class="page-title">
                    发行方管理 <small>运营模块</small>
                    <a href="javascript:openwin('/product/issuer/add', 500, 300)" class="btn green float-right">
                        <i class="icon-plus"></i> 添加发行方
                    </a>
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/adv/adv/index">运营管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/product/issuer/list">发行方管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0);">发行方列表</a>
                    </li>
                </ul>
            </div>

            <div class="portlet-body">
                <table class="table table-striped table-bordered table-advance table-hover">
                    <thead>
                    <tr>
                        <th>发行方名称</th>
                        <th><center>操作</center></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($issuers as $issuer) : ?>
                            <tr>
                                <td><?= $issuer->name ?></td>
                                <td>
                                    <center>
                                        <a href="javascript:openwin('/product/issuer/edit?id=<?= $issuer->id ?>', 500, 300)" class="btn mini green"><i class="icon-edit"></i>编辑</a>&nbsp;
                                        <a href="javascript:openwin('/product/issuer/media-edit?id=<?= $issuer->id ?>', 500, 300)" class="btn mini green"><i class="icon-edit"></i><?= $issuer->mediaUri ? '编辑' : '添加' ?>视频</a>
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