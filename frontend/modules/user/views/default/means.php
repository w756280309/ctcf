<?php
use yii\widgets\LinkPager;
use frontend\models\ProductCategoryData;
$pcd = new ProductCategoryData();
$self_url = "/user/default/means?current=2";
?>
<style type="text/css">
    .page-rigth-title .tab{
        width:70px;
        float: left;
        margin-right: 20px;
    }
    .page-rigth-title .tabon{
        color:#ff9600;
        border-bottom: 2px solid #ff9600
    }
</style>
	<div class="fr page-right">
		<div class="page-rigth-title">
                    <div class="tab<?php if(empty($tab)){echo ' tabon'; }?>"><a href="<?=$self_url."&tab=0"?>" target="_self">我的资产</a></div>
                    <div class="tab<?php if($tab){echo ' tabon'; }?>"><a href="<?=$self_url."&tab=1"?>" target="_self">特殊资产</a></div>
		</div>
		<div class="page-right-detail" style="padding:0;padding-bottom:100px;">
                    <?php if(empty($tab)){?>
			<table>
				<tr class="th">
					<th>项目编号</th>
					<th>项目名称</th>
					<th>年化收益</th>
					<th>项目期限</th>
					<th>到期日</th>
					<th>认购时间</th>
					<th>认购金额</th>
					<th>合同编号</th>
				</tr>
                                
                                 <?php foreach ($model as $key=>$val) { ?>
				<tr>
					<td><?=$val['product_sn'] ?></td>
					<td class="thback-tianshukong"><?=$val['product_title'] ?></td>
                    <td><?=  number_format($val['yield_rate'],2) ?>%</td>
					<td class="thback-tianshukong"><?=$val['product_duration'] ?>天</td>
                    <td><?= date("Y-m-d",$val['pay_time']) ?></td>
					<td><?= date("Y-m-d",$val['order_time']) ?></td>
					<td><?=$pcd->toFormatMoney($val['order_money']) ?></td>
					<td class="thback-click"><span class="thback-lan">查看合同</span><!--<?=$val['contract_sn'] ?>-->
					    <div class="thback-rela">
					        <div class="thback-abso">
					        	<p class="thback-abso-p1"><span>计划风险揭示书</span><a href="#" target="_blank" class="thback-abso-a">查看</a><a href="#" target="_blank">下载</a></p>
					        	<p><span>投资说明书</span><a href="#" target="_blank" class="thback-abso-a">查看</a><a href="#" target="_blank">下载</a></p>
					        	<p><span>资产管理计划合同</span><a href="#" target="_blank" class="thback-abso-a">查看</a><a href="#" target="_blank">下载</a></p>
					        	<p><span>认购份额确认函</span><a href="#" target="_blank" class="thback-abso-a">查看</a><a href="#" target="_blank">下载</a></p>
					        </div>
					    <div>
					</td>    
				</tr>
				<?php } ?>
                                
                                <tr><td colspan="8"><?= LinkPager::widget(['pagination' => $pages]); ?></td></tr>
			</table>
                    <?php }else{?>
                        <table>
				<tr class="th">
					<th>项目编号</th>
					<th>项目名称</th>
					<th>类型</th>
					<th>认购时间</th>
					<th>成交金额</th>
					<th>合同编号</th>
				</tr>
                                
                                 <?php foreach ($model as $key=>$val) { ?>
				<tr>
					<td><?=$val['product_sn'] ?></td>
					<td><?=$val['product_title'] ?></td>
                                        <td><?= common\models\product\OfflineProduct::getSpecialType($val['type']); ?></td>
					<td><?= date("Y-m-d",$val['order_time']) ?></td>
					<td><?=$pcd->toFormatMoney($val['order_money']) ?></td>
					<td class="thback-click"><span class="thback-lan">查看合同</span><!--<?=$val['contract_sn'] ?>-->
                        <div class="thback-rela">
					        <div class="thback-abso">
					        	<p class="thback-abso-p1"><span>计划风险揭示书</span><a href="#" target="_blank" class="thback-abso-a">查看</a><a href="#" target="_blank">下载</a></p>
					        	<p><span>投资说明书</span><a href="#" target="_blank" class="thback-abso-a">查看</a><a href="#" target="_blank">下载</a></p>
					        	<p><span>资产管理计划合同</span><a href="#" target="_blank" class="thback-abso-a">查看</a><a href="#" target="_blank">下载</a></p>
					        	<p><span>认购份额确认函</span><a href="#" target="_blank" class="thback-abso-a">查看</a><a href="#" target="_blank">下载</a></p>
					        </div>
					    <div>
					</td>
				</tr>
				<?php } ?>
                                
                                <tr><td colspan="6"><?= LinkPager::widget(['pagination' => $pages]); ?></td></tr>
			</table>
                    <?php }?>
		</div>
	</div>
<script src="https://www.wangcaigu.com/template/default/Public/js/jquery1.42.min.js" type="text/javascript"></script>
<script>
$(function(){
	$('.thback-click').hover(function(){
		$(this).children('.thback-rela').css("display","block");
	},
	function(){
		$(this).children('.thback-rela').css("display","none");
	})
})	
</script>