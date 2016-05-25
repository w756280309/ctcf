<?php
use yii\widgets\LinkPager;
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                渠道管理 <small>运营模块</small>
                <a href="/fenxiao/fenxiao/add" id="sample_editable_1_new" class="btn green" style="float: right;">
                    <i class="icon-plus"></i> 添加渠道用户
                </a>
            </h3>
            <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/adv/adv/index">运营管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/fenxiao/fenxiao/list">渠道管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0);">渠道列表</a>
                    </li>
            </ul>
        </div>

        <div class="portlet-body">
            <table class="table table-striped table-bordered table-advance table-hover">
                <thead>
                    <tr>
                        <th>分销商名称</th>
                        <th><center>操作</center></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $key => $val) : ?>
                    <tr>
                        <td><?= $val['name'] ?></td>
                        <td>
                            <center>
                                <a href="/fenxiao/fenxiao/edit?id=<?= $val['id'] ?>" class="btn mini green"><i class="icon-edit"></i> 编辑</a>
                            </center>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!--分页-->
        <div class="pagination" style="text-align:center"><?= LinkPager::widget(['pagination' => $pages]); ?></div>
    </div>
</div>
<?php $this->endBlock(); ?>