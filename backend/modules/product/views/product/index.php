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

$menus = AuthSys::getMenus('P1002000');

$list_edit = AuthSys::checkMenus($menus, "P1", "002", AuthSys::OP_TYPE_LIST, AuthSys::LIST_RULE_EDIT);  //编辑
$list_del = AuthSys::checkMenus($menus, "P1", "002", AuthSys::OP_TYPE_LIST, AuthSys::LIST_RULE_DEL);  //删除
$list_copy = AuthSys::checkMenus($menus, "P1", "002", AuthSys::OP_TYPE_LIST, AuthSys::LIST_RULE_COPY);  //复制

$btn_add = AuthSys::checkMenus($menus, "P1", "002", AuthSys::OP_TYPE_PAGE, AuthSys::LIST_RULE_ADD);  //新加
$btn_batch_bel = AuthSys::checkMenus($menus, "P1", "002", AuthSys::OP_TYPE_PAGE, AuthSys::LIST_RULE_BATCH_DEL);  //批量删除
$btn_line_on = AuthSys::checkMenus($menus, "P1", "002", AuthSys::OP_TYPE_PAGE, AuthSys::LIST_RULE_LINE_ON);  //上线
$btn_line_off = AuthSys::checkMenus($menus, "P1", "002", AuthSys::OP_TYPE_PAGE, AuthSys::LIST_RULE_LINE_OFF);  //下线
?>
<?php $this->beginBlock('blockmain'); //var_dump($btn_add,$btn_line_on,$btn_line_down);        ?>
<style type="text/css">

</style>
<div class="page_function">
	<div class="info">
		<h3>产品管理</h3>
		<small>使用以下功能进行标的操作</small> 
	</div>
	<div class="exercise">
		<a href="/product/product/index">产品列表</a>
		<a href="/product/product/edit">添加产品</a>
                
                <a href="javascript:explort();">导出开鑫贷数据</a>
	</div>
</div>
<script type="text/javascript">
function explort(){
    var ids = "";
            $(".table_list input[type='checkbox']:checked").each(function () {
                ids += ($(this).val()) + ',';
            });
            ids = (ids.substring(0, ids.length - 1));
            window.open('/product/product/explortkai?ids='+ids);
}
</script>
<div class="page_main">
	<div class="page_menu">
		<form action="/product/product/index" method="get" target="_self">
			&nbsp;&nbsp; 贷款分类 ：
			<select id="category" name="category_id">
				<option value="select">请选择</option>
				<?php
				foreach ($cat_data as $key => $val) {
					?>
					<option value="<?= $key ?>" <?php
					if (!empty($view_search) && isset($view_search['category_id'])) {
						if ($key == $view_search['category_id']) {
							echo "selected";
						}
					}
					?>><?= $val['name'] ?></option>
							<?php
						}
						?>
			</select>
			&nbsp;&nbsp; 贷款状态 ：
			<select id="product_status" name="product_status">
				<option value="select">请选择</option>
				<?php
				foreach ($product_status as $key => $val) {
					?>
					<option value='<?= $key ?>' <?php
					if (isset($view_search['product_status'])) {
						if ($key == $view_search['product_status']) {
							echo "selected";
						}
					}
					?>><?= $val ?></option>
							<?php
						}
						?>
			</select>
			&nbsp;&nbsp; 状态 ：
			<select id="status" name="status">
				<option value="select" <?php
				if (empty($view_search) || !isset($view_search['status'])) {
					echo "selected";
				}
				?>>请选择</option>
						<?php
						foreach ($status as $key => $val) {
							?>
					<option value='<?= $key ?>' <?php
					if (isset($view_search['status']) && $key == $view_search['status'] && ($view_search['status'] != "")) {
						echo "selected";
					}
					?>><?= $val ?></option>
							<?php
						}
						?>
			</select>
			&nbsp;&nbsp; 搜索：
			<input type="text" class="text_value" id="search" name='title' value="<?php
			if (isset($view_search['title'])) {
				echo $view_search['title'];
			}
			?>" /> &nbsp;&nbsp;
			<input type="submit" class="button_small" value="搜索" />
		</form>
	</div>

	<div class="page_table table_list">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<th width="5%">选择</th>
				<th width="5%">编号</th>
				<th width="10%">产品类型</th>
				<th width="10%">产品名称</th>
				<th width="10%">年化收益率</th>
				<th width="10%">项目期限</th>
				<th width="10%">借款金额</th>
				<th width="10%">起投金额</th>
				<th width="5%">上线状态</th>
				<th width="10%">项目状态</th>
				<th width="20%">操作</th>
			</tr>
			<?php
			foreach ($model as $key => $val) {
				?>
                        <tr class="exp" id="del_13">
					<td>
						<input name="s[]" type="checkbox" value="<?= $val['id'] ?>">
					</td>
					<td><?= $val['sn'] ?></td>
					<td><?php
						foreach ($cat_data as $k => $v) {
							if ($k == $val['category_id']) {
								echo $v['front_name'];
							}
						}
						?></td>
					<td><span><a href="<?= \Yii::$app->params['front_url'] . "product/default/detail?id=" . $val['id'] ?>" target="_blank"><?= $val['title'] ?></a></span>
					</td>
					<td><?= $val['yield_rate'] ?>%</td>
					<td><?= $val['product_duration'] ?></td>
					<td><?= $val['money'] ?>元</td>
					<td><?= $val['start_money'] ?></td>
					
					<td style="cursor:pointer" class="ajax_op" op="status" data-index="<?= $val['status'] ?>" index="<?= $val['id'] ?>">
						<?php
						if ($val['status'] == 1) {
							?>
							<font color=green><b>√</b></font>
							<?php
						} else {
							?>
							<font color=red><b>X</b></font>
						<?php } ?>
					</td>
					<td><?= $product_status[$val['product_status']] ?></td>
					<td>
						<?php if ($list_edit) { ?>
							<a href="/product/product/edit?id=<?= $val['id'] ?>">修改</a>
						<?php } ?>
						| 
						<?php if ($list_del) { ?>  
							   <a href="/product/product/delete?id=<?= $val['id'] ?>" onclick='if (!confirm("是否确定删除！"))
		                                           return false;'>删除</a>
						   <?php } ?>
						| 
						<?php if ($list_copy) { ?>  
							<a href="/product/product/copy?id=<?= $val['id'] ?>">复制</a>
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
		<?php if ($btn_batch_bel) { ?>
			<input type="button " class="button_small delpro" value="删除 " />
	<!--              <input type="button " class="button_small " value="导出项目资料EXECl " />-->
		<?php } ?>
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
                var da = eval(("(" + result + ")"));
                if (da['res'] == 0) {
                    alert(da['msg']);
                } else {
                    res(da['res'], location.href);
                }
                //location.reload();
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
                var da = eval(("(" + result + ")"));
                if (da['res']) {
                    res(da['res'], location.href);
                } else {
                    alert(da['msg']);
                }
            });
        })

        $('.ajax_op').bind('click', function () {
            op = $(this).attr('op');
            data_index = $(this).attr('data-index');
            index = $(this).attr('index');
            $.get("/product/product/moreop", {op: op, value: data_index, id: index}, function (result) {
                var da = eval(("(" + result + ")"));
                if (da['res']) {
                    res(da['res'], location.href);
                } else {
                    alert(da['msg']);
                }

            });
        });
    });</script> 
<?php $this->endBlock(); ?>

