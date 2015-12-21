<?php
$this->registerCssFile('/css/tender.css',['depends' => 'yii\web\YiiAsset'] );
$this->title = "投标失败";
?>

<div class="body">
	<div class="tendersuccess_center">
		<p class="tendersuccess_form_success_p tendersuccess_form_defeat_p"><span class="tendersuccess_form_defeat"></span><?=$msg ?>！</p>
		<div class="tendersuccess_form_success_div">
			<span class="tendersuccess_form_defeat_tip">若有疑问，请联系金交中心客服：025-8570-8888 （09:00-17:00）</span>
		</div>
	</div>
</div>