<?php
use yii\widgets\LinkPager;

error_reporting(E_ALL ^ E_NOTICE);

use common\models\AuthSys;

$menus = AuthSys::getMenus('A1004000');

$list_edit = AuthSys::checkMenus($menus, "A1", "004", AuthSys::OP_TYPE_LIST, AuthSys::LIST_RULE_EDIT);   //编辑
$list_del = AuthSys::checkMenus($menus, "A1", "004", AuthSys::OP_TYPE_LIST, AuthSys::LIST_RULE_DEL);  //删除
$list_display = AuthSys::checkMenus($menus, "A1", "004", AuthSys::OP_TYPE_LIST, AuthSys::LIST_RULE_DISPLAY);   //显示
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="page_function">
	<div class="info">
		<h3>广告列表</h3>
	</div>
</div>
<div class="tab" id="tab">
	<a class="selected" href="/adv/adv/index">返回分类列表</a>
</div>
<div class="page_main">
	<div class="page_table table_list">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
                        <th width="10%">
			<center>ID</center>
			</th>
			<th width="25%">位置</th>
			<th width="10%">
			<center>描述</center>
			</th>
			<th width="10%">
			<center>图</center>
			</th>
			<th width="15%">
			<center>link</center>
			</th>
			<th width="15%">
			<center>是否显示</center>
			</th>
			<th width="15%">
			<center>操作</center>
			</th>
			</tr>
			<?php foreach ($model as $val) { ?>
				<tr>
					<td>
				<center><?= $val->id ?></center>
				</td>
				<td><?= $pos[$val->pos_id] ?>
				</td>
				<td>
					<?= $val->description ?>
				</td>
				<td>
					<a href="/upload/adv/<?= $val->image ?>" target="_blank"><?= $val->image ?></a>
				</td>
				<td>
					<?= $val->link ?>
				</td>
				<?php if ($list_display) { ?>
					<td style="cursor:pointer" class="ajax_op" op="status" data-index="<?= $val['status'] ?>" index="<?= $val['id'] ?>">
					<?php } else { ?>
					<td>
					<?php } ?>
				<center>
					<?php if ($val->status) { ?>
						<font color=red><b>X</b></font>
					<?php } else { ?>
						<font color=green><b>√</b></font>
					<?php } ?>
				</center>
				</td>
				<td>
				<center>
					<?php if ($list_edit) { ?>
						<a href="/adv/adv/edit?id=<?= $val->id ?>">修改</a> |
					<?php } ?>
					<?php if ($list_del) { ?>
						   <a href="/adv/adv/delete?id=<?= $val->id ?>" onclick='if (!confirm("是否确定删除！"))
		                                       return false;'>删除</a>
					   <?php } ?>
				</center>
				</td>
				</tr>
			<?php } ?>
		</table>
		<?= LinkPager::widget(['pagination' => $pages]); ?>
	</div>
</div>
<script type="text/javascript">
    $(function () {

        $('.ajax_op').bind('click', function () {
            op = $(this).attr('op');
            data_index = $(this).attr('data-index');
            index = $(this).attr('index');
            $.get("/adv/adv/moreop", {op: op, value: data_index, id: index}, function (result) {
                res(result, "/adv/adv/index");
            });
        });

    })
</script>
<?php $this->endBlock(); ?>
