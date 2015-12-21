<?php

use yii\widgets\ActiveForm;

$this->registerCssFile('/css/present.css', ['depends' => 'yii\web\YiiAsset']);
$this->title = '绑定银行卡';
?>
<div class="body bindcard">
	<?php $form = ActiveForm::begin(['id' => 'bindcard_form', 'action' => "/user/draw/bindcard"]); ?>
	<div class="top-title">
		<div class="tab"><a>绑定提现卡</a></div>
	</div>
	<div class="center">
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
					<li class="li-r">

						<?=
						$form->field($model, 'province', [
							'inputOptions' => ['class' => 'li-input', 'placeholder' => '', 'tabindex' => ''],
							'template' => '<div class="">{input}</div>
				<div class="">{error}</div>
				'])->dropDownList(['' => '请选择省份']);
						?>
					</li>
					<li class="clear"></li>
					<li class="li-l">分支行所在城市</li>
					<li class="li-r">
						<?=
						$form->field($model, 'city', [
							'inputOptions' => ['class' => 'li-input', 'placeholder' => '', 'tabindex' => ''],
							'template' => '<div class="">{input}</div>
				<div class="">{error}</div>
				'])->dropDownList(['' => '请选择城市']);
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
</div>
<script>
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
				$("#userbank-city").html("<option value=''>请选择城市</option>");
				if (level == '') {
					var valueList = $(xmldoc).find("province");
					$(valueList).each(function () {
						console.log($(this).attr("name"));
						$("#userbank-province").append("<option value='" + $(this).attr("name") + "'>" + $(this).attr("name") + "</option>");
					})
				} else {
					var valueList = $(xmldoc).find("province");
					$(valueList).each(function () {
						if ($(this).attr("name") == level) {
							citys = $(this).children('city');
							$(citys).each(function () {
								$("#userbank-city").append("<option value='" + $(this).attr("name") + "'>" + $(this).attr("name") + "</option>");
							})
						}
					})
				}

			}
		})
	}

	$(function () {
		pcdata('');

		$("#userbank-province").bind('change', function () {
			pcdata($(this).val());
		})
		$('.bankclick').bind('click', function () {
			$number = $(this).attr('data-number');
			$name = $(this).attr('data-name');
			$('#userbank-bank_id').val($number);
			$('#userbank-bank_name').val($name);
		});
		$('.form-group').find('img').click(function () {
			$number = $(this).siblings('.bankclick').attr('data-number');
			$name = $(this).siblings('.bankclick').attr('data-name');
			$('#userbank-bank_id').val($number);
			$('#userbank-bank_name').val($name);
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