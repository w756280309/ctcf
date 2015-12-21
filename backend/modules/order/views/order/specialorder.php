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
				<h3>特殊资产投资记录</h3>
			</div>
			   
		</div>
		<div class="page_main">
			<div class="page_menu" style="height:auto;">
                            <form method="get" action="/order/order/specialorder?psn=<?= $psn ?>" target="_self">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="100" align="right">报价时间：</td>
							<td width="200" style="text-align: left;">
                                                            <input type="text" class="text_value" style="width:200px;" value="<?= $search['order_time'] ?>" name="order_time"  readonly="readonly" onclick='WdatePicker({dateFmt:"yyyy-MM-dd"});'/>
							</td>
							<td width="100" align="right">投资人账号：</td>
							<td width="200" style="text-align: left;">
								<input type="text" class="text_value" style="width:200px;" value="<?= $search['username'] ?>" name="username" />
							</td>
							<td width="100" align="right">报价金额：</td>
							<td width="200">
								<input type="text" class="text_value" style="width:200px;" value="<?= $search['order_money'] ?>" name="order_money" />
							</td>
						</tr>
						<tr>
							<td width="100" align="right">是否缴纳保证金：</td>
							<td width="200" style="text-align: left;">
                                                            <select name="deposit_status">
                                                                <option value="0" <?php if($search['deposit_status']==0){echo "selected";}?>>请选择</option>
                                                                <option value="1" <?php if($search['deposit_status']==1){echo "selected";}?>>否</option>
                                                                <option value="2" <?php if($search['deposit_status']==2){echo "selected";}?>>是</option>
                                                            </select>
							</td>
							<td width="100" align="right">是否成交：</td>
							<td width="200" style="text-align: left;">
							    <select name="deal_status">
                                                                <option value="0" <?php if($search['deal_status']==0){echo "selected";}?>>请选择</option>
                                                                <option value="1" <?php if($search['deal_status']==1){echo "selected";}?>>否</option>
                                                                <option value="2" <?php if($search['deal_status']==2){echo "selected";}?>>是</option>
                                                            </select>
							</td>
							<td width="100" align="right"></td>
							<td width="200">
								
							</td>
						</tr>
						<tr>
							
							<td colspan="6"><input type="submit" class="button_small" onclick="" value="搜索" /></td>
						</tr>
					</table>
				</form>
			</div>
			<div class="page_table table_list">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<th>ID</th>
                                                <th>报价时间</th>
                                                <th>投资人账号</th>
                                                <th>报价金额</th>
						<th>保证金金额</th>
                                                <th>是否缴纳保证金</th>
                                                <th>是否返还保证金</th>
						<th>是否成交</th>
<!--                                                <th>合同号</th>-->
						<th>操作</th>
					</tr>
                       <?php
                            foreach($model as $key=>$val)
                            {
                        ?>
					<tr>
						<td><?= $val['id'];?></td>
						<td><?= date("Y-m-d H:i",$val['order_time']);?></td>
                                                <td><?= $val['username'];?></td>
						<td><?= $val['order_money'];?></td>
						<td><?= $val['deposit_money'];?></td>
                                                <td><?= $val['deposit_status']?"是":"否"; ?></td>
                                                <td><?= $val['deposit_return_status']?"是":"否"; ?></td>
						<td><?= $val['deal_status']?"是":"否"; ?></td>
<!--						<td><?= $val['contract_sn'];?></td>-->
						<td>
							<a href="/order/order/specialview?id=<?= $val['id'];?>&psn=<?= $val['product_sn'];?>">编辑</a> | 
<!--                                                        <a href="/order/order/delete?id=<?= $val['id'];?>">删除</a> | -->
                                                        <a href="javascript:void(0);" onclick="returnBaozhengjin('<?= $val['id'];?>','<?= $val['user_id'];?>');">返还保证金</a>
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
<script type="text/javascript">
function returnBaozhengjin(id,uid){
    $.get("/order/order/returnbao?id="+id+"&uid="+uid, function(result){
        var da = eval(("("+result+")"));
        if(da['res']){
            location.reload();
        }else{
            alert(da['msg']);
        }
    });
}
</script>
<?php $this->endBlock(); ?>

