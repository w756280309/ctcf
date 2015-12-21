<?php

use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

error_reporting(E_ALL ^ E_NOTICE);

use common\models\AuthSys;

$menus = AuthSys::getMenus('U1001000');

$list_edit = AuthSys::checkMenus($menus, "U1", "001", AuthSys::OP_TYPE_LIST, AuthSys::LIST_RULE_EDIT);   //编辑
$list_display = AuthSys::checkMenus($menus, "U1", "001", AuthSys::OP_TYPE_LIST, AuthSys::LIST_RULE_DISPLAY);   //显示
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="page_function">
	<div class="info">
		<h3>会员类型</h3>
	</div>
</div>
<div class="tab" id="tab">
	<a class="selected" href="/user/usertype/edit">返回添加会员类型</a>
</div>
<div class="page_main">
	<div class="page_table table_list">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<th width="10%">
			<center>ID</center>
			</th>
			<th width="10%">会员类型名字</th>
			<!--th width="10%">
				<center>创建者</center>
			</th-->
			<th width="10%">
			<center>更新时间</center>
			</th>
			<th width="10%">
			<center>创建时间</center>
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
					<td><center><?= $val->id ?></center></td>
				<td><?= $val->name ?></td>
				<!--td><?= $val->creator_id ?></td-->
				<td style="display:none"><?= $up_at = $val->updated_at ?></td>
				<td><?php
					date_default_timezone_set('PRC');
					echo date("Y-m-d H:i:s", $up_at);
					?>
				</td>
				<td style="display:none"><?= $cr_at = $val->created_at ?></td>
				<td><?php echo date("Y-m-d H:i:s", $cr_at); ?></td>
				<?php if ($list_display) { ?>
					<td style="cursor:pointer" class="ajax_op" op="status" data-index="<?= $val['status'] ?>" index="<?= $val['id'] ?>">
					<?php } else { ?>
					<td>
					<?php } ?>
				<center>
					<?php if ($val->status) { ?>
						<font color=green><b>√</b></font>
					<?php } else { ?>
						<font color=red><b>X</b></font>
					<?php } ?>
				</center>
				</td>
				<td>
				<center>
					<?php if ($list_edit) { ?>
						<a href="/user/usertype/edit?id=<?= $val->id ?>">编辑</a>
					<?php } ?>
				</center>
				</td>
				</tr>
			<?php } ?>
		</table>
	</div>
</div>
<script type="text/javascript">
    $(function () {

        $('.ajax_op').bind('click', function () {
            op = $(this).attr('op');
            data_index = $(this).attr('data-index');
            index = $(this).attr('index');
            $.get("/user/usertype/moreop", {op: op, value: data_index, id: index}, function (result) {
//            if(result){
//                alert('修改成功');
//                location.reload();
//            }else{
//                alert('系统异常');
//            }
                res(result, "/user/usertype/index");
            });
        });
    })
</script>
<?php $this->endBlock(); ?>
