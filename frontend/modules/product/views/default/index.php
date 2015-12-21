<?php
use yii\widgets\LinkPager;
use frontend\models\ProductCategoryData;
use common\models\product\OnlineProduct;
$pcd = new ProductCategoryData();
//var_dump( $pcd->toFormatMoney(500000000.00));exit;
$cat_data = $pcd->category(['status'=>1,'parent_id'=>0]);
$this->title = $cat_model->name;
?>

<div class="body">
	<ul class="bnav">
		<li class="arrow">
			<a href="/">首页</a>
		</li>
		<li class="arrow">
			<a href="/product">产品公告</a>
		</li>
		<li>
			<?=$cat_model->name ?>
		</li>
	</ul>
	<div class="fl page-left">
		<div class="page-left-title"></div>
		<div class="page-left-content">
			<?php 
			foreach ($cat_data as $key=>$val){ 

				?>
				<div class="page-left-content-title" style="background:url(<?=$val['img_pre']."pc_".$val['code']?>.png) 20px 3px no-repeat"><a href="/product?cid=<?=$key?>"><?=$val['front_name']?></a></div>

				<ul>
					<?php
					$child_cat_data = $pcd->getSubCat(['status'=>1,'parent_id'=>$key]);

					foreach($child_cat_data as $k=>$v){
						?>
						<li <?php if($v['id']==$cat_model->id){echo 'class="selected"';} ?>>
							<a href="/product?cid=<?=$v['id']?>"><?=$v['name']?></a>
						</li>
						<?php }?>  

					</ul>
					<?php } ?>

				</div>
				<div class="page-left-bottom"></div>
			</div>

			<div class="fr page-right">
				<?php if(!empty($cat_model->description)){ ?>
				<div class="backchangelw">
					<p class="ppp1"><?=$cat_model->name ?></p>
					<p class="ppp2"><?=$cat_model->description ?></p>
				</div>
				<?php } ?>
				<ul class="product-list">              
					<?php foreach ($model as $key=>$val) { ?>
					<li>
						<div class="fl proimg">
                                                    <img src="<?= '/images/category/'.$val['cat_code'].'.jpg' ?>" title="<?=$val['title'] ?>" alt="<?=$val['title'] ?>" />
						</div>
						<div class="fl proinfo" style="width:450px;">
							<p title="<?=$val['title'] ?>"><?=$val['title'] ?> <span><?=$val['sn'] ?></span></p>
                                                        <?php if($val['special_type']==0){ //普通标  ?>
                                                                <ul>
                                                                        <li style="margin:0; width:152px;"><span>融资金额：</span><?= $pcd->toFormatMoney($val['money']) ?></li>
                                                                        <li style="margin:0; width:152px;">项目期限：
                                                                            <?php if($val['line']==1){ ?>
                                                                                <?php if($val['product_duration']){ ?>
                                                                                <?=$val['product_duration'] ?><?php if($val['product_duration_type']==2){echo "年";}else if($val['product_duration_type']==1){echo "个月";}else{echo "天";} ?>
                                                                                <?php }else{ ?>
                                                                                详见协议
                                                                                <?php } ?>
                                                                            <?php }else{ ?>
                                                                                <?=$val['product_duration_type']?>
                                                                            <?php } ?>
                                                                        </li>
                                                                        <li style="margin:0;color: #FF0000;width:100px;text-align: center;">
                                                                            <?php if($val['line']==1){ ?>
                                                                                <?php if(number_format($val['yield_rate'], 2)!='0.00'){ ?>
                                                                                <span style="font-size: 22px;"><?=  number_format($val['yield_rate'], 2) ?></span> %
                                                                                <?php }else{ ?>
                                                                                    <font color="#767676">详见协议</font>
                                                                                <?php } ?>
                                                                             <?php }else{ ?>
                                                                                 <span style="font-size: 22px;"><?=  number_format($val['yield_rate']*100, 2) ?></span> %   
                                                                             <?php } ?>
                                                                        </li>
                                                                </ul>
                                                                <ul>
                                                                        <li style="margin:0; width:152px;"><span>起投金额：</span>
                                                                            <?php if($val['start_money']=='0.00'){echo "详见协议";}else{ echo $pcd->toFormatMoney($val['start_money']);}?>
                                                                        </li>
                                                                        <li style="margin:0; width:152px;"><span>项目状态：</span>
                                                                            
                                                                            <?php if($val['line']==1){ 
                                                                                echo  \common\models\product\OfflineProduct::getProductStatusAll($val['product_status']);
                                                                            }else
                                                                            { 
                                                                                echo OnlineProduct::getProductStatusAll($val['product_status']); 
                                                                            }
                                                                            ?>
                                                                        </li>
                                                                        <li style="margin:0;">预期年化收益率</li>
                                                                </ul>
                                                        <?php }else{ ?>
                                                                <ul>
                                                                        <li style="margin:0; width:152px;"><span>挂牌底价：</span><?= $pcd->toFormatMoney($val['money']) ?></li>
                                                                        <li style="margin:0; width:152px;">类型：</span>
                                                                            <?=$val['special_type_title']; ?>
                                                                        </li>
                                                                </ul>
                                                                <ul>
                                                                        <li style="margin:0; width:152px;"><span>截止日期：</span>
                                                                            <?php if($val['start_money']=='0.00'){echo "详见协议";}else{ echo $pcd->toFormatMoney($val['start_money']);}?>
                                                                        </li>
                                                                        <li style="margin:0; width:152px;"><span>交易状态：</span><?php echo  \common\models\product\OfflineProduct::getProductStatusAll($val['product_status']); ?></li>
                                                                        
                                                                </ul>
                                                        <?php } ?>
						</div>
						<div class="fr">
                                                    <?php if($val['line']==0){ //线上标  ?>
                                                        <?php 
                                                        $op_name = "";
                                                        if($val['product_status']==OnlineProduct::STATUS_NOW||$val['product_status']==OnlineProduct::STATUS_FOUND){
                                                            $op_name="立即投资";
                                                        }else if($val['product_status']==OnlineProduct::STATUS_PRE){
                                                            $op_name="即将开始";
                                                        }else if($val['product_status']==OnlineProduct::STATUS_FULL){
                                                            $op_name="满&emsp;&emsp;标";
                                                        }else if($val['product_status']==OnlineProduct::STATUS_HUAN){
                                                            $op_name="还&nbsp;款&nbsp;中";
                                                        }else if($val['product_status']==OnlineProduct::STATUS_OVER){
                                                            $op_name="已&nbsp;还&nbsp;清";
                                                        }else if($val['product_status']==OnlineProduct::STATUS_LIU){
                                                            $op_name="流&emsp;&emsp;标";
                                                        }
                                                        ?>
                                                        <a class="link" href="/product/tender/detail?id=<?=$val['id'] ?>"><?=$op_name ?></a>
                                                        
                                                    <?php }else{ if($val['special_type']==0){ //普通标  ?>
							<a class="link" href="/product/default/detail?id=<?=$val['id'] ?>">查看详情</a>
                                                    <?php }else{ ?>
                                                        <a class="link" href="/product/default/specialdetail?id=<?=$val['id'] ?>">查看详情</a>
                                                    <?php } }?>    
						</div>			
					</li>
					<?php } ?>


				</ul>
				<?= LinkPager::widget(['pagination' => $pages]); ?>
			</div>
		</div>
