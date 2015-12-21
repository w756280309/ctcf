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

$menus = AuthSys::getMenus('P1003000');

$list_edit = AuthSys::checkMenus($menus, "P1", "003", AuthSys::OP_TYPE_LIST, AuthSys::LIST_RULE_EDIT);   //编辑
$list_del = AuthSys::checkMenus($menus, "P1", "003", AuthSys::OP_TYPE_LIST, AuthSys::LIST_RULE_DEL);  //删除
$list_copy = AuthSys::checkMenus($menus, "P1", "003", AuthSys::OP_TYPE_LIST, AuthSys::LIST_RULE_COPY);  //复制
$list_search = AuthSys::checkMenus($menus, "P1", "003", AuthSys::OP_TYPE_LIST, AuthSys::LIST_RULE_SEARCH);  //产品管理——投资记录查看

$btn_add = AuthSys::checkMenus($menus, "P1", "003", AuthSys::OP_TYPE_PAGE, AuthSys::LIST_RULE_ADD);  //添加
$btn_batch_del = AuthSys::checkMenus($menus, "P1", "003", AuthSys::OP_TYPE_PAGE, AuthSys::LIST_RULE_BATCH_DEL);  //批量删除
$btn_line_on = AuthSys::checkMenus($menus, "P1", "003", AuthSys::OP_TYPE_PAGE, AuthSys::LIST_RULE_LINE_ON);  //上线
$btn_line_off = AuthSys::checkMenus($menus, "P1", "003", AuthSys::OP_TYPE_PAGE, AuthSys::LIST_RULE_LINE_OFF);  //下线

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js');
?>
<?php $this->beginBlock('blockmain'); ?>
<style type="text/css">

</style>
<div class="page_function">
	<div class="info">
		<h3>特殊资产管理</h3>
		<small>使用以下功能进行标的操作</small> 
	</div>
	<div class="exercise">
		<a href="/product/product/special">特殊资产列表</a>
		<a href="/product/product/specialedit">添加特殊资产</a>
	</div>
</div>
<div class="page_main">
	<div class="page_menu" style="height:75px">
		<form action="/product/product/special" method="get" target="_self">
			<p>&nbsp;&nbsp; 项目名称 ：
				<input type="text" name="title" />
				&nbsp;&nbsp; 项目类型 ：
				<select id="status" name="special_type">
					<?php foreach ($specail_arr as $key => $val) { ?>
						<option value="<?= $key ?>"><?= $val ?></option>
					<?php } ?>
				</select>
				&nbsp;&nbsp; 截止日期 ：
				<input type="text" name="end_time" readonly="readonly" onclick='WdatePicker({dateFmt: "yyyy-MM-dd"});' />  </p><p>
				&nbsp;&nbsp; 项目状态 ：
				<select id="status" name="status">
					<?php foreach ($special_status_arr as $key => $val) { ?>
						<option value="<?= $key ?>"><?= $val ?></option>
					<?php } ?>
				</select>


				&nbsp;&nbsp; 挂牌底价 ：
				<input type="text" name="money" />
				&nbsp;&nbsp; 上线状态 ：
				<select id="status" name="line">
					<option value="0">-请选择-</option>
					<option value="1">下线</option>
					<option value="2">上线</option>
				</select>
				&nbsp;&nbsp;
				<input type="submit" class="button_small" value="搜索" />
			</p>
		</form>
	</div>

	<div class="page_table table_list">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<th width="5%">选择</th>
				<th width="5%">编号</th>
				<th width="15%">产品名称</th>
				<th width="10%">产品类型</th>
				<th width="15%">截至日期</th>
				<th width="10%">挂牌底价</th>
				<th width="10%">项目状态</th>
				<th width="5%">上线状态</th>
				<th width="25%">操作</th>
			</tr>
			<?php
			foreach ($model as $key => $val) {
				?>
				<tr id="del_13">
					<td>
						<input name="s[]" type="checkbox" value="<?= $val['id'] ?>">
					</td>
					<td><?= $val['sn'] ?></td>
					<td><span><a href="<?= \Yii::$app->params['front_url'] . "product/default/detail?id=" . $val['id'] ?>" target="_blank"><?= $val['title'] ?></a></span>
					</td>
					<td><?= $val['special_type_title'] ?></td>
					<td><?= date("Y-m-d H:i", $val['end_time']); ?></td>
					<td><?= $val['money'] ?>元</td>
					<td><?= $special_status_arr[$val['product_status']] ?></td>
					<td style="cursor:pointer" class="ajax_op" op="status" data-index="<?= $val['status'] ?>" index="<?= $val['id'] ?>">
						<?php if ($btn_line_on) { ?>
							<?php
							if ($val['status'] == 1) {
								?>
								<font color=green><b>√</b></font>
								<?php
							} else {
								?>
								<font color=red><b>X</b></font>
								<?php
							}
						}
						?>
					</td>

					<td>
						<?php if ($list_edit) { ?>
							<a href="/product/product/specialedit?id=<?= $val['id'] ?>">修改</a> | 
						<?php } ?>
						<?php if ($list_del) { ?>
							   <a href="/product/product/delete?id=<?= $val['id'] ?>&page=special" onclick='if (!confirm("是否确定删除！"))
		                                           return false;'>删除</a> | 
						   <?php } ?>
						   <?php if ($list_copy) { ?>
							<a href="/product/product/specialedit?copy_id=<?= $val['id'] ?>">复制</a> | 
						<?php } ?>
						<?php if ($$list_search) { ?>
							<a href="/order/order/specialorder?psn=<?= $val['sn'] ?>">投资记录</a>
						<?php } ?>
					</td>
				</tr>
				<?php
			}
			?>   
		</table>
		<?= LinkPager::widget(['pagination' => $pages]); ?>


	</div>
</div>
<div class="page_tool ">
	<div class="function">
		<input type="checkbox" class="selectall " />全选&emsp;
		<?php if ($btn_line_on) { ?>
			<input type="button " data-index="1" class="button_small lineop" value="上线 " />
		<?php } ?>
		<?php if ($btn_line_off) { ?>
			<input type="button " data-index="0"  class="button_small lineop" value="下线 " />
		<?php } ?>
		<?php if ($btn_batch_del) { ?>
			<input type="button " class="button_small delpro" value="删除 " />
		<?php } ?>
<!--              <input type="button " class="button_small " value="导出项目资料EXECl " />-->
	</div>

</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        $('.selectall').bind('click', function () {
            $(".table_list input[type='checkbox']").prop("checked", $(this).is(":checked"));

        })
        $('.lineop').bind('click', function () {
            if (!confirm("确定是否继续此次操作！")) {
                return false;
            }
            var ids = "";
            $(".table_list input[type='checkbox']:checked").each(function () {
                ids += ($(this).val()) + ',';
            });
            ids = (ids.substring(0, ids.length - 1));
            op = $(this).attr('data-index');
            $.get("/product/product/line?ids=" + ids + "&op=" + op, function (result) {
                location.reload();
            });
        })

        $('.delpro').bind('click', function () {
            if (!confirm("是否确定删除！")) {
                return false;
            }
            var ids = "";
            $(".table_list input[type='checkbox']:checked").each(function () {
                ids += ($(this).val()) + ',';
            });
            ids = (ids.substring(0, ids.length - 1));
            op = $(this).attr('data-index');
            $.get("/product/product/delmore?ids=" + ids, function (result) {
                location.reload();
            });
        })

        $('.ajax_op').bind('click', function () {
            op = $(this).attr('op');
            data_index = $(this).attr('data-index');
            index = $(this).attr('index');
            $.get("/product/product/moreop", {op: op, value: data_index, id: index}, function (result) {
                var da = eval(("(" + result + ")"));
                res(da['res'], location.href);

            });
        });
    });</script> 
<?php $this->endBlock(); ?>

