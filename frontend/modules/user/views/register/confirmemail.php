<?php
use yii\widgets\ActiveForm;
$this->registerCssFile('/css/regcss.css',['depends' => 'yii\web\YiiAsset'] );
$this->title = "邮箱验证成功";
?>

<!--个人用户-->
<div class="body">
	<div class="register_top">
		<ul class="bnav">
			<li class="arrow">
				<a href="/">首页</a>
			</li>
			<li>
				<?=$this->title ?>
			</li>
		</ul>
	</div>
	<!--注册第一步-->
	<div class="register_center">
		
		<div class="register_c2">
			
                                    <p class="register_form_success_p"><span class="register_form_success"></span><?= $model->email ?>,邮箱验证成功</p>
                                    <div class="register_form_success_div">
                                         <span class="register_form_success_pp"><a class="register_form_success_a1" href="/user">去用户中心</a></span>
                                         <span class="register_form_success_ppp"><a class="register_form_success_a2" href="/">回首页</a></span>
                                    </div>
                               
		</div>
	</div>
	
	
</div>

<script>
	$(function(){

	})
</script>
