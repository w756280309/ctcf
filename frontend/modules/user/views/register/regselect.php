<?php
use yii\widgets\ActiveForm;
?>

<div class="body">
	<div class="register_top">
		<ul class="bnav">
			<li class="arrow">
				<a href="/">首页</a>
			</li>
			<li>
				个人注册
			</li>
		</ul>
	</div>
	<div class="register_center register_select">
		<ul>
			<li>
				<a id="regsel_top" class="regclass" href="/user/register/reg?step=1&type=1">
					<span></span>
					<p>个人用户注册</p>
				</a>
			</li> 
			<li>
				<a id="regsel_bot" class="regclass" href="/user/register/reg?step=1&type=2">
					<span></span>
					<p>企业机构注册</p>
				</a>
			</li> 
		</ul>
		<div class="clear"></div>
	</div>
</div>
<script>
	$(function(){
		$('#regsel_top').hover(function(){
			$(this).find('span').css("background-position","0 -136px");
			$(this).find('p').css("color","#ff7800");
		},function(){
			$(this).find('span').css("background-position","0 0");
			$(this).find('p').css("color","#5d5d5d");
		});
		$('#regsel_bot').hover(function(){
			$(this).find('span').css("background-position","-174px -136px");
			$(this).find('p').css("color","#ff7800");
		},function(){
			$(this).find('span').css("background-position","-174px 0");
			$(this).find('p').css("color","#5d5d5d");
		});
	})
</script>

