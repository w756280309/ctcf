<?php

use yii\widgets\ActiveForm;

$this->registerCssFile('/css/tender.css', ['depends' => 'yii\web\YiiAsset']);
$this->title = '充值';
?>
<div class="fr page-right tender-recharge">
	<div class="page-rigth-title">
            <div class="tab"><a>充值</a><a class="tab-right" style="display:none">充值提示</a></div>
	</div>
	<div class="page-right-detail">
		<?php $form = ActiveForm::begin(['id' => 'tender-recharge', 'action' => "/user/recharge/recharge"]); ?>
		<div class="tender-recharge-1">
                    <p>选择充值的银行</p><a class="tender-recharge-1-a" href="javascript:void(0)" onclick="switchAc(this);" data-index="12">使用企业账户</a>
		</div>
		<div class="clear"></div>
		<div class="tender-recharge-2">
			<?php foreach ($bank_show as $key => $val) { 
                            
                            ?>
                    <div class="form-group field-user-agree required has-success class<?=$val['support'] ?>" style="<?php if($val['support']=='11-12'||$val['support']=='11'){}else{echo 'display:none';} ?>">
					<input type="hidden" name="" value="<?php echo $val['bankname'] ?>">
					<label><input type="radio" id="bankclick" class="bankclick" name="bank" data-number='<?php echo $val['number'] ?>' data-name='<?php echo $val['bankname'] ?>' value="1"><img src="/images/bankimg/<?php echo $val['number'] ?>.jpg" width="138" height="33" alt="<?php echo $val['bankname'] ?>" /></label>
				</div>
			<?php } ?>  
			<div class="hidden">	
				<?=
				$form->field($model, 'bank_id', [
					'template' => '{input}{error}'])->hiddenInput();
				?>
				<input type="hidden" id="account_type" name="account_type" value="11" />
			</div>
		</div>
		<div class="clear"></div>
		<p class="tender-recharge-3">输入充值金额</p>
		<div class="tender-recharge-4"><ul><li class="tender-recharge-c">账户余额</li><li class="tender-recharge-s"><?= $ua->available_balance ?></li><li class="tender-recharge-c">元</li></ul>
		</div>
		<div class="tender-recharge-5">
			<table>
				<tr>
					<td width="68">充值金额</td>
					<td width="200"><?= $form->field($model, 'fund', ['inputOptions' => ['class' => 'text_value tender-recharge-5-input', 'style' => 'width: 200px'], 'template' => '{input}']); ?></td>
					<td width="32">元</td>
					<td width="200"><?= $form->field($model, 'bank_id', ['template' => '{error}']); ?><?= $form->field($model, 'fund', ['template' => '{error}']); ?></td>
					<td></td>
				</tr>
			</table>
		</div>
		<div class="tender-recharge-6">
			<span class="tendersuccess_form_success_pp"><input class="tendersuccess_form_success_a1" type="submit" value="充值" /></span>
		</div>
		<?php ActiveForm::end(); ?>
	</div>
</div>
<script>
        function switchAc(obj){
	$account_type = $(obj).attr('data-index');
	$('#account_type').val($account_type);
            if($(obj).attr('data-index')=='12'){
                $(obj).html('使用个人账户');
                $(obj).attr('data-index',11)
                $('.class11-12').show()
                $('.class12').show()
                $('.class11').hide()
            }else{
                $(obj).html('使用企业账户');
                $(obj).attr('data-index',12)
                $('.class11-12').show()
                $('.class11').show()
                $('.class12').hide()
            }
        }
	$(function () {
		$('.bankclick').bind('click', function () {
			$number = $(this).attr('data-number');
			$('#rechargerecord-bank_id').val($number);
		});
		$('.form-group').find('img').click(function () {
			$number = $(this).siblings('.bankclick').attr('data-number');
			$('#rechargerecord-bank_id').val($number);
			$(this).css("border", "1px solid #FF0000");
			$(this).parents('.form-group').siblings().find('img').css("border", "1px solid #dcdcdc");
			$(this).siblings('.bankclick').prop("checked",true).parents('.form-group').siblings().find('.bankclick').prop('checked',false);
		});
		$('.form-group').find('.bankclick').click(function () {
			$(this).attr("checked",true);
			$(this).parents('.form-group').siblings().find('.bankclick').attr('checked',false);
			$(this).siblings('img').css("border", "1px solid #FF0000").parents('.form-group').siblings().find('img').css("border", "1px solid #dcdcdc");
		});
	});
</script>
