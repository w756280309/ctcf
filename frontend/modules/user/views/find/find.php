<?php
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;

$this->title = "找回密码";
?>
<style type="text/css">
    .step1 .field-user-mobile .help-block{
        width:640px;font-size:14px;height:28px;line-height:14px;color: #FF0000;padding-left:11px;padding-top: 40px;
        text-indent: 20px;
    }
    .step1 .field-user-verifycode .help-block{
        width:640px;font-size:14px;height:28px;line-height:89px;color: #FF0000;padding-left:11px;text-indent: 20px;
    }
    
    .step2 .help-block{
        width:640px;font-size:14px;height:28px;line-height:14px;color: #FF0000;padding-left:11px;padding-top: 40px;
        text-indent: 20px;
    }
    .paw-text .help-block{
        padding-left:210px;
    }
</style>
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
	<div class="register_center phone_pwd">
            <h2 class="phone_pwd_h2">找回密码</h2>
            <p class="phone_pwd_p">通过绑定手机找回密码</p>
            <div class="register_c2">
            <?php $form = ActiveForm::begin(['id'=>'find_form', 'action' =>"/user/find?step=".$step.'&mobile='.$mobile ]); ?>

            <?php if($step==1){?>
            <div class="step1">

                    <ul class="register_form">
                            <li class="register_form_li_height">
                                    <div class="register_xing"><span class="register_xing_k"></span><span class="register_xing_span"></span>输入手机号码</div>	
                            </li>
                            <li class="register_form_li_width">
                                    <?= $form->field($model, 'mobile',
                                            [
                                            'inputOptions'=>['class'=>'username phone_pwd_username','placeholder'=>'请输入您的手机号','tabindex'=>'1'],
                                            'template' => '<div class="mobile-line phone_pwd_line">{input}
                                            <span class="tip"></span></div>
                                            '])->textInput();?>
                                            <?=$form->field($model, 'mobile', ['template' => '{error}']) ?>

                                    </li>
                            </ul>
                            <div class="clear"></div>
                            <style>
                               .required img{padding-top:23px;padding-left:10px;float:left;}
                            </style>
                            <ul class="register_form">
                                    <li class="register_form_li_height">
                                            <div class="register_xing_na sendcode phone_pwd_na"><span class="register_xing_k"></span><span class="register_xing_span"></span>验证码</div>	
                                    </li>
                                    <li class="register_form_li_width" style="width:530px;">
                                            <?= $form->field($model, 'verifyCode',
                                                    [
                                                    'inputOptions'=>['class'=>'username  phone_pwd_username_yanzheng','placeholder'=>'','tabindex'=>'2'],
                                                    'template' => '<div class="password-line phone_pwd_line_yanzheng">{input}
                                                    <span class="tip"></span></div>
                                                    '])->textInput();?>
                                            <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                                                    'template' => '{image}','captchaAction'=>'/site/captcha'
                                                    ]) ?>
                                                    <p class="sms_code_tip" style="color:red"></p>
                                            </li>
                                    </ul>
                                    <div class="clear"></div>

                                    <p class="register_form_sub phone_pwd_sub" style="position:static;padding-top: 20px;"><input type="submit" value="下一步"/></p>
                            </div>
            <?php }else if($step==2){?>
                <div class="step2">
                            <?= $form->field($model, 'mobile',
                            [
                            'template' => '{input}
                            '])->hiddenInput();?>
                            <ul class="register_form">
                                <li class="register_form_li_height">
                                    <li class="register_form_li_height">短息验证码将发送到<span><?=$model->mobile?></span>手机上，请注意查收</li>
                                </li>
                            </ul>
                    
                    
                            
                    <ul class="register_form">
                            <li class="register_form_li_height"><span class="popup_paw_ul_span">*</span>手机验证码</li>
                            <li class="register_form_li_width">
                                    <?= $form->field($model, 'sms_code',
                                            [
                                            'inputOptions'=>['class'=>'username phone_pwd_username_yanzheng','placeholder'=>'','tabindex'=>'1'],
                                            'template' => '<div class="mobile-line phone_pwd_line_yanzheng">{input}
                                            <span class="tip"></span></div>
                                            <div class="paw-text">{error}</div>
                                            '])->textInput();?>
                                    </li>
                                    <li>
                                            <input class="popup_paw_input popup_nana_yanzheng popup_jinyong" type="button" id="btn" value="获取验证码" />
                                    </li>
                            </ul>
                            <div class="clear"></div>
                            <ul class="register_form">
                            <li class="register_form_li_height">
                                    <div class="register_xing"><span class="register_xing_k"></span><span class="register_xing_span"></span>重置密码</div>	
                            </li>
                            <li class="register_form_li_width">
                                    <?= $form->field($model, 'f_pwd',
                                            [
                                            'inputOptions'=>['class'=>'username phone_pwd_username','placeholder'=>'请输入您的新密码','tabindex'=>'4'],
                                            'template' => '<div class="mobile-line phone_pwd_line">{input}
                                            <span class="tip"></span></div>
                                            '])->passwordInput();?>
                                            <?=$form->field($model, 'f_pwd', ['template' => '{error}']) ?>

                                    </li>
                            </ul>
                            <div class="clear"></div>
                            <ul class="register_form">
                                    <li class="register_form_li_height">
                                            <div class="register_xing"><span class="register_xing_k"></span><span class="register_xing_span"></span>确认密码</div>	
                                    </li>
                                    <li class="register_form_li_width">
                                            <?= $form->field($model, 'c_f_pwd',
                                                    [
                                                    'inputOptions'=>['class'=>'username phone_pwd_username','placeholder'=>'请确认您的密码','tabindex'=>'4'],
                                                    'template' => '<div class="mobile-line phone_pwd_line">{input}
                                                    <span class="tip"></span></div>
                                                    '])->passwordInput();?>
                                                    <?=$form->field($model, 'c_f_pwd', ['template' => '{error}']) ?>

                                            </li>
                                    </ul>
                                    <p class="register_form_sub phone_pwd_khsub_2"><input type="submit" value="完成"/></p>
                                    <div class="clear"></div>
                            </div>
            <?php }?>
                            <?php ActiveForm::end(); ?>
                    </div>
            </div>
</div>  
			

			<script type="text/javascript"> 
                                $(function(){
                                    $('.field-user-verifycode label').remove();
                                })
				var wait=60; 
				function time(o) { 
					if (wait == 0) { 
						o.removeAttribute("disabled"); 
						o.value="获取验证码"; 
						wait = 60; 
					} else { 
						o.setAttribute("disabled", true); 
						o.value=wait+"秒后可重发"; 
						wait--; 
						setTimeout(function() { 
							time(o) 
						}, 
						1000) 
					} 
				} 
				document.getElementById("btn").onclick=function(){
                                    mobile='<?=$model->mobile?>';
                                    csrf = $($("input[name='_csrf']").get(0)).val();
                                    if($(this).attr('disabled')==undefined){

                                        if(mobile==""){
                                            alert("请输入手机号");return false;
                                        }
                                        if(!/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/i.test(mobile))
                                        {
                                          alert('非法的手机号');return false;
                                        }
                                        var uid = '<?=$model->id?>';
                                        $.ajax({
                                                type: "post",
                                                url: "/user/register/checkfindmobile?mobile="+mobile+"&uid="+uid+'&method='+1+"&temp=16777",
                                                data: {_csrf:csrf},
                                                //url: "/user/default/checkmobile",
                                                //data: {mobile:mobile,uid:uid,'method':1,temp:16777,_csrf:csrf},
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
                                    time(this);
                                } 
			</script>
