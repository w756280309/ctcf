<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;

$this->title = '分类管理';

use common\models\AuthSys;

$menus = AuthSys::getMenus('N1002000');

$list_edit = AuthSys::checkMenus($menus, "N1", "002", AuthSys::OP_TYPE_LIST, AuthSys::LIST_RULE_EDIT);  //编辑
$list_del = AuthSys::checkMenus($menus, "N1", "002", AuthSys::OP_TYPE_LIST, AuthSys::LIST_RULE_DEL);  //删除
$btn_add = AuthSys::checkMenus($menus, "N1", "002", AuthSys::OP_TYPE_PAGE, AuthSys::LIST_RULE_ADD);  //添加
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="page_function">
    <div class="info">
		<h3><?= Html::encode($this->title) ?></h3>
    </div>
    <div class="exercise">
        <a href="/news/category/edit">添加分类</a>

		<span style="color: red">预置数据，删除须谨慎</span>
    </div>
</div>

<div class="page_main">
    <div class="page_table table_list">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
				<th width="10%"><center>ID</center></th>
			<th width="25%">栏目名称</th>
			<th width="10%"><center>顺序</center></th>
			<th width="10%"><center>栏目显示</center></th>
			<th width="15%"><center>栏目操作</center></th>
            </tr>                                    
			<?php foreach ($models as $news): ?>
				<tr>
					<td><center><?= $news['id'] ?></center></td>
				<td  style="text-align: left;"><a href="#" target="_blank"><?= $news['name'] ?></a></td>
				<td><center><input type="text" value="<?= $news['sort'] ?>" class="sequence" readonly="readonly" /></center></td>
				<td><center><font color=green><b>
						<?php
						if ($news['status'] == 1) {
							echo('√');
						} else {
							echo('X');
						}
						?>

					</b></font></center></td>
				<td>
				<center>
					<?php if ($list_edit) { ?>
						<a href="/news/category/edit?id=<?= $news['id'] ?>">修改</a> | 			  
					<?php } ?>
					<?php if ($list_del) { ?>
						<a href="/news/category/delete?id=<?= $news['id'] ?>" onclick="javascript:return confirm('确定要删除吗？');">删除</a>
					<?php } ?>
				</center>
				</td>
				</tr>   
			<?php endforeach; ?> 
        </table>
    </div>
</div>
<br /><br /><br />

<?php $this->endBlock(); ?>