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
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
       <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">            
                <h3 class="page-title">
                        系统管理 <small>系统管理模块【主要包含管理员、权限、角色】</small>
                        <a href="/adminuser/admin/edit" id="sample_editable_1_new" class="btn green" style="float: right;">
                        新增管理员 <i class="icon-plus"></i>
                        </a>
                </h3>
                <ul class="breadcrumb">
                        <li>
                                <i class="icon-home"></i>
                                <a href="/adminuser/admin/list">系统管理</a> 
                                <i class="icon-angle-right"></i>
                        </li>
                        <li>
                                <a href="/adminuser/admin/list">管理员管理</a>
                                <i class="icon-angle-right"></i>
                        </li>    
                        <li>
                                <a href="javascript:void(0);">管理员列表</a>
                        </li>    
                </ul>
        </div>
        
    <!--search end -->
        <div class="portlet-body">
                <table class="table table-striped table-bordered table-advance table-hover">
			<thead>
                                <tr>
                                        <th>ID</th>
                                        <th>管理员用户名</th>
                                        <th>管理员姓名</th>
                                        <th>管理员Email</th>
                                        <th style="text-align: center">操作</th>
                                </tr>
                        </thead>
                        <tbody>
			<?php
			foreach ($model as $key => $val) {
				?>
				<tr>
                                    <td><?= $val['id'] ?></td>
                                    <td>
                                        <a href="#" target="_blank"><?= $val['username'] ?></a>
                                    </td>
                                    <td>
                                        <?= $val['real_name'] ?>
                                    </td>
                                    <td>
                                        <b><?= $val['email'] ?></b>
                                    </td>
                                    <td style="text-align: center">
                                        <a href="/adminuser/admin/edit?id=<?= $val['id'] ?>" class="btn mini green"><i class="icon-edit"></i> 编辑</a>
                                            <?php if ($val['status'] == 1) { ?>
                                                    <a href="javascript:void(0);" class="btn mini red ajax_op" op="status" data-index="<?= $val['status'] ?>" index="<?= $val['id'] ?>"><i class="icon-minus-sign"></i>
                                                    禁用
                                            <?php } else { ?>
                                                    <a href="javascript:void(0);" class="btn mini green ajax_op" op="status" data-index="<?= $val['status'] ?>" index="<?= $val['id'] ?>"><i class="icon-ok"></i>
                                                    使用
                                            <?php } ?>
                                        </a>
                                    </td>
				</tr>
				<?php
			}
			?>
			
                        </tbody>
		</table>
            <div class="pagination" style="text-align:center"><?=  LinkPager::widget(['pagination' => $pages]); ?></div>
        </div>
</div>
       
       <script type="text/javascript">
    jQuery(document).ready(function () {
        $('.ajax_op').bind('click', function () {
            openLoading()
            op = $(this).attr('op');
            data_index = $(this).attr('data-index');
            index = $(this).attr('index');
            
            $.get("/adminuser/admin/activedo", {op: op, value: data_index, id: index}, function (result) {
                cloaseLoading()
                newalert(result,'',1);
            });
            return false;
        });
    });
</script> 
<?php $this->endBlock(); ?>

