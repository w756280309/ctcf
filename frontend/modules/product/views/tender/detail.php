<?php

use yii\widgets\ActiveForm;
use common\models\product\OnlineProduct;

$this->registerJs("var laySum = 0;", 1); //定义变量 介绍layer的index
$this->registerJs("var lefttime = " . $lefttime, 1); //定义变量 剩余时间
$this->registerJs("var status = " . $model->status, 1); //定义变量 标的状态
$this->registerJs("var allow_pro_money = " . $balance, 1); //定义变量 标的状态
$this->registerJs("var balance = " . ((\Yii::$app->user->isGuest) ? 0 : ($ua->available_balance)), 1); //定义变量 余额
$this->registerJs("var u = " . ((\Yii::$app->user->isGuest) ? 0 : (Yii::$app->user->id)), 1); //定义变量 余额
$this->registerJs("var start_money = " . $model->start_money, 1); //定义变量 起投金额
$this->registerJs("var dizeng_money = " . $model->dizeng_money, 1); //定义变量 起投金额

$this->registerJs("var runtimes = 0;", 1); //定义变量 
$this->registerJsFile('/js/common.js', ['depends' => 'yii\web\YiiAsset', 'position' => 1]);
$this->registerJsFile('/js/layer/layer.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/js/product.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/js/olpro.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerCssFile('/css/tender.css', ['depends' => 'yii\web\YiiAsset']);

$this->title = $cat_model->name . "-" . $model->title;
error_reporting(E_ALL ^ E_NOTICE);

$expires = preg_split("/([a-zA-Z0-9]+)/", $model->expires_show, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
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
			<a href="/product?cid=<?= $cat_model->id ?>"><?= $cat_model->name ?></a>
		</li>
		<li>
<?= $model->title ?>
		</li>
	</ul>

	<div class="product-detail" style="margin-bottom: 0">
		<div class="product-detail-left">
			<div class="product-detail-enen">
				<h2 class="product-detail-h2">
<?= $model->title ?>
				</h2>
				<div class="product-detail-sn">
<?= $model->sn ?>号
				</div>
			</div>
			<div class="clear"></div>
			<div class="part">
				<div class="fl prodetinfo">
					<ul class="prodetinfo-part1">
						<li class="prodetinfo-part1-li-1">
							<div class="text1"><?= bcmul($model->yield_rate, 100, 2) ?><span>%</span></div>
							<div class="text2">年化收益率</div>
						</li>
						<li class="prodetinfo-part1-li-2">
							<div class="text1 color"><?= $expires[0] ?><span><?= $expires[1] ?></span></div>
							<div class="text2">项目期限</div>
						</li>
<?php if ($model->status < 3 || $model->status == 7) { ?>
							<li class="prodetinfo-part1-li-3">
								<div><span class="prodetinfo-part1-li-3-timeicon"></span>
									<font id="RemainD">X</font>天
									<font id="RemainH">X</font>时
									<font id="RemainM">X</font>分
									<font id="RemainS">X</font>秒
								</div>
								<div class="prodetinfo-part1-li-3-d2">
									<?php if ($model->status == 1) { ?>
										距项目开始
									<?php } else if ($model->status == 2) { ?>
										剩余时间
	<?php } ?>
								</div>
							</li>
<?php } ?>
					</ul>
				</div>
				<div class="fl tenderdetail">
					<div class="tenderdetail-div">
						<ul class="tenderdetail-ul-1">
							<li>
								<p class="text1">融资总额<span><?= Yii::$app->functions->toFormatMoney($model->money) ?></span></p>
							</li>
<!--							<li>
								<p class="text1">募集时间<span><?= date("Y-m-d", $model->start_date) ?>至<?= date("Y-m-d", $model->end_date) ?></span></p>
							</li>-->
							<li>
								<p class="text1">还款方式<span><?php echo Yii::$app->params['hkfs'][$model->refund_method] ?></span></p>
							</li>
						</ul>
					</div>
					<div class="tenderdetail-div">
						<ul class="tenderdetail-ul-2">
							<li>
								<p class="text1">起投金额<span><?= Yii::$app->functions->toFormatMoney($model->start_money); ?></span>
								</p>
							</li>
							<li>
								<p class="text1">项目状态<span><?php
										if ($model->status == 1) {
											echo "预告期";
										} elseif ($model->status == 2) {
											echo "募集期";
										} elseif ($model->status == 3) {
											echo "已满标";
										} elseif ($model->status == 4) {
											echo "已流标";
										} elseif ($model->status == 5) {
											echo "还款中";
										} elseif ($model->status == 6) {
											echo "已还清";
										} else {
											echo "已成立";
										}
										?></span></p>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
<?php if ($model->status < OnlineProduct::STATUS_FULL || $model->status == OnlineProduct::STATUS_FOUND) { ?>
			<div class="product-detail-right">
				<p class="detail-right-1">项目进度<span><?= $per * 100 ?>%</span></p>
				<div class="detail-right-2">
					<div><div style="width: <?= $per * 276 ?>px; height: 7px; border-radius: 4px; background: rgb(14, 175, 230);" class="progress_rate"></div></div></div>
				<p class="detail-right-3">项目可投余额<span class="detail-right-3-s2">元</span><span class="detail-right-3-s1"><?= $balance ?></span></p>
				<?php if (Yii::$app->user->id) { ?>
					<p class="detail-right-4">我的可用余额<span class=""><?= $ua->available_balance ?>元</span></p>
	<?php } else { ?>
					<p class="detail-right-4" id="ljtz_click">我的可用余额<span>查看余额请</span><a href="javascript:void(0);" op="" title="登录" wh="388-245">[登录]</a></p>
	<?php } ?>
				<p class="detail-right-5">投资金额：<a href="/user/recharge/recharge?current=3" target="_blank">去充值</a></p>

				<form action="/product/tender/to-order?id=<?= $model->id ?>" method="post" id="suborder" onsubmit="return create()">
					<input type="hidden" value="<?= \Yii::$app->getRequest()->getCsrfToken(); ?>" name="_csrf" />
					<input type="hidden" value="<?= $model->id ?>" name="online_id" />
					<p class="detail-right-6">
						<input type="text" name="order_money" id="order_money" style="width:223px" placeholder="<?= Yii::$app->functions->toFormatMoney($model->start_money); ?>起投，<?= Yii::$app->functions->toFormatMoney($model->dizeng_money); ?>递增" />

						<span>.00元</span>

					</p>
					<?php if ($model->status < OnlineProduct::STATUS_PRE) { ?>
						<div class="detail-right-7"><a href="/">即将开始</a></div>
					<?php
					} elseif ($model->status = OnlineProduct::STATUS_NOW) {
                                            if($allow){
						$target = "";
						$buy = TRUE;
						if ($model->target == '1') {
							$target = "【注意：定向标】";
							$allow_uid = explode(',', $model->target_uid);
							if (!in_array(Yii::$app->user->id, $allow_uid)) {
								$buy = FALSE;
							}
						}
						?>
						<?php if ($buy) { ?>
							<?php
							$user = Yii::$app->user->getIdentity();
							//var_dump($user->trade_pwd);
							if(empty($user)){
								?>
						<p id="ljtz_click"><a href="javascript:void(0);" op="" title="登录" wh="400-275" class="detail-right-7btn" style="display:block;">立即投标</a></p>
								<?php
							}elseif (!empty($user->trade_pwd)) {
								?>
								<p><input type="submit" value="立即投标<?= $target ?>" class="detail-right-7btn"/></p>
			<?php } else { ?>
								<p><input type="button" value="立即投标<?= $target ?>" class="detail-right-7btn settradepwd"/></p>
								<script type="text/javascript" src="/js/tradepaw.js"></script>
								<script type="text/javascript">
									function getlay() {  //利用这个方法向子页面传递layer的index
										return laySum;
									}
									function logintip(){
										
									}
								</script>
							<?php } ?>

						<?php } else { ?>
							<p><input type="button" value="您不在该定向标的可投范围内" class="detail-right-7btn"/></p>
		<?php } ?>

                                        <?php }}else{ ?>
                                                  <p><input type="button" value="请您先通过实名认证" class="detail-right-7btn"/></p>      
                                        <?php } ?>
				</form>
			</div>
			<?php } elseif ($model->status == OnlineProduct::STATUS_FULL) { ?>
			<div class="product-detail-right">
				<p class="detail-right-8"><span></span><font>项目满标</font></p>
				<?php if (Yii::$app->user->id) { ?>
					<p class="detail-right-4">我的可用余额<span class=""><?= $ua->available_balance ?>元</span></p>
	<?php } else { ?>
					<p class="detail-right-4" id="ljtz_click">我的可用余额<span>查看余额请</span><a href="javascript:void(0);" op="" title="登录" wh="388-245">[登录]</a></p>
			<?php } ?>
				<div class="detail-right-7"><a href="/product">点击投资其他项目</a></div>
			</div>
			<?php } elseif ($model->status == OnlineProduct::STATUS_LIU) { ?>
			<div class="product-detail-right">
				<p class="detail-right-8"><span></span><font>项目流标</font></p>

				<?php if (Yii::$app->user->id) { ?>
					<p class="detail-right-4">我的可用余额<span class=""><?= $ua->available_balance ?>元</span></p>
	<?php } else { ?>
					<p class="detail-right-4" id="ljtz_click">我的可用余额<span>查看余额请</span><a href="javascript:void(0);" op="" title="登录" wh="388-245">[登录]</a></p>
			<?php } ?>

				<div class="detail-right-7"><a href="/product">点击投资其他项目</a></div>
			</div>
			<?php } elseif ($model->status == OnlineProduct::STATUS_HUAN) { ?>
			<div class="product-detail-right">
				<p class="detail-right-9"><span></span><font>还款中</font></p>
				<?php if (Yii::$app->user->id) { ?>
					<p class="detail-right-4">我的可用余额<span class=""><?= $ua->available_balance ?>元</span></p>
	<?php } else { ?>
					<p class="detail-right-4" id="ljtz_click">我的可用余额<span>查看余额请</span><a href="javascript:void(0);" op="" title="登录" wh="388-245">[登录]</a></p>
			<?php } ?>
				<div class="detail-right-7"><a href="/product">点击投资其他项目</a></div>
			</div>
			<?php } elseif ($model->status == OnlineProduct::STATUS_OVER) { ?>
			<div class="product-detail-right">
				<p class="detail-right-10"><span></span><font>已还清</font></p>
				<?php if (Yii::$app->user->id) { ?>
					<p class="detail-right-4">我的可用余额<span class=""><?= $ua->available_balance ?>元</span></p>
	<?php } else { ?>
					<p class="detail-right-4" id="ljtz_click">我的可用余额<span>查看余额请</span><a href="javascript:void(0);" op="" title="登录" wh="388-245">[登录]</a></p>
			<?php } ?>
				<div class="detail-right-7"><a href="javascript:void(0);">点击投资其他项目</a></div>
			</div>
<?php } ?>
	</div>
	<div class="clear"></div>
	<div class="product-detail">
		<div class="part">
			<div class="label"><span></span>产品详情</div>
			<div class="product-detail_csxc"><?= $model->description ?></div>

		</div>

<?php if (!empty($model->account_name) && !empty($model->account) && !empty($model->bank)) { ?>
			<div class="part">
				<div class="label"><span></span>资金监管账户</div>
				<table>
					<tr>
						<td>账户名称</td>
						<td>银行帐号</td>
						<td>开户行</td>
					</tr>
					<tr>
						<td><?= $model->account_name ?></td>
						<td><?= $model->account ?></td>
						<td><?= $model->bank ?></td>
					</tr>
				</table>
			</div>
<?php } ?>
				<?php if (isset($field[1])) { ?>
			<div class="part">
				<div class="label"><span></span>投资要求</div>
				<table>
	<?php foreach ($field[1] as $key => $val) { ?>
						<tr>
							<td><?= $val['name'] ?></td>
							<td><?= $val['content'] ?></td>
						</tr>
			<?php } ?>  
				</table>
			</div>
<?php } ?>

				<?php if (isset($field[2])) { ?>
			<div class="part" style="border-bottom: none;">
				<div class="label"><span></span>备查文件</div>
				<table style="width: 60%">
	<?php foreach ($field[2] as $key => $val) { ?>
						<tr>
							<td style="text-align: center"><?= $key + 1 ?></td>
							<td style="text-align: center"><?= $val['name'] ?></td>
							<td style="text-align: center; color:#0088cc">
								<?php if (Yii::$app->user->id) { ?>
									<?php if (empty($val['content'])) { ?> 
										本中心留档备查
			<?php } else { ?> 
										<?php if ($allow) { ?>
											<a href="/upload/product/<?= $val['content'] ?>" target="_blank" style="color:#0088cc">点击查看</a>
											&emsp;
											<a href="/download.php?file=<?= $val['content'] ?>" style="color:#0088cc">点击下载</a>
										<?php } else { ?>
											<a href="javascript:alert('无权限查看');" target="_blank" style="color:#0088cc">点击查看</a>
											&emsp;
											<a href="javascript:alert('无权限下载');" style="color:#0088cc">点击下载</a>
										<?php } ?>
									<?php } ?> 
		<?php } else { ?>
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
<script type="text/javascript">
	$(function () {
		GetRTime();
	})
</script>