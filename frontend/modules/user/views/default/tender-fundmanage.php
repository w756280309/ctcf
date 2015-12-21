<?php

use yii\widgets\LinkPager;
use frontend\models\ProductCategoryData;
use common\models\user\MoneyRecord;

$this->registerCssFile('/css/tender.css', ['depends' => 'yii\web\YiiAsset']);
?>
<div class="fr page-right tender-fundmanage">
	<div class="page-rigth-title">
		<div class="tab<?php
		if (empty($tab)) {
			echo ' tabon';
		}
		?>"><a href="javascript:void(0);" target="_self">资金管理</a></div>
	</div>
	<div class="page-right-detail">
		<div class="tender-fundmanage-top">
			<a class="tender-fundmanage-top-1">账户余额：</a>
			<a class="tender-fundmanage-top-2"><?php echo "$remaining" ?></a>
			<a class="tender-fundmanage-top-1">元</a>
			<span class="tender-fundmanage-top-4"><a href="/user/draw/withdrawcash?current=3">提现</a></span>
			<span class="tender-fundmanage-top-3"><a href="/user/recharge/recharge?current=3" target="_blank">充值</a></span>
		</div>
		<?php
		$classclick = "";
		$self_url_all = "/user/default/fundmanage?current=3";
		$self_url_huan = "/user/default/fundmanage?current=3&type=" . MoneyRecord::TYPE_HUANKUAN;
		$self_url_order = "/user/default/fundmanage?current=3&type=" . MoneyRecord::TYPE_ORDER;
		$self_url_fang = "/user/default/fundmanage?current=3&type=" . MoneyRecord::TYPE_FANGKUAN;
		$self_url_draw = "/user/default/fundmanage?current=3&type=" . MoneyRecord::TYPE_DRAW;
		$self_url_recharge = "/user/default/fundmanage?current=3&type=" . MoneyRecord::TYPE_RECHARGE;
		if (!is_numeric($type)) {
			$classclick_all = "a-click";
		} elseif ($type == MoneyRecord::TYPE_HUANKUAN) {
			$classclick_huan = "a-click";
		} elseif ($type == MoneyRecord::TYPE_ORDER) {
			$classclick_order = "a-click";
		} elseif ($type == MoneyRecord::TYPE_FANGKUAN) {
			$classclick_fang = "a-click";
		} elseif ($type == MoneyRecord::TYPE_DRAW) {
			$classclick_draw = "a-click";
		} elseif ($type == MoneyRecord::TYPE_RECHARGE && is_numeric($type)) {
			$classclick_recharge = "a-click";
		} else {
			
		}
		?>
		<div class="tender-fundmanage-center">
			<span class="tender-fundmanage-center-1">筛选：</span>
			<a class="tender-fundmanage-center-2 <?= $classclick_all ?>" href="<?= $self_url_all ?>">全部</a>
			<?php if ($investtype == investtype) { ?>
				<a class="tender-fundmanage-center-2 <?= $classclick_huan ?>"  href="<?= $self_url_huan ?>">还款</a>
			<?php } else { ?>
				<a class="tender-fundmanage-center-2 <?= $classclick_order ?>"  href="<?= $self_url_order ?>">投标</a>
				<a class="tender-fundmanage-center-2 <?= $classclick_fang ?>"  href="<?= $self_url_fang ?>">回款</a>
			<?php } ?>
			<a class="tender-fundmanage-center-2 <?= $classclick_draw ?>"  href="<?= $self_url_draw ?>">提现</a>
			<a class="tender-fundmanage-center-2 <?= $classclick_recharge ?>"  href="<?= $self_url_recharge ?>">充值</a>

		</div>
		<div class="tender-fundmanage-bottom">
			<table>
				<tr class="th">
					<th width="80">类型</th>
					<th width="160">时间</th>
					<th width="120">金额</th>
					<th width="140">余额</th>
					<th width="300">备注</th>
				</tr>
				<?php foreach ($model as $key => $val) { ?>
					<tr>
						<td><?php
							if ($val['type'] == 0) {
								echo "充值";
							} elseif ($val['type'] == 1) {
								echo "提现";
							} elseif ($val['type'] == 2) {
								echo "投标";
							} elseif ($val['type'] == 3) {
								echo "放款";
							} elseif ($val['type'] == 4) {
								echo "还款";
							} elseif ($val['type'] == 5) {
								echo "撤标退款";
							} elseif ($val['type'] == 6) {
								echo "平台手续费";
							} else {
								echo "--";
							}
							?></td>
						<td><?= date("Y-m-d H:i:s", $val['updated_at']); ?></td>
						<td class="thback-tianshukong"><?php
							if ($val['in_money'] != 0.00) {
								echo "<font color='red'>" . '+' . $val['in_money'] . "</font>";
							} else {
								echo "<font color='green'>" . '-' . $val['out_money'] . "</font>";
							}
							?>&nbsp;元</td>
						<td><?= $val['balance'] ?>&nbsp;元</td>
						<td class="cnn">资金流水：<?= $val['sn'] ?><br />
							项目流水：<?= $val['osn'] ?>
						</td>
					</tr>
				<?php } ?>
				<tr><td colspan="7"><?= LinkPager::widget(['pagination' => $pages]); ?></td></tr>
			</table>
		</div>
	</div>
</div>
