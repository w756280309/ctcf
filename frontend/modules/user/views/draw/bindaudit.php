<?php
$this->registerCssFile('/css/present.css', ['depends' => 'yii\web\YiiAsset']);
$this->title = "银行卡绑定审核";
?>

<div class="body">
	<div class="draw bindaudit">
		<div class="ob1"><span class="ob_s1 bindaudit-s"></span><div>银行卡绑定成功！</div><div>感谢您的支持！</div>
<!--                    <div>银行卡绑定请求已经提交，</div>
                    <div>审核流程一般会在1-2个工作日完成，感谢您的支持！</div>-->
                        
                </div>
		<div class="ob2">
			<span class="ob2_tip">若有疑问，请联系金交中心客服：025-8570-8888 （09:00-17:00）</span>
			<span class="ob2_s"><a class="ob2_s_a" href="/user">确定</a></span>
		</div>
	</div>
</div>

