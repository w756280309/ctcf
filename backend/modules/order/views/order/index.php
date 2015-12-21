<?php
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
/**
 * Created by zhy.
 * User: al
 * Date: 15-3-17
 * Time: 下午6:25
 */
/* @var $categories */
/* @var $this yii\web\View */
$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);
error_reporting(E_ALL^E_NOTICE);
$uid =  Yii::$app->request->get('uid');
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="page_function">
			<div class="info">
				<h3>投资明细</h3>
			</div>
			<div class="exercise">
				<a href="/user/user/list">会员列表</a><a href="/order/order/edit?uid=<?= $user['id'] ?>">添加订单</a>
			</div>
    
		</div>
		<div class="page_main">
			<div class="page_menu" style="height:auto;">
                            <form method="get" action="/order/order/index?user_id=<?= $user['id'] ?>" target="_self">
                                <input type="hidden" value="<?= $user['id'] ?>" name="user_id" />
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="100" align="right">项目名称：</td>
							<td width="200" style="text-align: left;">
                                                            <input type="text" class="text_value" style="width:200px;" value="<?= $search['product_title'] ?>" name="product_title" />
							</td>
							<td width="100" align="right">年化收益：</td>
							<td width="200" style="text-align: left;">
								<input type="text" class="text_value" style="width:200px;" value="<?= $search['yield_rate'] ?>" name="yield_rate" />
							</td>
							<td width="100" align="right">认购金额：</td>
							<td width="200">
								<input type="text" class="text_value" style="width:200px;" value="<?= $search['order_money'] ?>" name="order_money" />
							</td>
						</tr>
						<tr>
							<td width="100" align="right">合同编号：</td>
							<td width="200" style="text-align: left;">
								<input type="text" class="text_value" style="width:200px;" value="<?= $search['contract_sn'] ?>" name="contract_sn" />
							</td>
							<td width="100" align="right">认购时间：</td>
							<td width="200" style="text-align: left;">
								<input type="text" class="text_value" style="width:200px;" value="<?= $search['order_time'] ?>"  name="order_time"  onClick="WdatePicker()" />
							</td>
							<td width="100" align="right">到期兑付日：</td>
							<td width="200">
								<input type="text" class="text_value" style="width:200px;" value="<?= $search['pay_time'] ?>"    name="pay_time" onClick="WdatePicker()"/>
							</td>
						</tr>
						<tr>
							
							<td colspan="6"><input type="submit" class="button_small" onclick="" value="搜索" /></td>
						</tr>
					</table>
				</form>
			</div>
			<div><label>用户名：</label><?= $user['username'] ?> <label>总计：</label><?=$count ?>笔</div>
			<div class="page_table table_list">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<th>ID</th>
						<th>项目编号</th>
						<th>项目名称</th>
						<th>年化收益</th>
						<th>项目期限</th>
                                                <th>认购时间</th>
                                                <th>认购金额</th>
						<th>到期兑付日</th>
						<th>合同编号</th>
						<th>操作</th>
					</tr>
                       <?php
                            foreach($model as $key=>$val)
                            {
                        ?>
					<tr>
						<td><?= $val['id'];?></td>
						<td><?= $val['product_sn'];?></td>
						<td><?= $val['product_title'];?></td>
						<td><?= $val['yield_rate'];?></td>
						<td><?= $val['product_duration'];?></td>
                                                <td><?= date("Y-m-d H:i",$val['order_time']);?></td>
						<td><?= $val['order_money'];?></td>
						<td><?= date("Y-m-d H:i",$val['pay_time']);?></td>
						<td><?= $val['contract_sn'];?></td>
						<td>
							<a href="/order/order/edit?id=<?= $val['id'];?>&uid=<?= $val['user_id'];?>">编辑</a> | <a href="/order/order/delete?id=<?= $val['id'];?>">删除</a> | <a href="/order/order/view?id=<?= $val['id'];?>&uid=<?= $val['user_id'];?>">查看</a>
						</td>
					</tr>
                                        
                        <?php 
                            }
                        ?>  
				</table>
			</div>
		</div>

		<div class="page_tool">
			<?= LinkPager::widget(['pagination' => $pages]); ?>
		</div>
<?php $this->endBlock(); ?>

