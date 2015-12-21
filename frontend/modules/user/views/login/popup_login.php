<?php

use yii\widgets\ActiveForm;

$this->registerCssFile('/css/popup.css', ['depends' => 'yii\web\YiiAsset']);
?>
<?php $form = ActiveForm::begin(['id' => 'user_form', 'action' => "/user/login/popup-login"]); ?>
	<style>
.tender-login-deng{text-align: center;line-height: 154px;}		
.tender-login{font-family:'微软雅黑';}
.tender-login .field-loginform-username{padding-left:13px;padding-top:20px;height:50px;font-size:14px;}
.tender-login .user{color:#000;border:1px solid #e6e6e6;border-radius:3px;height:25px;width:213px;text-indent:7px;}
.tender-login .password{color:#000;border:1px solid #e6e6e6;border-radius:3px;height:25px;width:213px;text-indent:7px;}
.tender-login div div.tip{float:left;}
.tender-login div div.text{width:222px;float:left;height: 48px;}
.tender-login .tender-login-fontcolor{color:#474747;font-size:14px;}
.tender-login .field-loginform-password{padding-left:26px;}
.tender-login .field-loginform-username .help-block{text-indent:56px;color:red;line-height: 26px;font-size:12px;}
.tender-login .field-loginform-password .help-block{text-indent:56px;color:red;line-height: 26px;font-size:12px;}
.tender-login .cs a{color:#474747;text-decoration: none;line-height: 24px;font-size:14px;}
.tender-login-csx .csx{width: 80px;height:28px;background:#ff7800;color:#fff;border-radius:5px;border:0;cursor:pointer;margin-left:143px;margin-top: 23px;}
.tender-login .clear{clear:both;}
	</style>
<?php if ($op == "refresh") { ?>
	<script type="text/javascript">
		parent.location.reload();
	</script>
	<div class="main tender-login-deng">
		正在登录，请稍后
	</div>
<?php } else { ?>
	<div class="main tender-login">
		
		<?=
		$form->field($model, 'username', [
			'labelOptions' => ['class' => 'tender-login-fontcolor'],
			'inputOptions' => ['class' => 'user', 'id' => 'user', 'tabindex' => '1','placeholder' => '请输入您的用户名'],
			'template' => '<div class="tip">{label}：</div><div class="text">{input}{error}</div><div class="cs"><a href="/user/register/prereg" target="_blank">免费注册</a></div>'])->textInput();
		?>
		<?=
		$form->field($model, 'password', [
			'labelOptions' => ['class' => 'tender-login-fontcolor'],
			'inputOptions' => ['class' => 'password', 'id' => 'user', 'tabindex' => '2','placeholder' => '请输入您的登录密码'],
			'template' => '<div class="tip">{label}：</div><div class="text">{input}{error}</div><div class="cs"><a href="/user/find?step=1" target="_blank">忘记密码</a></div>'])->passwordInput();
		?>
	</div>
	<div class="clear"></div>
	<div class="button tender-login-csx">
		<button class="csx" type="submit"  tabindex="3">登录</button>
<!--		<button type="reset"  tabindex="4">重置</button>-->
	</div>
<?php } ?>
<?php ActiveForm::end(); ?>
