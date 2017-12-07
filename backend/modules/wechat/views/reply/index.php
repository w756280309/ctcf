<?php

use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\AuthSys;

$menus = AuthSys::getMenus('A100000');

?>
<?php $this->beginBlock('blockmain'); ?>

    <div class="container-fluid">
        <!-- BEGIN PAGE HEADER-->
        <div class="row-fluid">
            <div class="span12">
                <h3 class="page-title">
                    运营管理 <small>公众号管理模块【主要包含自动回复】</small>
                    <a href="/wechat/reply/edit" id="sample_editable_1_new" class="btn green" style="float: right;">
                        新增 <i class="icon-plus"></i>
                    </a>
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/adv/adv/index">运营管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/wechat/reply/index">公众号管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0)">自动回复列表</a>
                    </li>
                </ul>
            </div>

            <!--search end -->
            <div class="portlet-body">
                <table class="table table-striped table-bordered table-advance table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>类型</th>
                        <th>关键字</th>
                        <th>回复内容</th>
                        <th>状态</th>
                        <th style="text-align: center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($model as $key => $val) : ?>
                        <tr>
                            <td>
                                <?= $val['id'] ?>
                            </td>
                            <td><?= $types[$val['type']] ?></td>
                            <td><?= $val['keyword'] ?></td>
                            <td><?= $val['content'] ?></td>
                            <td>
                                <?= $status[$val['isDel']] ?>
                            </td>
                            <td style="text-align: center">
                                <a href="/wechat/reply/edit?id=<?= $val['id'] ?>" class="btn mini green"><i class="icon-edit"></i> 编辑</a>
                                |
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="pagination" style="text-align:center"><?=  LinkPager::widget(['pagination' => $pages]); ?></div>

            </div>

        </div>

    </div>


    <script type="text/javascript">
        jQuery(document).ready(function () {
            $('.ajax_op').bind('click', function () {
                openLoading()
                op = $(this).attr('op');
                data_index = $(this).attr('data-index');
                index = $(this).attr('index');

                $.get("/system/role/activedo", {op: op, value: data_index, id: index}, function (result) {
                    cloaseLoading()
                    newalert(result,'',1);
                });
            });
        });
    </script>
<?php $this->endBlock(); ?>