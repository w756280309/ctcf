<?php

use yii\helpers\Html;
use frontend\assets\AppAsset;
use yii\widgets\ActiveForm;

AppAsset::register($this);
$this->registerJsFile('js/jquery-1.11.1.min.js', ['depends' => 'yii\web\YiiAsset', 'position' => 1]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
        <link rel="shortcut icon" href="/images/favicon.ico" type="imagend.microsoft.icon">
		<?php $this->head() ?>
        <link href="/css/common.css" rel="stylesheet">
        <link href="/css/popup.css" rel="stylesheet">
        <style type="text/css">
            .tender-login-deng{text-align: center;line-height: 75px;}
			.settradepwd-div div{height:53px;}
			.settradepwd-div p {margin: 11px auto;}
			.qfds{padding-top:13px;padding-bottom:18px;}
			.qfds p{width:345px;margin:0 auto;}
			.popup_paw_ul {padding-top:0px;}
			.popup_paw_enter input{margin-top:0;}
			.popup_paw_enter a{margin-top:0;}
			.popup_paw_form_li_2 {padding-left: 24px;}
			.popup_paw_form_li_2_left{padding-left: 10px;}
        </style>
		<script>
			function lout(re) {
                            if(re==1){
                                window.parent.location.reload()
                            }
				var index = window.parent.getlay();
				//parent.location.href="/user/login/logout";
				parent.layer.close(index);
			}

		</script>
	</head>
	<body>
		<?php $this->beginBody() ?>
		<?php if ($res == '100') { ?>
			<div class="main tender-login-deng settradepwd-div">
				<div></div>
				<span>
				交易密码设置成功，请牢记
				</span>
				<p class="popup_email_enter_in"><input type="button" value="确定" onclick="lout(1)" /></p>
			</div>
		<?php } else if ($res == '101') { ?>
			<div class="main tender-login-deng settradepwd-div">
				<span>
				交易密码设置失败，请联系客服
				</span>
				<p class="popup_email_enter_in"><input type="button" value="关闭" onclick="lout(0)" /></p>
			</div>
                <?php } else if ($res == '102') { ?>
			<div class="main tender-login-deng settradepwd-div">
				<span>
				交易密码修改失败
				</span>
                            <p class="popup_email_enter_in"><input type="button" value="返回" onclick="history.go(-1)" style="display:inline" />&emsp;<input type="button" value="关闭" onclick="lout(0)" style="display:inline" /></p>
			</div>
		<?php } else { ?>    
                        
			<?php $form = ActiveForm::begin(['id' => 'popup_form', 'action' => "/user/draw/settradepwd"]); ?>
                        <div class="qfds"><p>为保证资金安全，在南京金融资产交易中心系统中进行提现，投标等操作时，均需要输入交易密码</p></div> 
                        <?php if($set==0){ ?>
                        <!--设置交易密码-->
			
			<ul class="popup_paw_ul popup_paw_ul_wkl settradepwd-ul">
				<li class="popup_paw_form_li_1 popup_paw_form_li_1_en"><span class="popup_paw_ul_span"></span>设置交易密码</li>
				<li class="popup_paw_form_li_2">
					<?=
					$form->field($model, 'f_trade_pwd', [
						'inputOptions' => ['class' => 'popup_paw_input', 'placeholder' => '(8-16位密码,数字或字母组合)', 'tabindex' => '1'],
						'template' => '<div class="username-line popup_paw-input">{input}
				<span class="tip"></span></div>
				<div class="paw-text">{error}</div>
				'])->passwordInput();
					?>
				</li>
			</ul>
			<div class="clear"></div>
			<ul class="popup_paw_ul popup_paw_ul_wkl settradepwd-ul settradepwd-ul-en">
				<li class="popup_paw_form_li_1 popup_paw_form_li_1_en"><span class="popup_paw_ul_span"></span>确认交易密码</li>
				<li class="popup_paw_form_li_2">
					<?=
					$form->field($model, 'confirm_trade_pwd', [
						'inputOptions' => ['class' => 'popup_paw_input', 'placeholder' => '请再次输入交易密码', 'tabindex' => '1'],
						'template' => '<div class="username-line popup_paw-input">{input}
				<span class="tip"></span></div>
				<div class="paw-text">{error}</div>
				'])->passwordInput();
					?>
				</li>
			</ul>
			
                        <?php }else{ ?>
                        <!--修改交易密码-->
                        <ul class="popup_paw_ul popup_paw_ul_wkl settradepwd-ul">
				<li class="popup_paw_form_li_1 popup_paw_form_li_1_en"><span class="popup_paw_ul_span"></span>原交易密码</li>
				<li class="popup_paw_form_li_2">
					<?=
					$form->field($model, 'old_trade', [
						'inputOptions' => ['class' => 'popup_paw_input', 'placeholder' => '(8-16位密码,数字或字母组合)', 'tabindex' => '1','value'=>""],
						'template' => '<div class="username-line popup_paw-input">{input}
				<span class="tip"></span></div>
				<div class="paw-text">{error}</div>
				'])->passwordInput();
					?>
				</li>
			</ul>
			<div class="clear"></div>
                        
                        <ul class="popup_paw_ul popup_paw_ul_wkl settradepwd-ul">
				<li class="popup_paw_form_li_1 popup_paw_form_li_1_en"><span class="popup_paw_ul_span"></span>新交易密码</li>
				<li class="popup_paw_form_li_2">
					<?=
					$form->field($model, 'new_trade', [
						'inputOptions' => ['class' => 'popup_paw_input', 'placeholder' => '(8-16位密码,数字或字母组合)', 'tabindex' => '2'],
						'template' => '<div class="username-line popup_paw-input">{input}
				<span class="tip"></span></div>
				<div class="paw-text">{error}</div>
				'])->passwordInput();
					?>
				</li>
			</ul>
			<div class="clear"></div>
			<ul class="popup_paw_ul popup_paw_ul_wkl settradepwd-ul settradepwd-ul-en">
				<li class="popup_paw_form_li_1 popup_paw_form_li_1_en"><span class="popup_paw_ul_span"></span>确认交易密码</li>
				<li class="popup_paw_form_li_2 popup_paw_form_li_2_left">
					<?=
					$form->field($model, 'new_trade_confirm', [
						'inputOptions' => ['class' => 'popup_paw_input', 'placeholder' => '请再次输入交易密码', 'tabindex' => '3'],
						'template' => '<div class="username-line popup_paw-input">{input}
				<span class="tip"></span></div>
				<div class="paw-text">{error}</div>
				'])->passwordInput();
					?>
				</li>
			</ul>
                        
                        <?php } ?>
                        
                        <div class="clear"></div>
			<ul class="popup_paw_enter settradepwd-sub">
				<li><input type="submit" value="确 定" onclick="" /><li>
				<li><a onclick="lout()" style="cursor:pointer;">取消</a></li>
			</ul>
                        
			<?php ActiveForm::end(); ?>

			
		<?php } ?>   
                <?php $this->endBody() ?>
	</body>
</html>
<?php $this->endPage() ?>