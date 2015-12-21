<?php

use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
/**
 * Created by zhy.
 * User: al
 * Date: 15-3-17
 * Time: 下午6:25
 */
/* @var $categories */
/* @var $this yii\web\View */
use common\models\AuthSys;

$menus = AuthSys::getMenus('S1001000');

?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">            
                <h3 class="page-title">
                        系统管理 <small>系统管理模块【主要包含管理员、权限、角色】</small>
                        <a href="/system/role/edit" id="sample_editable_1_new" class="btn green" style="float: right;">
                        新增角色 <i class="icon-plus"></i>
                        </a>
                </h3>
                <ul class="breadcrumb">
                        <li>
                                <i class="icon-home"></i>
                                <a href="/system/role/list">系统管理</a> 
                                <i class="icon-angle-right"></i>
                        </li>
                        <li>
                                <a href="/system/role/list">角色管理</a>
                                <i class="icon-angle-right"></i>
                        </li>    
                        <li>
                                <a href="javascript:void(0)">角色列表</a>
                        </li>    
                </ul>
        </div>
        
        <!--search end -->
        <div class="portlet-body">
                <table class="table table-striped table-bordered table-advance table-hover">
                        <thead>
                                <tr>
                                        <th>ID</th>
                                        <th>编号</th>
                                        <th>角色名称</th>
                                        <th>权限说明</th>
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
                                        <td><?= $val['sn'] ?></td>
                                        <td><?= $val['role_name'] ?></td>
                                        <td><?= $val['role_description'] ?></td>
                                        <td>
                                        <?php if ($val['status'] == 1) { ?>
                                               <font color=green><i class="icon-ok-sign"></i> 正常</font>
                                        <?php } else { ?>
                                               <font color=red><i class="icon-remove"></i> 禁用</font>
                                        <?php } ?>
                                        </td>
                                        <td style="text-align: center">
                                            <a href="/system/role/edit?id=<?= $val['id'] ?>" class="btn mini green"><i class="icon-edit"></i> 编辑</a>
                                             |
                                             
                                             
                                                    <?php if ($val['status'] == 1) { ?>
                                                            <a href="" class="btn mini red ajax_op" op="status" data-index="<?= $val['status'] ?>" index="<?= $val['id'] ?>"><i class="icon-minus-sign"></i>
                                                            禁用
                                                    <?php } else { ?>
                                                            <a href="" class="btn mini green ajax_op" op="status" data-index="<?= $val['status'] ?>" index="<?= $val['id'] ?>"><i class="icon-ok"></i>
                                                            使用
                                                    <?php } ?>
                                                    </a>
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