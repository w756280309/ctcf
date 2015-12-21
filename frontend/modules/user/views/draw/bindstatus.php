<?php
$this->registerCssFile('/css/present.css', ['depends' => 'yii\web\YiiAsset']);
if ($status == success) {
	$this->title = "绑定成功";
} else {
	$this->title = "绑定失败";
}
?>
<?php if ($status == 'success') { ?>
	<div class="body">
		<div class="draw bindstatus">
			<p class="ob1"><span class="ob_s1"></span>银行卡绑定成功！</p>
			<div class="ob2">
				<span class="ob2_s"><a class="ob2_s_a" href="/user">确定</a></span>
			</div>
		</div>
	</div>
<?php } else if($status == 'certification') { ?>
	<div class="body">
		<div class="draw bindstatus">
			<p class="ob1 ob1-1"><span class="ob_s1-1"></span>请先实名认证！</p>
			<div class="ob2">
				<span class="ob2_tip">若有疑问，请联系金交中心客服：025-8570-8888 （09:00-17:00）</span>
				<span class="ob2_s"><a class="ob2_s_a" href="/product">确定</a></span>
			</div>
		</div>
	</div>
<?php } else { ?>
	<div class="body">
		<div class="draw bindstatus">
			<p class="ob1 ob1-1"><span class="ob_s1-1"></span>银行卡绑定失败！</p>
			<div class="ob2">
				<span class="ob2_tip">若有疑问，请联系金交中心客服：025-8570-8888 （09:00-17:00）</span>
				<span class="ob2_s"><a class="ob2_s_a" href="/product">确定</a></span>
			</div>
		</div>
	</div>
<?php } ?>


