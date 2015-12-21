<?php

use yii\widgets\LinkPager;

$this->title = '账户查阅';
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="page_function">
    <div class="info">
        <h3>账户查阅</h3>
    </div>
</div>
<div class="tab" id="tab">
	<a href="/user/user/list">返回列表</a>
	<a class="<?php
	if ($type == 1) {
		echo "selected";
	}
	?>" href="/user/user/accountaccess?user_id=<?= $user_id ?>&type=1">投资账户</a>
	<a class="<?php
	if ($type == 2) {
		echo 'selected';
	}
	?>" href="/user/user/accountaccess?user_id=<?= $user_id ?>&type=2">融资账户</a>
</div>
<style>
.page_form	table tr td {height:17px;}
</style>
<div class="page_form">

    <div class="page_table form_table">
        <table width="600px" border="0" cellspacing="0" cellpadding="0">

            <tr>
                <th width="100">用户名称</th>
                <td width="100"><?php echo $username; ?></td>
            </tr>	
			<tr>
                <th width="100">账户余额</th>
                <td width="100"><?= $model->account_balance ?></td>
            </tr>	
			<tr>
                <th width="100">可用余额</th>
                <td width="100"><?= $model->available_balance ?></td>
            </tr>	
			<tr>
                <th width="100">冻结余额</th>
                <td width="100"><?= $model->freeze_balance ?></td>
            </tr>	
			<tr>
                <th width="100">账户入金总额</th>
                <td width="100"><?= $model->in_sum ?></td>
            </tr>	
			<tr>
                <th width="100">账户出金总额</th>
                <td width="100"><?= $model->out_sum ?></td>
            </tr>	
			<tr>
                <th width="100">创建时间</th>
                <td width="100"><?php echo date('Y-m-d  H:i:m', $model->created_at) ?></td>
            </tr>	
			<tr>
                <th width="100">上次修改时间</th>
                <td width="100"><?php echo date('Y-m-d  H:i:m', $model->updated_at) ?></td>
            </tr>
        </table>
    </div>
</div>

<?php $this->endBlock(); ?>
