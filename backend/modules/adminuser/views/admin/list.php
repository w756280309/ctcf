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


        <!--search start-->
        <div class="portlet-body">
            <form action="/adminuser/admin/list" method="get" target="_self" id="loanFilter">
                <table class="table">
                    <tbody>
                    <tr>
                        <td><span class="title">管理员用户名</span></td>
                        <td><input  type="text" class="m-wrap span4 " style="margin-bottom: 0px;width:200px" name='username' value="<?= Yii::$app->request->get('username') ?>" /></td>
                        <td><span class="title">管理员姓名</span></td>
                        <td><input  type="text" class="m-wrap span6" style="margin-bottom: 0px;width:200px" name='real_name' value="<?= Yii::$app->request->get('real_name') ?>" /></td>
                        <td>
                            <div align="right" style="margin-right: 20px">
                                <input type="button"  class="btn" value="重置" style="width: 60px;" onclick="location='/adminuser/admin/list'"/>
                                <button type='submit' class="btn blue" style="width: 100px;">查询 <i class="m-icon-swapright m-icon-white"></i></button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
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

