<?php

/**
 * Created by zhy.
 * User: al
 * Date: 15-3-17
 * Time: 下午6:25
 */
/* @var $categories */
/* @var $this yii\web\View */
use common\models\AuthSys;

$menus = AuthSys::getMenus('P1001000');

$list_edit = AuthSys::checkMenus($menus, "P1", "001", AuthSys::OP_TYPE_LIST, AuthSys::LIST_RULE_EDIT);  //编辑
$list_del = AuthSys::checkMenus($menus, "P1", "001", AuthSys::OP_TYPE_LIST, AuthSys::LIST_RULE_DEL);  //删除

$btn_add = AuthSys::checkMenus($menus, "P1", "001", AuthSys::OP_TYPE_PAGE, AuthSys::LIST_RULE_ADD);  //添加
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="page_function">
	<div class="info">
		<h3>分类管理</h3>
	</div>
	<div class="exercise">
		<a href="/product/category/index">分类列表</a>
		<a href="/product/category/add">添加分类</a>
		<span style="color: red">预置数据，删除须谨慎</span>
	</div>
</div>
<div class="page_main">
	<div class="page_table table_list">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<th width="10%">
			<center>ID</center>
			</th>
			<th width="25%">分类ID</th>
			<th width="10%">
			<center>分类名称</center>
			</th>
			<th width="15%">
			<center>操作</center>
			</th>
			</tr>

			<?php
			foreach ($model as $key => $val) {
				?>
				<tr>
					<td>
				<center><?= $key ?></center>
				</td>
				<td><a href="#" target="_blank"><?= $val['code'] ?></a>
				</td>
				<td style="text-align: right">
					<font>
					<?= $val['name'] ?>
					</font>

				</td>

				<td>
				<center>
					<?php if ($list_edit) { ?>
						<a href="/product/category/add?id=<?= $key ?>">修改</a>
					<?php } ?>
					|
					<?php if ($list_del) { ?>
						   <a href="/product/category/delete?id=<?= $key ?>" onclick='if (!confirm("是否确定删除！"))
		                                       return false;'>删除</a>
					   <?php } ?>
				</center>
				</td>
				</tr>



				<?php
			}
			?>
		</table>
	</div>
</div>

<?php $this->endBlock(); ?>


