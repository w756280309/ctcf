<?php
use yii\widgets\ActiveForm;

if($type==1){
    $title="个人用户";
}else if($type==2){
    $title="机构用户";
}
$this->title = $title."注册";
$this->registerCssFile('/css/regcss.css',['depends' => 'yii\web\YiiAsset'] );
$this->registerJs("var temp_uid='".$temp_uid."';",1);//在头部加载
$this->registerJsFile('/js/reg.js', ['depends' => 'yii\web\YiiAsset']);

?>
<style>
	.password-line{overflow:hidden;}
    .userpass_lw{width:219px;} 
    .org_step_second .has-error{width:262px;min-height:20px;}
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
	<div class="register_center">
		<div class="register_c1">
			<ul class="register_c1_ul">
                                <li class="register_c1_li <?php if($step==1){ ?>arrow<?php } ?>">注册新用户</li>
				<li class="register_c1_li_side"></li>
				<li class="register_c1_li <?php if($step==2){ ?>arrow<?php } ?>">完善资料</li>
				<li class="register_c1_li_side"></li>
				<li class="register_c1_li <?php if($step==3){ ?>arrow<?php } ?>">注册成功</li>
			</ul>
		</div>
		<div class="register_c2<?php if($type==2){ ?> register_c2_org_kw<?php } ?>">
			<?php $form = ActiveForm::begin(['id'=>'reg_form', 'action' =>"/user/register/reg?step=".$step."&type=".$type."&code=".$code ]); ?>
                        <input type="hidden" name="temp_uid" value="<?=$temp_uid ?>" />
                        <?=$form->field($model, 'type', ['template' => '{input}'])->hiddenInput(['value'=>$type]);?>
                        <?php if($step==1){ ?>
                    <div class="step1">
			<ul class="register_form">
				<li class="register_form_li_height">
					<div class="register_xing"><span class="register_xing_span"></span>输入用户名</div>	
				</li>
				<li class="register_form_li_width discuz_width_t1s1">
					<?= $form->field($model, 'username',
						[
						'inputOptions'=>['class'=>'username','placeholder'=>'用户名为5-16位字母，数字符号','tabindex'=>'1'],
						'template' => '<div class="username-line">{input}
						<span class="tip"></span></div>
						'])->textInput();?>
<!--                                        <div class="error-text"><span class="register_form_cha"></span>{error}</div>-->
                                    <?=$form->field($model, 'username', ['template' => '{error}']) ?>
					</li>
			</ul>
			<div class="clear"></div>
			<ul class="register_form">
				<li class="register_form_li_height">
					<div class="register_xing"><span class="register_xing_k"></span><span class="register_xing_span"></span>输入密码</div>	
				</li>
				<li class="register_form_li_width  discuz_width_t1s1">
					<?= $form->field($model, 'password',
						[
						'inputOptions'=>['class'=>'username userpass_lw','placeholder'=>'密码为6位以上字母,数字，符号','tabindex'=>'2'],
						'template' => '<div class="password-line">{input}
						<span class="tip"></span></div>
						'])->passwordInput();?>
                                            <?=$form->field($model, 'password', ['template' => '{error}']) ?>
					</li>
			</ul>
			<div class="clear"></div>
			<ul class="register_form">
				<li class="register_form_li_height">
					<div class="register_xing"><span class="register_xing_k"></span><span class="register_xing_span"></span>密码确认</div>	
				</li>
				<li class="register_form_li_width  discuz_width_t1s1">
					<?= $form->field($model, 'password_confirm',
						[
						'inputOptions'=>['class'=>'username','placeholder'=>'','tabindex'=>'3'],
						'template' => '<div class="password-line">{input}
						<span class="tip"></span></div>
						'])->passwordInput();?>
                                            <?=$form->field($model, 'password_confirm', ['template' => '{error}']) ?>
					</li>
			</ul>
			<div class="clear"></div>
			<ul class="register_form">
				<li class="register_form_li_height">
					<div class="register_xing"><span class="register_xing_k"></span><span class="register_xing_span"></span>手机号码</div>	
				</li>
				<li class="register_form_li_width  discuz_width_t1s1">
					<?= $form->field($model, 'mobile',
						[
						'inputOptions'=>['class'=>'username','placeholder'=>'','tabindex'=>'4'],
						'template' => '<div class="mobile-line">{input}
						<span class="tip"></span></div>
						'])->textInput();?>
                                    <?=$form->field($model, 'mobile', ['template' => '{error}']) ?>
                                    
					</li>
			</ul>
			<div class="clear"></div>
			<ul class="register_form" style="padding-bottom:3px;">
				<li class="register_form_li_height">
					<div class="register_xing_na sendcode"><span class="register_xing_k"></span><span class="register_xing_span"></span>验证码</div>	
				</li>
				<li class="register_form_li_width  discuz_width_t1s1">
					<?= $form->field($model, 'sms_code',
						[
						'inputOptions'=>['class'=>'validate-input','placeholder'=>'','tabindex'=>'5'],
						'template' => '<div class="validate-line">{input}
						<span class="tip"></span></div>
						<input class="register_btn register_code" id=btn type="button" value="获取验证码" onclick="" />
						'])->textInput();?>
                                    <?=$form->field($model, 'sms_code', ['template' => '{error}']) ?>
                                    
					</li>
			</ul>
			<p class="sms_code_tip" style="color:black;padding-top: 10px;padding-left:70px;"></p>
			<div class="clear"></div>
                        <div class="register_form_p" style="padding-bottom:0px">
                            
                            <?= $form->field($model, 'agree', [
                                        'inputOptions'=>['style'=>'height:20px; line-height: 20px; vertical-align: middle;','value'=>0],
                                        'template' => '{input}<label style="height:20px; line-height: 20px; vertical-align: middle;">我已阅读并同意南京交易中心的</label>'
                                            . '<a href="http://www.njfae.cn/news/about?aid=73&cid=8" target="_blank" style="height:20px; line-height: 20px; vertical-align: middle;">'
                                            . '<span class="register_blue">《服务协议》</span></a>'])->checkbox();?>
                            <?=$form->field($model, 'agree', ['template' => '{error}']) ?>                            
                        </div>
			<p class="register_form_sub"><input type="submit" value="注册"onclick=""/></p>
                        </div>
                        <?php }else if($step==2){ ?>
                            <?php if($type==1){ ?>
                            
                            <!--个人注册第二步 start-->
                            <div class="person_reg_second">
                            <ul class="register_form">
                                        <li class="register_form_li_height register_left_name">
                                                <div class="register_xing"><span class="register_xing_span"></span>会员姓名</div>	
                                        </li>
                                        <li class="register_form_li_width discuz_width_t1s2">
                                                <?= $form->field($model, 'real_name',
                                                        [
                                                        'inputOptions'=>['class'=>'username register_user','placeholder'=>'','tabindex'=>'1'],
                                                        'template' => '<div class="none-line">{input}
                                                        <span class="tip"></span></div>
                                                        '])->textInput();?>
                                            <?=$form->field($model, 'real_name', ['template' => '{error}']) ?>
                                                </li>
                                </ul>
                                <div class="clear"></div>
                                <ul class="register_form">
                                        <li class="register_form_li_height">
                                                <div class="register_xing"><span class="register_xing_k"></span><span class="register_xing_span"></span>会员身份证号</div>	
                                        </li>
                                        <li class="register_form_li_width  discuz_width_t1s2">
                                                <?= $form->field($model, 'idcard',
                                                        [
                                                        'inputOptions'=>['class'=>'username register_user','placeholder'=>'','tabindex'=>'2'],
                                                        'template' => '<div class="none-line">{input}
                                                        <span class="tip"></span></div>
                                                        '])->textInput();?>
                                            <?=$form->field($model, 'idcard', ['template' => '{error}']) ?>
                                                </li>
                                </ul>
                                <div class="clear"></div>
                                <ul class="register_form">
                                        <li class="register_form_li_height register_left_email">
                                                <div class="register_xing"><span class="register_xing_k"></span><span class="register_xing_span"></span>常用邮箱</div>	
                                        </li>
                                        <li class="register_form_li_width  discuz_width_t1s2">
                                                <?= $form->field($model, 'email',
                                                        [
                                                        'inputOptions'=>['class'=>'username register_user','placeholder'=>'','tabindex'=>'2'],
                                                        'template' => '<div class="none-line">{input}
                                                        <span class="tip"></span></div>
                                                        '])->textInput();?>
                                            
                                            <?=$form->field($model, 'email', ['template' => '{error}']) ?>
                                                </li>
                                </ul>
                                <div class="clear"></div>
                                <p class="register_form_sub register_form_sub_kw"><input type="submit" value="完成" onclick=""/></p>
                                </div>
                                <!--个人注册第二步 end-->

                            <?php }else if($type==2){ ?>
                            
                                <!--机构注册第二步 start -->
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="org_step_second">
                                    <tr class="register_tab_tr">
                                        <td class="register_tab_td_1" width="137"><span class="register_span_red">*</span>会员分类</td>
                                        <td width="262">
                                            <?= $form->field($model, 'cat_id',
                                                [
                                                'inputOptions'=>['class'=>'register_tab_td_select'],
                                                'template' => '{input}'
                                                ])->dropDownList(array('0'=>"--请选择--")+$category);?>
                                            
                                        </td>
                                        <td colspan="2"><?=$form->field($model, 'cat_id', ['template' => '{error}']) ?></td>
                                    </tr>
                                    <tr class="register_tab_tr">
                                        <td class="register_tab_td_1" width="137"><span class="register_span_red">*</span>机构名称</td>
                                        <td width="262" class="register_tab_td_2">
                                            <?= $form->field($model, 'org_name',
                                                                        [
                                                                        'inputOptions'=>['class'=>'register_tab_td_input','placeholder'=>'','tabindex'=>'1'],
                                                                        'template' => '<div class="none-line">{input}
                                                                        <span class="tip"></span></div>
                                                                        '])->textInput();?>
                                            <?=$form->field($model, 'org_name', ['template' => '{error}']) ?>
                                        </td>
                                        <td class="register_tab_td_1" width="166"><span class="register_span_red">*</span>联系人姓名</td>
                                        <td class="register_tab_td_2" width="382">
                                                <?= $form->field($model, 'real_name',
                                                                        [
                                                                        'inputOptions'=>['class'=>'register_tab_td_input','placeholder'=>'','tabindex'=>'1'],
                                                                        'template' => '<div class="none-line">{input}
                                                                        <span class="tip"></span></div>
                                                                        '])->textInput();?>
                                            <?=$form->field($model, 'real_name', ['template' => '{error}']) ?>
                                        </td>
                                    </tr>
                                    <tr class="register_tab_tr">
                                        <td class="register_tab_td_1" width="137"><span class="register_span_red">*</span>营业执照编号</td>
                                        <td width="262" class="register_tab_td_2">
                                            <?= $form->field($model, 'business_licence',
                                                                        [
                                                                        'inputOptions'=>['class'=>'register_tab_td_input','placeholder'=>'','tabindex'=>'1'],
                                                                        'template' => '<div class="none-line">{input}
                                                                        <span class="tip"></span></div>
                                                                        '])->textInput();?>
                                            <?=$form->field($model, 'business_licence', ['template' => '{error}']) ?>
                                        </td>
                                        <td class="register_tab_td_1" width="166"><span class="register_span_red">*</span>组织机构代码证号</td>
                                        <td class="register_tab_td_2" width="382">
                                                <?= $form->field($model, 'org_code',
                                                                        [
                                                                        'inputOptions'=>['class'=>'register_tab_td_input','placeholder'=>'','tabindex'=>'1'],
                                                                        'template' => '<div class="none-line">{input}
                                                                        <span class="tip"></span></div>
                                                                        '])->textInput();?>
                                            <?=$form->field($model, 'org_code', ['template' => '{error}']) ?>
                                        </td>
                                    </tr>
                                    <tr class="register_tab_tr">
                                        <td class="register_tab_td_1" width="137"><span class="register_span_red">*</span>税务登记证号</td>
                                        <td width="262" class="register_tab_td_2">
                                            <?= $form->field($model, 'shui_code',
                                                                        [
                                                                        'inputOptions'=>['class'=>'register_tab_td_input','placeholder'=>'','tabindex'=>'1'],
                                                                        'template' => '<div class="none-line">{input}
                                                                        <span class="tip"></span></div>
                                                                        '])->textInput();?>
                                            <?=$form->field($model, 'shui_code', ['template' => '{error}']) ?>
                                        </td>
                                        <td class="register_tab_td_1" width="166"><span class="register_span_red">*</span>联系人办公电话</td>
                                        <td class="register_tab_td_2" width="382">
                                                <?= $form->field($model, 'tel',
                                                                        [
                                                                        'inputOptions'=>['class'=>'register_tab_td_input','placeholder'=>'','tabindex'=>'1'],
                                                                        'template' => '<div class="none-line">{input}
                                                                        <span class="tip"></span></div>
                                                                        '])->textInput();?>
                                            <?=$form->field($model, 'tel', ['template' => '{error}']) ?>
                                        </td>
                                    </tr>
                                    <tr class="register_tab_tr">
                                        <td class="register_tab_td_1" width="137"><span class="register_span_red">*</span>法定代表人姓名</td>
                                        <td width="262" class="register_tab_td_2">
                                            <?= $form->field($model, 'law_master',
                                                                        [
                                                                        'inputOptions'=>['class'=>'register_tab_td_input','placeholder'=>'','tabindex'=>'1'],
                                                                        'template' => '<div class="none-line">{input}
                                                                        <span class="tip"></span></div>
                                                                        '])->textInput();?>
                                             <?=$form->field($model, 'law_master', ['template' => '{error}']) ?>
                                        </td>
                                        <td class="register_tab_td_1" width="166"><span class="register_span_red"></span>联系人常用邮箱</td>
                                        <td class="register_tab_td_2" width="382">
                                                <?= $form->field($model, 'email',
                                                                        [
                                                                        'inputOptions'=>['class'=>'register_tab_td_input','placeholder'=>'','tabindex'=>'1'],
                                                                        'template' => '<div class="none-line">{input}
                                                                        <span class="tip"></span></div>
                                                                        '])->textInput();?>
                                            <?=$form->field($model, 'email', ['template' => '{error}']) ?>
                                        </td>
                                    </tr>
                                    <tr class="register_tab_tr">
                                        <td class="register_tab_td_1" width="137"><span class="register_span_red">*</span>法定代表人身份证</td>
                                        <td width="262" class="register_tab_td_2">
                                            <?= $form->field($model, 'law_master_idcard',
                                                                        [
                                                                        'inputOptions'=>['class'=>'register_tab_td_input','placeholder'=>'','tabindex'=>'1'],
                                                                        'template' => '<div class="none-line">{input}
                                                                        <span class="tip"></span></div>
                                                                        '])->textInput();?>
                                            <?=$form->field($model, 'law_master_idcard', ['template' => '{error}']) ?>
                                        </td>
                                        <td class="register_tab_td_1" width="166"><span class="register_span_red"></span>公司网址</td>
                                        <td class="register_tab_td_2" width="382">
                                                <?= $form->field($model, 'org_url',
                                                                        [
                                                                        'inputOptions'=>['class'=>'register_tab_td_input','placeholder'=>'机构网址以http://或https://开头','tabindex'=>'1','value'=>""],
                                                                        'template' => '<div class="none-line">{input}
                                                                        <span class="tip"></span></div>
                                                                        '])->textInput();?>
                                            <?=$form->field($model, 'org_url', ['template' => '{error}']) ?>
                                        </td>
                                    </tr>
                            </table>
                                        <div class="clear"></div>
                                        <p class="register_form_sub register_form_org_kw"><input type="submit" value="提交审核" /></p>
                                <!--机构注册第二步 end  -->
                            <?php } ?>
                        <?php }else if($step==3){ ?>
                                <?php if($type==1){?>
                                    <p class="register_form_success_p"><span class="register_form_success"></span>您已成功完成南京金融交易中心的注册</p>
                                    <div class="register_form_success_div">
                                         <span class="register_form_success_pp"><a class="register_form_success_a1" href="/product">去投资</a></span>
                                         <span class="register_form_success_ppp"><a class="register_form_success_a2" href="/">回首页</a></span>
                                    </div>
                                <?php }else if($type==2){ ?>
                                    <p class="register_form_org_p"><span class="register_form_success"></span>您的资料已经成功提交到金交中心，后台将会对这些资料进行审核，</p>
                                    <p class="register_form_org_pp">审核流程在3-5个工作日完成，感谢您的支持！</p>
                                    <div class="register_form_success_div">
                                         <span class="register_form_success_pp register_form_org_span"><a class="register_form_success_a1" href="/">回首页</a></span>
                                    </div>
                                <?php } ?>
                        <?php } ?>
			<?php ActiveForm::end(); ?>
		</div>
	</div>
	
	
</div>

<script>
	$(function(){

	})
</script>
