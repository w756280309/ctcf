<?php

use yii\widgets\ActiveForm;

$this->registerCssFile('/css/present.css', ['depends' => 'yii\web\YiiAsset']);
$this->title = '绑定银行卡';
?>
<div class="body bindcard">
	<div class="top-title">
	        <div class="tab"><a>绑定提现卡</a></div>
	    </div>
	<?php if ($step == 1) { ?>
		<?php $form = ActiveForm::begin(['id' => 'bindcard_form', 'action' => "/user/draw/bindcardnew"]); ?>
	    <div class="center">
			<div class="register_c1">
				<ul class="register_c1_ul">
					<li class="register_c1_li <?php if($step==1){ ?>arrow<?php } ?>">基本信息</li>
					<li class="register_c1_li_side"></li>
					<li class="register_c1_li <?php if($step==2){ ?>arrow<?php } ?>">详细信息</li>
				</ul>
			</div>
	        <div class="center-title">选择一个银行</div>
	        <div class="center-content">
	            <div class="center-content-top">
	                <div class="bankcard">
						<?php foreach ($bank_show as $key => $val) { ?>
							<div class="form-group field-user-agree required has-success">
								<label><input type="radio" class="bankclick" name="bank" data-number='<?php echo $val['number'] ?>' data-name='<?php echo $val['bankname'] ?>' value="1"><img src="/images/bankimg/<?php echo $val['number'] ?>.jpg" width="138" height="33" alt="<?php echo $val['bankname'] ?>" /></label>

							</div>
						<?php } ?>  
	                </div>
	                <div class="hidden">	
						<?=
						$form->field($model, 'bank_id', [
							'template' => '{input}'])->hiddenInput();
						?>
						<?=
						$form->field($model, 'bank_name', [
							'template' => '{input}'])->hiddenInput();
						?>
	                </div>
	                <div class="clear"></div>
	            </div>
	            <div class="center-content-middle"><a onclick="onlyclick()" style="cursor:pointer;"></a></div>
	            <div class="center-content-bottom">
	                <ul>
	                    <li class="li-l">银行卡号</li>
	                    <li class="li-r"><?=
							$form->field($model, 'card_number', [
								'inputOptions' => ['class' => 'li-input', 'placeholder' => '', 'tabindex' => ''],
								'template' => '<div class="">{input}</div>
				<div class="">{error}</div>
				'])->textInput();
							?>
	                    </li>
	                    <li class="clear"></li>
	                    <li class="li-l">账户名称</li>
	                    <li class="li-r"><?=
							$form->field($model, 'account', [
								'inputOptions' => ['class' => 'li-input', 'placeholder' => '', 'tabindex' => ''],
								'template' => '<div class="">{input}</div>
				<div class="">{error}</div>
				'])->textInput();
							?>

	                    </li>
	                    <li class="clear"></li>
	                    <li class="li-l">持卡人类型</li>
	                    <li class="li-r"><?=
							$form->field($model, 'account_type', [
								'inputOptions' => ['class' => 'li-input', 'placeholder' => '', 'tabindex' => ''],
								'template' => '<div class="">{input}</div>
				<div class="">{error}</div>
				'])->dropDownList(['11' => "个人账户", '12' => '企业账户']);
							?>
							<?=
							$form->field($model, 'bank_name', [
								'template' => '{error}'])
							?>
	                    </li>
	                    <li class="clear"></li>                                        
	                    <li class="li-l">身份证号</li>
	                    <li class="li-r"><?= $idcard; ?>
	                    </li>
	                    <li class="clear"></li>
	                    <li class="li-l">认证手机号</li>
	                    <li class="li-r"><?= $mobile; ?>
	                    </li>
	                    <li class="clear"></li>
	                    <li class="li-l">短信码</li>
	                    <li class="li-r">
							<?=
							$form->field($model, 'sms', [
								'inputOptions' => ['class' => 'li-input', 'placeholder' => '', 'tabindex' => '', 'style' => "width:130px"],
								'template' => '<div class="">{input}&emsp;<input type="button" class="li-confirm" value="获取验证码"onclick="checkBindCard(this);"/></div>
				<div class="">{error}</div>
				'])->textInput();
							?>

	                    </li>
	                    <li class="clear"></li>
	                    <li><span class="li-span"><input type="submit" class="li-confirm" value="确 定"/></span></li>
	                    <li><a class="li-cancel" href="/user/draw/withdrawcash?current=3" target="_self">取消</a></li>

	                    <li class="clear"></li>
	                    <li>
	                    <li class="li-l">&emsp;</li>
	                    <li>  <?=
							$form->field($model, 'bank_id', [
								'template' => '{error}'])
							?></li>
	                    </li>
	                    <li class="clear"></li>
	                </ul>

	                <div class="clear"></div>
	            </div>
	            <div class="clear"></div>
	        </div>
	    </div>
		<?php ActiveForm::end(); ?>
	<?php } else if ($step == 2) { ?>
		<?php $form = ActiveForm::begin(['id' => 'bindcard_form', 'action' => "/user/draw/bindcardnew?id=" . $model->id]); ?>

	    <div class="center">
<div class="register_c1">
				<ul class="register_c1_ul">
					<li class="register_c1_li <?php if($step==1){ ?>arrow<?php } ?>">基本信息</li>
					<li class="register_c1_li_side"></li>
					<li class="register_c1_li <?php if($step==2){ ?>arrow<?php } ?>">详细信息</li>
				</ul>
			</div>
	        <div class="center-content">

	            <div class="center-content-bottom">
	                <ul>
	                    <li class="li-l">分支行名称</li>
	                    <li class="li-r"><?=
							$form->field($model, 'sub_bank_name', [
								'inputOptions' => ['class' => 'li-input', 'placeholder' => '', 'tabindex' => ''],
								'template' => '<div class="">{input}</div>
				<div class="">{error}</div>
				'])->textInput();
							?>
	                    </li>
	                    <li class="clear"></li>
	                    <li class="li-l">分支行所在省份</li>
	                    <li class="li-r"><?=
							$form->field($model, 'province', [
								'inputOptions' => ['class' => 'li-input', 'placeholder' => '', 'tabindex' => ''],
								'template' => '<div class="">{input}</div>
				<div class="">{error}</div>
				'])->dropDownList(['' => '请选择省份']);
							?>

	                    </li>
	                    <li class="clear"></li>
	                    <li class="li-l">分支行所在市</li>
	                    <li class="li-r"><?=
							$form->field($model, 'city', [
								'inputOptions' => ['class' => 'li-input', 'placeholder' => '', 'tabindex' => ''],
								'template' => '<div class="">{input}</div>
				<div class="">{error}</div>
				'])->dropDownList(['' => '请选择城市']);
							?>
	                    </li>
                            <li class="clear"></li>
                            <li class="li-r" style="color:red">
                                注意：请核对后填写，填写错误会影响到您的提现时间，如有疑问请拨打<?=$model->bank_name?>客服热线<?=  Yii::$app->params['bank_tel'][$model->bank_id]['tel']?>咨询
                            </li>

	                    <li class="clear"></li>
	                    <li><span class="li-span"><input type="submit" class="li-confirm" value="确 定"/></span></li>
	                    <li><a class="li-cancel" href="/user/draw/withdrawcash?current=3" target="_self">取消</a></li>

	                </ul>

	                <div class="clear"></div>
	            </div>
	            <div class="clear"></div>
	        </div>
	    </div>
		<?php ActiveForm::end(); ?>
	<?php } ?>
</div>
<script>
	var wait = 60;
	function time(o) {
		if (wait == 0) {
			o.removeAttribute("disabled");
			o.value = "获取验证码";
			o.style.background = 'orange';
			o.style.color = 'white';
			wait = 60;
		} else {

			o.setAttribute("disabled", true);
			o.value = wait + "秒后可重发";
			o.style.background = 'gray';
			o.style.color = 'white';
			wait--;
			setTimeout(function () {
				time(o)
			},
					1000)
		}
	}

	function checkBindCard(obj) {
		if ($('#userbanks-bank_id').val() == '') {
			alert('请选择一个银行');
			return false;
		}
		if ($('#userbanks-card_number').val() == '') {
			alert('银行卡号不能为空');
			return false;
		}
		if ($('#userbanks-card_number').val().length < 16 && $('#userbanks-card_number').val().length > 19) {
			alert('银行卡号错误');
			return false;
		}
		if ($('#userbanks-account').val() == '') {
			alert('账户名称不能为空');
			return false;
		}
		time(obj);
		$.post("/user/draw/bindcarddo", $('#bindcard_form').serialize(), function (result) {
			if (result['res'] == 0) {
				alert(result['msg'])
			}
		});
	}

	function  onlyclick() {
		var lxl = $('.center-content-top').css('height');
		if (lxl == '150px') {
			$('.center-content-top').css("height", "450px");
			$('.center-content-middle').find('a').css("background-position", "0 -116px");
		} else {
			$('.center-content-top').css("height", "150px");
			$('.center-content-middle').find('a').css("background-position", "0 -102px");
		}

	}

	function pcdata(level) {
		$.ajax({
			url: "/prov.xml",
			datatype: "xml",
			type: "GET",
			success: function (xmldoc) {
				$("#userbanks-city").html("<option value=''>请选择城市</option>");
				if (level == '') {
					var valueList = $(xmldoc).find("province");
					$(valueList).each(function () {
						$("#userbanks-province").append("<option value='" + $(this).attr("name") + "'>" + $(this).attr("name") + "</option>");
					})
				} else {
					var valueList = $(xmldoc).find("province");
					$(valueList).each(function () {
						if ($(this).attr("name") == level) {
							citys = $(this).children('city');
							$(citys).each(function () {
								$("#userbanks-city").append("<option value='" + $(this).attr("name") + "'>" + $(this).attr("name") + "</option>");
							})
						}
					})
				}

			}
		})
	}

	$(function () {
		pcdata('');

		$("#userbanks-province").bind('change', function () {
			pcdata($(this).val());
		})
		$('.bankclick').bind('click', function () {
			$number = $(this).attr('data-number');
			$name = $(this).attr('data-name');
			$('#userbanks-bank_id').val($number);
			$('#userbanks-bank_name').val($name);
		});
		$('.form-group').find('img').click(function () {
			$number = $(this).siblings('.bankclick').attr('data-number');
			$name = $(this).siblings('.bankclick').attr('data-name');
			$('#userbanks-bank_id').val($number);
			$('#userbanks-bank_name').val($name);
			$(this).css("border", "1px solid #FF0000");
			$(this).parents('.form-group').siblings().find('img').css("border", "1px solid #dcdcdc");
			$(this).siblings('.bankclick').prop("checked", true).parents('.form-group').siblings().find('.bankclick').prop('checked', false);
		});
		$('.form-group').find('.bankclick').click(function () {
			$(this).attr("checked", true);
			$(this).parents('.form-group').siblings().find('.bankclick').attr('checked', false);
			$(this).siblings('img').css("border", "1px solid #FF0000").parents('.form-group').siblings().find('img').css("border", "1px solid #dcdcdc");
		});
	})
</script>