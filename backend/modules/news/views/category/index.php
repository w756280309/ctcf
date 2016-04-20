<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                分类管理
                <small>运营模块</small>
                <a href="/news/category/edit" id="sample_editable_1_new" class="btn green" style="float: right;">
                    添加分类 <i class="icon-plus"></i>
                </a>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/news/category/index">分类管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">分类列表</a>
                </li>
            </ul>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-bordered table-advance table-hover">
                <thead>
                <tr>
                    <th style="text-align: center">ID</th>
                    <th style="text-align: center">类型</th>
                    <th style="text-align: center">名称</th>
                    <th style="text-align: center">父类</th>
                    <th style="text-align: center">描述</th>
                    <th style="text-align: center">状态</th>
                    <th style="text-align: center">排序</th>
                    <th style="text-align: center">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($models as $key => $val) : ?>
                    <tr>
                        <td style="text-align: center">
                            <?= $val['id'] ?>
                        </td>
                        <td style="text-align: center"><?= $val->getTypeName() ?></td>
                        <td style="text-align: center">
                            <a href="/news/category/edit?id=<?= $val['id'] ?>"><?= $val['name'] ?></a>
                        </td>
                        <td style="text-align: center"><?= $val->getParentName() ?></td>
                        <td style="text-align: center">
                            <?= $val['description'] ?>
                        </td>
                        <td style="text-align: center">
                            <?php if ($val['status'] === \common\models\Category::STATUS_ACTIVE): ?>
                                <i class="icon-ok green" style="color: green;"></i>
                            <?php else: ?>
                                <i class="icon-remove" style="color: red;"></i>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center"><?= $val['sort'] ?></td>
                        <td style="text-align: center">
                            <a href="/news/category/edit?id=<?= $val['id'] ?>" class="btn mini purple"><i
                                    class="icon-edit"></i> 编辑</a>
                            <a href="/news/category/delete?id=<?= $val['id'] ?>"
                               onclick="javascript:return confirm('确定要删除吗？');" class="btn mini black"><i
                                    class="icon-trash"></i> 删除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>
<?php $this->endBlock(); ?>

