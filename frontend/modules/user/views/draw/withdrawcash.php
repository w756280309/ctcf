<?php

use yii\widgets\ActiveForm;
use common\models\user\UserBank;
$this->registerCssFile('/css/tender.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerCssFile('/css/present.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/js/layer/layer.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/js/tradepaw.js', ['depends' => 'yii\web\YiiAsset']);
?>
<script type="text/javascript">
	$(function () {
//    $('.tender-withdrawcash-div-add').bind('click',function(){
//        laySum = $.layer({
//            type: 2,
//            title: [
//                '绑卡操作', 
//                'background:#fff; height:36px; color:#515151; border-bottom:1px solid #f5ebdf; font-weight:bold;' //自定义标题样式
//            ], 
//            border:[0],
//            area: ['300px', '400px'],
//            close: function(index){
//                            layer.close(index);
//                            location.reload();
//            },
//            iframe: {src: ''}
//        })    
//    })

	})
</script>
<div class="fr page-right tender-recharge">
	<div class="page-rigth-title">
            <div class="tab"><a>提现</a><a class="tab-right" style="display: none">提现提示</a></div>
	</div>
	<div class="page-right-detail">
		<?php $form = ActiveForm::begin(['id' => 'tender-recharge', 'action' => "/user/draw/withdrawcash"]); ?>
		<div class="tender-withdrawcash">
			<ul>
				<li>
					<?php foreach ($banks as $bank) { ?>
						<div class="tender-withdrawcash-div">
							<a href="javascript:void(0);" onclick="card('<?php echo $bank['id'] ?>');" class="xin">
								<p><?=UserBank::safetyCardNumber($bank->card_number) ?></p>
								<div><img src="/images/bankimg/<?php echo $bank['bank_id'] ?>.jpg" width="138" height="33" alt="<?php echo $bank['bank_id'] ?>" /></div>
							</a>
						</div>
					<?php } ?>
					<div class="tender-withdrawcash-div tender-withdrawcash-div-add" onclick="location.href = '/user/draw/bindcardnew'">
						<a href="javascript:void(0);">
							<p class="tender-withdrawcash-div-p1">+</p>
							<p class="tender-withdrawcash-div-p2">添加银行卡</p>
						</a>
					</div>
				</li>
			</ul>
		</div>
		<div class="clear"></div>
		<div class="fr page-right addtip">

			<ul class="addtip-ul">
				<li class="addtip-ul-li-1">提现前需要先绑定一张银行卡；</li>
				<li class="addtip-ul-li-2">绑定的银行卡姓名需要和用户实名认证的姓名一致</li>
			</ul>

		</div>
		<div class="clear"></div>
		<p class="tender-recharge-3">输入提现金额</p>
		<div class="tender-recharge-4"><ul><li class="tender-recharge-c">账户余额</li><li class="tender-recharge-s"><?= $available_balance ?></li><li class="tender-recharge-c">元</li></ul>
		</div>
		<div class="tender-recharge-5">
			<table>
				<tr>
					<td width="68">提现金额</td>
					<td width="200"><?= $form->field($model, 'money', ['inputOptions' => ['class' => 'text_value tender-recharge-5-input', 'style' => 'width: 200px'], 'template' => '{input}']); ?></td>
					<td width="32">元</td>
					<td width="260"><?= $form->field($model, 'money', ['template' => '{error}']); ?><?= $form->field($model, 'bank_id', ['template' => '{error}']); ?></td>
					<td></td>
				</tr>
				<tr class="kongheight"></tr>
				<tr>
					<td width="68">交易密码</td>
                                        <td width="200"><?= $form->field($model, 'drawpwd', ['inputOptions' => ['class' => 'text_value tender-recharge-5-input', 'style' => 'width: 200px','onfocus'=>'this.type=\'password\'','AUTOCOMPLETE'=>"off"], 'template' => '{input}'])->textInput(); ?></td>
					<td width="32"></td>
					<td width="260"><?= $form->field($model, 'drawpwd', ['template' => '{error}']); ?></td>
					<td></td>
				</tr>
			</table>
		</div>
		<div class="tender-recharge-6">
			<span class="tendersuccess_form_success_pp">
				<?php if (bcdiv($available_balance, 1, 2) == 0) { ?>
					<input class="tendersuccess_form_success_a1" type="button" value="无法提现" disabled='disabled' />
					<?php
				} else {
					$user = Yii::$app->user->getIdentity();
					if ($user->trade_pwd) {
						?>
						<input class="tendersuccess_form_success_a1" type="submit" value="提现" <?php
						if (bcdiv($available_balance, 1, 2) == 0) {
							echo "disabled='disabled'";
						}
						?> />
	<?php } else { ?>
						<input class="tendersuccess_form_success_a1 settradepwd" type="submit" value="提现"  />
	<?php } ?>
<?php } ?>



			</span>
		</div>
<?= $form->field($model, 'bank_id', ['template' => '{input}'])->hiddenInput(); ?>
<?php ActiveForm::end(); ?>
	</div>
</div>
<script type="text/javascript">
	function getlay() {  //利用这个方法向子页面传递layer的index
		return laySum;
	}
	function card(id) {
		$('#drawrecord-bank_id').val(id);
	}
	$('.tender-withdrawcash-div').click(function () {
		$(this).css("border", "1px solid #FF0000");
		$(this).siblings().css("border", "1px solid #dcdcdc");
	});
</script>