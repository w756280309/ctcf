<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = "线上投资明细——查看";
$user_id= Yii::$app->request->get('user_id');
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="page_function">
    <div class="info">
        <h3>投资详情</h3>
    </div>
</div>
<div class="tab" id="tab">
    <a class="selected" href="/user/user/onlinelist?user_id=<?=$user_id ?>">返回列表</a>
</div>

<div class="page_form">
	<div class="page_table form_table">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<th width="100">订单ID:</th>
				<td width="300">
					<?= $model['id'] ?>
				</td>
				<td></td>
			</tr>
			<tr>
				<th width="100">订单序号:</th>
				<td width="300">
					<?= $model['sn'] ?>
				</td>
				<td></td>
			</tr>
			<tr>
				<th width="100">用户名:</th>
				<td width="300">
					<?php echo "$username"; ?>
				</td>
				<td></td>
			</tr>
			<tr>
				<th width="100">年利率:</th>
				<td width="300">
					<?= $model['yield_rate'] ?>
				</td>
				<td></td>
			</tr>
			<tr>
				<th width="100">借款期限 :</th>
				<td width="300">
					<?= $model['expires'] ?>
				</td>
				<td>(以天为单位) 如 15  表示15天</td>
			</tr>
			<tr>
				<th width="100">还款方式:</th>
				<td width="300">
					<?php
					if ($model['refund_method'] == 1) {
						echo "按天到期本息";
					} else {
						echo "按月付息还本";
					}
					?>
				</td>
				<td></td>
			</tr>
			<tr>
				<th width="100">投标金额:</th>
				<td width="300">
<?=$model['order_money'] ?>
				</td>
				<td></td>
			</tr>
			<tr>
				<th width="100">成功时间:</th>
				<td width="300">
<?= $model['order_time'] ?>
				</td>
				<td>支付之后</td>
			</tr>
		</table>
	</div>
</div>

<?php $this->endBlock(); ?>