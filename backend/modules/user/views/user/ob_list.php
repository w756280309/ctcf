<?php

use yii\widgets\LinkPager;

$this->title = '底层融资方管理';
?>

<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <h3 class="page-title">
                    底层融资方管理 <small>会员管理模块</small>
                    <a href="/user/user/addob" class="btn green float-right">
                        <i class="icon-plus"></i> 添加底层融资方
                    </a>
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/user/user/listt">会员管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/user/user/listob">底层融资方管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0);">底层融资方列表</a>
                    </li>
                </ul>

            </div>

            <div class="portlet-body">
                <table class="table table-striped table-bordered table-advance table-hover">
                    <thead>
                    <tr>
                        <th class="span4">底层融资方名称</th>
                        <th><center>操作</center></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($model as $key => $val) : ?>
                        <tr>
                            <td><?= $val->name ?></td>
                            <td>
                                <center>
                                    <a href="/user/user/editob?id=<?= $val->id ?>" class="btn mini green"><i class="icon-edit"></i>编辑</a>
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
