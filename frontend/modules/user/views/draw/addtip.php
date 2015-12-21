<?php
$this->registerCssFile('/css/present.css', ['depends' => 'yii\web\YiiAsset']);
$status = Yii::$app->request->get('status');
if ($status == add) {
	$this->title = "添加银行卡";
} else {
	$this->title = "添加银行卡——审核中";
}
?>
<div class="fr page-right addtip">
	<div class="page-rigth-title" style="border: 0;margin-bottom:12px;">
		<div class="tab"><a class="tab-left">提现</a><a class="tab-right">提现提示</a></div>
	</div>
	<div class="page-right-detail">
		<div class="div-1">
			<ul>
				<li>
					<div class="div-1-1">
						<a href="#">
							<?php if ($status == add) { ?>
								<p class="tender-withdrawcash-div-p1">+</p>
								<p class="tender-withdrawcash-div-p2">添加银行卡</p>
							<?php } else { ?>
								<p class="addwait">绑定申请审核中</p>
							<?php } ?>

						</a>
					</div>
				</li>
			</ul>
		</div>
		<div class="clear"></div>
		<ul class="addtip-ul">
			<li class="addtip-ul-li-1">提现前需要先绑定一张银行卡；</li>
			<li class="addtip-ul-li-2">绑定的银行卡姓名需要和用户实名认证的姓名一致</li>
		</ul>
	</div>
</div>



