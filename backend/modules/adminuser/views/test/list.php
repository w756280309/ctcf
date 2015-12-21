<?php

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

$menus = AuthSys::getMenus('S1003000');

$list_edit = AuthSys::checkMenus($menus, "S1", "003", AuthSys::OP_TYPE_LIST, AuthSys::LIST_RULE_EDIT);   //编辑
$list_freeze = AuthSys::checkMenus($menus, "S1", "003", AuthSys::OP_TYPE_LIST, AuthSys::LIST_RULE_FREEZE);  //冻结激活
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">            
                <h3 class="page-title">
                        系统管理 <small>系统管理模块【主要包含管理员、权限、角色】</small>
                        <a href="/adminuser/test/edit" id="sample_editable_1_new" class="btn green" style="float: right;">
                        新增权限 <i class="icon-plus"></i>
                        </a>
                </h3>
                <ul class="breadcrumb">
                        <li>
                                <i class="icon-home"></i>
                                <a href="/system/">系统管理</a> 
                                <i class="icon-angle-right"></i>
                        </li>
                        <li>
                                <a href="/adminuser/test/list">测试用户管理</a>
                                <i class="icon-angle-right"></i>
                        </li>    
                </ul>
        </div>
        

        <!--search end -->
        <div class="portlet-body">
                <table class="table table-striped table-bordered table-advance table-hover">
                        <thead>
                                <tr>
                                        <th>ID</th>
                                        <th>测试用户名</th>
                                        <th>手机号码</th>
                                        <th>登陆时间</th>
                                        <th>状态</th>
                                        <th>创建时间</th>
                                        <th style="text-align: center">操作</th>
                                </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($model as $key => $val) : ?>
                                <tr>
                                        <td>
                                             <?= $val['id'] ?>
                                        </td>
                                        <td><?= $val['user'] ?></td>
                                        <td><?= $val['tel'] ?></td>
                                        <td><?= date("Y-m-d H:i:s",$val['login_time']) ?></td>
                                        <td>
                                        <?php if ($val['status'] == 1) { ?>
                                               <font color=green><i class="icon-ok-sign"></i> 正常</font>
                                        <?php } else { ?>
                                               <font color=red><i class="icon-remove"></i> 禁用</font>
                                        <?php } ?>
                                        </td>
                                        <td><?= date("Y-m-d H:i:s",$val['created_at']) ?></td>
                                        <td style="text-align: center">
                                            <a href="/adminuser/test/edit?id=<?= $val['id'] ?>" class="btn mini green"><i class="icon-edit"></i> 编辑</a>
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
            op = $(this).attr('op');
            data_index = $(this).attr('data-index');
            index = $(this).attr('index');
            
            $.get("/adminuser/test/activedo", {op: op, value: data_index, id: index}, function (result) {
//                alert(result,'',1);
            });
        });
    });
</script> 

<?php $this->endBlock(); ?>



