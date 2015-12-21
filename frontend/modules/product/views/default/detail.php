<?php
$this->registerJs("var laySum = 0; ;",1);//定义变量 介绍layer的index
$this->registerJsFile('/js/layer/layer.min.js', ['depends' => 'yii\web\YiiAsset']);
//$this->registerJsFile('/js/product.js', ['depends' => 'yii\web\YiiAsset']);

$this->title = $cat_model->name."-".$model->title;
error_reporting(E_ALL^E_NOTICE);
use frontend\models\ProductCategoryData;
$pcd = new ProductCategoryData();
?>
<div class="body">
	<ul class="bnav">
		<li class="arrow">
			<a href="/">首页</a>
		</li>
		<li class="arrow">
			<a href="/product">产品公告</a>
		</li>
		<li class="arrow">
			<a href="/product?cid=<?=$cat_model->id ?>"><?=$cat_model->name ?></a>
		</li>
		<li>
			<?=$model->title ?>
		</li>
	</ul>
	
	<div class="product-detail">
		<h2>
			<?=$model->title ?>
		</h2>
            <div id="ljtz_click" style="width:99px;height:25px;background-color:#ff9600;border-radius:6px;text-indent:24px;line-height:25px;font-family:'微软雅黑';position:relative;bottom:44px;left:863px"><a style="color:#fff;" href="#" op="" title="登录" wh="500-500">立即投资</a></div>
		<div id="show_click" style="position:relative;display:none;">
			<div style="width:280px;height:70px;background:url(/images/ngnrl.png) no-repeat;position:absolute;bottom:-28px;left:676px;"><p style="width:262px;padding-top:13px;padding-left:15px;font-size:12px;color:#565656;">尊敬的投资者，南京金交中心挂牌产品均通过分销平台销售，投资请联系我们的客户服务专员，联系电话：025-8570-8888</p></div>
		</div>
		<script>
			$(function(){
				$('#ljtz_click').hover(function() {
                                    
					$('#show_click').css("display","block");
                                        
				}, function() {
					$('#show_click').css("display","none");
				});
			})
		</script>
		<div class="part">
			<div class="fl prodetinfo">
				<ul class="prodetinfo-part1">
					<li>
                                            <div class="text1"><?php if(number_format($model->yield_rate,2)!='0.00'){ ?><?=number_format($model->yield_rate,2) ?><span>%</span><?php }else{ ?><span style="font-size: 18px;color:#f08200">详见协议</span><?php } ?></div>
						<div class="text2">年化收益率</div>
					</li>
					<li class="line2"></li>
					<li>
						<div class="text1"><?php if($model->product_duration){ ?><?=$model->product_duration ?><span><?=$duration_type?></span><?php }else{ ?><span style="font-size: 18px;color:#f08200">详见协议</span><?php } ?></div>
						<div class="text2">项目期限</div>
					</li>
				</ul>
			</div>
			<div class="fl line1"></div>
			<div class="fl prodetinfo">
				<div style="border-bottom: 1px solid #E5E5E5;overflow: hidden;">
					<ul class="prodetinfo-part2">
						<li>
							<div class="text1">项目编号</div>
							<div class="text2"><?=$model->sn ?></div>
						</li>
						<li class="line3"></li>
						<li>
							<div class="text1">融资总额</div>
							<div class="text2"><?=$pcd->toFormatMoney($model->money) ?></div>
						</li>
					</ul>
				</div>
				<div>
					<ul class="prodetinfo-part2">
						<li>
							<div class="text1">起投金额</div>
							<div class="text2">
                                                        
                                                        <?php if($model->start_money=='0.00'){echo "详见协议";}else{ echo $pcd->toFormatMoney($model->start_money);}?>
                                                        
                                                        </div>
						</li>
						<li class="line3"></li>
						<li>
							<div class="text1">项目状态</div>
							<div class="text3">
								<?php 
								if($model->product_status==1){
									echo "即将开始";
								}else if($model->product_status==2){
									echo "挂牌公告";
								}else if($model->product_status==3){
									echo "协议签署";
								}else if($model->product_status==4){
									echo "项目成交";
								}else{
									echo "无效状态";
								}

								?>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
		
		<div class="part">
			<div class="label"><span></span>产品详情</div>
			<?= $model->description ?>

		</div>
		
		<?php if(!empty($model->account_name)&&!empty($model->account)&&!empty($model->bank)) {?>
		<div class="part">
			<div class="label"><span></span>资金监管账户</div>
			<table>
				<tr>
					<td>账户名称</td>
					<td>银行帐号</td>
					<td>开户行</td>
				</tr>
				<tr>
					<td><?=$model->account_name ?></td>
					<td><?=$model->account ?></td>
					<td><?=$model->bank ?></td>
				</tr>
			</table>
		</div>
		<?php }?>
		<?php if(isset($field[1])){ ?>
		<div class="part">
			<div class="label"><span></span>投资要求</div>
			<table>
				<?php foreach($field[1] as $key=>$val){ ?>
				<tr>
					<td><?= $val['name'] ?></td>
					<td><?= $val['content'] ?></td>
				</tr>
				<?php } ?>  
			</table>
		</div>
		<?php } ?>

		<?php  if(isset($field[2])){ ?>
		<div class="part" style="border-bottom: none;">
			<div class="label"><span></span>备查文件</div>
			<table style="width: 60%">
				<?php foreach($field[2] as $key=>$val){ ?>
				<tr>
					<td style="text-align: center"><?= $key+1 ?></td>
					<td style="text-align: center"><?= $val['name'] ?></td>
					<td style="text-align: center; color:#0088cc">
						<?php if(Yii::$app->user->id){ ?>
							<?php if(empty($val['content'])){ ?> 
							本中心留档备查
							<?php }else{ ?> 
                                                            <?php if($allow){ ?>
                                                                <a href="/upload/product/<?=$val['content'] ?>" target="_blank" style="color:#0088cc">点击查看</a>
                                                                &emsp;
                                                                <a href="/download.php?file=<?=$val['content'] ?>" style="color:#0088cc">点击下载</a>
                                                            <?php }else{ ?>
                                                                <a href="javascript:alert('无权限查看');" target="_blank" style="color:#0088cc">点击查看</a>
                                                                &emsp;
                                                                <a href="javascript:alert('无权限下载');" style="color:#0088cc">点击下载</a>
                                                            <?php }?>
							<?php } ?> 
							<?php }else{ ?>
							<a href="javascript:alert('您尚未登录，请登录');" style="color:gray">点击下载</a>
							<?php } ?> 
						</td>
					</tr>
					<?php } ?>    
				</table>
			</div>
			<?php } ?>   

		</div>
	</div>