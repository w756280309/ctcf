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
                    <a href="/product/issuer/add" class="btn green float-right">
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
                        <th class="span4">发行方名称</th>
                        <th>视频名称</th>
                        <th>是否首页显示</th>
                        <th>是否PC端显示</th>
                        <th>排序值</th>
                        <th><center>操作</center></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($issuers as $issuer) : ?>
                            <tr>
                                <td><?= $issuer->name ?></td>
                                <td><?= empty($issuer->mediaTitle) ? '---' : $issuer->mediaTitle ?></td>
                                <td><?= $issuer->isShow ? '是' : '否' ?></td>
                                <td><?= $issuer->allowShowOnPc ? '是' : '否' ?></td>
                                <td><?= null !== $issuer->sort ? $issuer->sort : '无' ?></td>
                                <td>
                                    <center>
                                        <a href="/product/issuer/edit?id=<?= $issuer->id ?>" class="btn mini green"><i class="icon-edit"></i>编辑</a>
                                        <a href="/product/choice/edit?id=<?= $issuer->id ?>" class="btn mini green"><i class="icon-edit"></i>首页精选项目管理</a>
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