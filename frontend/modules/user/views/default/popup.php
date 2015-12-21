<?php
use yii\helpers\Html;
use frontend\assets\AppAsset;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;
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
        
</head>
<body>
 <?php $this->beginBody() ?>
<!--邮箱-->
<?php if($op=='email'){ ?>

	<?php $form = ActiveForm::begin(['id'=>'popup_form', 'action' =>"/user/default/popup?op=".$op ]); ?>
	<ul class="popup_email_ul">
		<li class="popup_email_form_li_1"><span class="popup_email_ul_span">*</span> 邮箱</li>
		<li class="popup_email_form_li_2">
			<?= $form->field($model, 'email',
				[
				'inputOptions'=>['class'=>'popup_email_input','placeholder'=>'请输入您的邮箱','tabindex'=>'1','readonly'=>'true'],
				'template' => '<div class="username-line popup_email-input">{input}
				<span class="tip"></span></div>
				<div class="email-text">{error}</div>
				'])->textInput();?>
		</li>
	</ul>
		<p class="popup_email_enter_in"><input type="submit" value="立即验证"onclick=""/></p>
		<?php ActiveForm::end(); ?>

<?php }else if($op=='email_examin'){ ?>
<div class="popup_email1">
	<p class="popup_email_pp">我们已经发送了一封验证邮件到您的</p>
	<p class="popup_email_ppp">***@****.com邮箱，请进行如邮箱进行认证。</p>
	<p class="popup_email_enter"><a href="<?=$email; ?>" target="_blank">进入邮箱</a></p>
</div>
<?php }else if($op=='email_edit'){ ?>
<div class="popup_email1">
	<?php $form = ActiveForm::begin(['id'=>'popup_form', 'action' =>"/user/default/popup?op=".$op ]); ?>
	<ul class="popup_email_ul">
		<li class="popup_email_form_li_1"><span class="popup_email_ul_span">*</span> 邮箱</li>
		<li class="popup_email_form_li_2">
			<?= $form->field($model, 'email',
				[
				'inputOptions'=>['class'=>'popup_email_input','placeholder'=>'请输入您的邮箱','tabindex'=>'1'],
				'template' => '<div class="username-line popup_email-input">{input}
				<span class="tip"></span></div>
				<div class="email-text">{error}</div>
				'])->textInput();?>
		</li>
	</ul>
		<p class="popup_email_enter_in popup_email_enter_width"><input type="submit" value="确认修改并验证"onclick=""/></p>
		<?php ActiveForm::end(); ?>
</div>
<?php }else if($op=='pwd_edit'){ ?>
<!--密码-->
<style>
	.popup_paw_ul_wkl{display:block;width:390px;height:20px;}

</style>
	<?php $form = ActiveForm::begin(['id'=>'popup_form', 'action' =>"/user/default/popup?op=".$op ]); ?>
	<ul class="popup_paw_ul popup_paw_ul_wkl">
		<li class="popup_paw_form_li_1"><span class="popup_paw_ul_span">*</span>原密码</li>
		<li class="popup_paw_form_li_2">
			<?= $form->field($model, 'old_password',
				[
				'inputOptions'=>['class'=>'popup_paw_input','placeholder'=>'请输入原登录密码','tabindex'=>'1'],
				'template' => '<div class="username-line popup_paw-input">{input}
				<span class="tip"></span></div>
				<div class="paw-text">{error}</div>
				'])->passwordInput();?>
		</li>
	</ul>
	<ul class="popup_paw_ul popup_paw_ul_wkl">
		<li class="popup_paw_form_li_1"><span class="popup_paw_ul_span">*</span>新密码</li>
		<li class="popup_paw_form_li_2">
			<?= $form->field($model, 'new_password',
				[
				'inputOptions'=>['class'=>'popup_paw_input','placeholder'=>'输入6位以上字母、数字和符号','tabindex'=>'1'],
				'template' => '<div class="username-line popup_paw-input">{input}
				<span class="tip"></span></div>
				<div class="paw-text">{error}</div>
				'])->passwordInput();?>
		</li>
	</ul>
	<ul class="popup_paw_ul popup_paw_ul_wkl">
		<li class="popup_paw_form_li_1 popup_paw_form_li_1_en"><span class="popup_paw_ul_span">*</span>确认新密码</li>
		<li class="popup_paw_form_li_2">
			<?= $form->field($model, 'new_confirm_password',
				[
				'inputOptions'=>['class'=>'popup_paw_input','placeholder'=>'请再次输入您的新密码','tabindex'=>'1'],
				'template' => '<div class="username-line popup_paw-input">{input}
				<span class="tip"></span></div>
				<div class="paw-text">{error}</div>
				'])->passwordInput();?>
		</li>
	</ul>
	<div class="clear"></div>
		<ul class="popup_paw_enter">
		     <li><input type="submit" value="确 定" onclick="" /><li>
		</ul>
		<?php ActiveForm::end(); ?>
<?php }else if($op=='pwd_edit_over'){ ?>
<div class="popup_email1">
	<p class="popup_email_pp">密码修改成功</p>
        <p class="popup_email_ppp">请进行<a ahref="/user/login/logout" href="javascript:void(0);" onclick="lout();">退出</a>操作。</p>
</div>
        <script type="text/javascript">
        function lout(){
            //var index = window.parent.getlay();
            parent.location.href="/user/login/logout";
            //parent.layer.close(index);
        }
        </script>
<?php }else if($op=='pwd_find'){ ?>
    
<?php }else if($op=='mobile_bind'){ ?>
<script type="text/javascript">
var mobile = '<?= $model->mobile ?>';
var temp = 17342;
</script>
<style>
	.popup_paw_ul_hw{display:block;width:380px;height:20px;}
</style>

	<?php $form = ActiveForm::begin(['id'=>'popup_form', 'action' =>"/user/default/popup?op=".$op ]); ?>
        <?= $form->field($model, 'mobile',
				['template' => '{input}'])->hiddenInput();?>
	<ul class="popup_paw_ul popup_paw_ul_hw">
		<li class="popup_paw_form_li_1 popup_nana_form_li_1"><span class="popup_paw_ul_span">*</span>原手机号码</li>
		<li class="popup_paw_form_li_2">
            <p class="popup_paw_change_li_p"><?= $model->mobile ?></p>
		</li>
	</ul>
	<ul class="popup_paw_ul popup_paw_change_ul popup_paw_ul_hw">
		<li class="popup_paw_form_li_1  popup_nana_form_li_1"><span class="popup_paw_ul_span">*</span>手机验证码</li>
		<li class="popup_paw_form_li_2">
			<?= $form->field($model, 'sms_code',
				[
				'inputOptions'=>['class'=>'popup_paw_input popup_nana_input','placeholder'=>'','tabindex'=>'1'],
				'template' => '<div class="username-line popup_paw-input">{input}
				<span class="tip"><input class="popup_paw_input popup_nana_yanzheng popup_jinyong discuz_popup" type="button" id="btn" value="获取验证码" /></span></div>
				<div class="paw-text">{error}</div>
				'])->textInput();?>
		</li>
	</ul>
	<ul class="popup_paw_ul popup_paw_deng popup_paw_ul_hw">
		<li class="popup_paw_form_li_1 popup_qing_form_li_1"><span class="popup_paw_ul_span">*</span>登录密码</li>
		<li class="popup_paw_form_li_2">
			<?= $form->field($model, 'password',
				[
				'inputOptions'=>['class'=>'popup_paw_input','placeholder'=>'请输入您的登录密码','tabindex'=>'1'],
				'template' => '<div class="username-line popup_paw-input">{input}
				<span class="tip"></span></div>
				<div class="paw-text">{error}</div>
				'])->passwordInput();?>
		</li>
	</ul>
	<div class="clear"></div>
	<div class="popup_nana_height"></div>
	<p class="popup_email_enter_in"><input type="submit" value="下一步"onclick=""/></p>
	<?php ActiveForm::end(); ?>

<?php }else if($op=='mobile_edit'){ ?>
<script type="text/javascript">
var mobile = '';
var temp = 17342;
</script>
	<?php $form = ActiveForm::begin(['id'=>'popup_form', 'action' =>"/user/default/popup?op=".$op ]); ?>
	<ul class="popup_paw_ul popup_paw_deng" style="display:block;width:380px;height:20px;">
		<li class="popup_paw_form_li_1 popup_nana_form_li_1"><span class="popup_paw_ul_span">*</span>新手机号码</li>
		<li class="popup_paw_form_li_2">
			<?= $form->field($model, 'new_mobile',
				[
				'inputOptions'=>['class'=>'popup_paw_input','placeholder'=>'请输入您的新号码','tabindex'=>'1'],
				'template' => '<div class="username-line popup_paw-input">{input}
				<span class="tip"></span></div>
				<div class="paw-text">{error}</div>
				'])->textInput();?>
		</li>
	</ul>
	<ul class="popup_paw_ul popup_paw_change_ul" style="display:block;width:380px;height:20px;">
		<li class="popup_paw_form_li_1  popup_nana_form_li_1"><span class="popup_paw_ul_span">*</span>手机验证码</li>
		<li class="popup_paw_form_li_2">
			<?= $form->field($model, 'sms_code',
				[
				'inputOptions'=>['class'=>'popup_paw_input popup_nana_input','placeholder'=>'','tabindex'=>'1'],
				'template' => '<div class="username-line popup_paw-input">{input}
				<span class="tip"></span></div>
				<div class="paw-text">{error}</div>
				'])->textInput();?>
		</li>
		<li>
			<input style="margin:0px;" class="popup_paw_input popup_nana_yanzheng popup_jinyong" type="button" id="btn" value="获取验证码" />
		</li>
	</ul>
	<div class="clear"></div>
	<div class="popup_nana_height"></div>
	<p class="popup_email_enter_in"><input type="submit" value="下一步"onclick=""/></p>
	<?php ActiveForm::end(); ?>
<?php }else if($op=='mobile_verify'){ ?>
<script type="text/javascript">
var mobile = '<?= $model->mobile?>';
var temp = 12552;
</script>
	<?php $form = ActiveForm::begin(['id'=>'popup_form', 'action' =>"/user/default/popup?op=".$op ]); ?>
	<ul class="popup_paw_ul popup_paw_deng" style="display:block;width:380px;height:20px;">
		<li class="popup_paw_form_li_1 popup_nana_form_li_1"><span class="popup_paw_ul_span">*</span>手机号码</li>
		<li class="popup_paw_form_li_2">
			<?= $form->field($model, 'mobile',
				[
				'inputOptions'=>['class'=>'popup_paw_input','placeholder'=>'','tabindex'=>'1','readonly'=>"readonly"],
				'template' => '<div class="username-line popup_paw-input">{input}
				<span class="tip"></span></div>
				<div class="paw-text">{error}</div>
				'])->textInput();?>
		</li>
	</ul>
	<ul class="popup_paw_ul popup_paw_change_ul" style="display:block;width:380px;height:20px;">
		<li class="popup_paw_form_li_1  popup_nana_form_li_1"><span class="popup_paw_ul_span">*</span>手机验证码</li>
		<li class="popup_paw_form_li_2">
			<?= $form->field($model, 'sms_code',
				[
				'inputOptions'=>['class'=>'popup_paw_input popup_nana_input','placeholder'=>'','tabindex'=>'1'],
				'template' => '<div class="username-line popup_paw-input">{input}
				<span class="tip"></span></div>
				<div class="paw-text">{error}</div>
				'])->textInput();?>
		</li>
		<li>
			<input style="margin:0px;" class="popup_paw_input popup_nana_yanzheng popup_jinyong" type="button" id="btn" value="获取验证码" />
		</li>
	</ul>
	<div class="clear"></div>
	<div class="popup_nana_height"></div>
	<p class="popup_email_enter_in"><input type="submit" value="下一步"onclick=""/></p>
	<?php ActiveForm::end(); ?>
<?php }else if($op=='mobile_edit_over'){ ?>
<div class="popup_email1">
	<p class="popup_email_pp">更换绑定手机成功</p>
</div>
<?php }else if($op=='mobile_verify_over'){ ?>
<div class="popup_email1">
	<p class="popup_email_pp">手机绑定成功</p>
</div>
<?php }else if($op=='pwd_find2'){ ?>
        
<?php } ?>
<script>
	$(function(){
		$('#clickoff').click(function(){
			$(this).parent('p').parent('div').css({"display":"none"});
		}),
		$('#clickend').click(function(){
			$(this).parents('.popup_paw').css({"display":"none"});
		})
	})	
</script>
<script type="text/javascript" src="/js/reg.js"></script>
<script type="text/javascript">
	var wait=60; 
	function time(o) { 
	if (wait == 0) { 
	o.removeAttribute("disabled"); 
	o.value="获取验证码"; 
	document.getElementById("btn").style.background= 'orange';
	document.getElementById("btn").style.color= 'white';
	wait = 60; 
	} else { 
               
	o.setAttribute("disabled", true); 
	o.value=wait+"秒后可重发"; 
	document.getElementById("btn").style.background= 'gray';
	document.getElementById("btn").style.color= 'orange';
	wait--; 
	setTimeout(function() { 
	time(o) 
	}, 
	1000) 
	} 
	} 
	document.getElementById("btn").onclick=function(){
            if($(this).attr('disabled')==undefined){
                
                if(mobile==''){
                    mobile=$('#user-new_mobile').val();
                    if(mobile==""){
                        alert("请输入手机号");return false;
                    }
                    if(!/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/i.test(mobile))
                    {
                      alert('非法的手机号');return false;
                    }
                }
                var uid = '<?=Yii::$app->user->id ?>';
                csrf = $($("input[name='_csrf']").get(0)).val();
                $.ajax({
                        type: "post",
                        url: "/user/default/checkmobile?mobile="+mobile+"&uid="+uid+'&method='+1+"&temp="+temp,
                        data: {_csrf:csrf},
                        dataType: "json",
                        success: function(data){  
                               if(data['code']==0){
                                   alert(data['msg']);return false;
                               }else{
                                   //$('.sms_code_tip').html('短信验证码已发，如未收到，请倒计时结束后点击<a href="javascript:sendSmsCode(2);" style="color:#0088cc">语音验证</a>');
                               }
                        }
                    });
            }
            //console.log($(this).attr('disabled'));
            time(this);
        } 
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>