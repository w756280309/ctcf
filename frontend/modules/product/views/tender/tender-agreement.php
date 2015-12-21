<?php

use yii\widgets\ActiveForm;

$this->registerCssFile('/css/tender.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/js/layer/layer.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->title = '';
$this->registerJs('var laySum=0;', 1);
?>

<div class="body">
	<ul class="bnav">
		<li class="arrow">
			<a href="/">首页</a>
		</li>
		<li class="arrow">
			<a href="/product">产品公告</a>
		</li>
		<li>
			<?= $model->title ?>
		</li>
	</ul>
	<div class="product-detail tender-agreement">
		<div class="product-detail-enen">
			<h2 class="product-detail-h2">
				<?= $model->title ?>
			</h2>
			<div class="product-detail-sn">
				<?= $model->sn ?>号
			</div>
		</div>
		<div class="tender-agreement-center">
			<div class="tender-agreement-center-div">
				<p>投资金额<span><?= number_format($money, 2) ?> 元</span></p>
			</div>
			<div class="tender-agreement-center-div">
				<p>年化收益率<span><?= bcmul($model->yield_rate, 100, 2) ?>%</span></p>
			</div>
			<div>
				<p>项目期限<span><?= $model->expires_show ?></span></p>
			</div>
		</div>
		<div class="tender-agreement-bottom">
			<?php $form = ActiveForm::begin(['id' => 'online_order_form', 'action' => "/product/tender/to-order?id=" . $ecript_id]); ?>
			<ul>

				<?php foreach ($ctemplate as $val) { ?>
					<?php
					$path = "";
					$layer = 0;
					if (empty($val['content'])) {
						$path = $val->path;
					} else {
						if ($val['type'] != 3) {
							$path = "/product/tender/hetongview?p=" . $ecript_id . "&t=2&h=" . $val['id'];
						} else {
							$path = "javascript:void(0);";
							$layer = 1;
						}
					}
					?>
					<li>
						<div class="tender-agreement-bottom-div tender-agreement-bottom-right">
							<a href="<?= $path ?>" target="_blank" <?php if ($layer) {
					echo "onclick='showHetong(" . $val['id'] . ")'";
				} ?>>《<?= $val->name ?>》</a>
						</div>
					</li>
					<li class="clear" style="height: 17px;"></li>
					<?php } ?>
				<li>
					<?=
					$form->field($omodel, 'agree', [
						'inputOptions' => ['style' => '', 'value' => 0],
						'template' => '<div class="tender-agreement-bottom-div tender-agreement-bottom-right">{input}{error}</div>'])->checkbox();
					?>
				</li>
			</ul>
			<div class="clear"></div>
			<span class="tendersuccess_form_success_pp" style="width: auto; text-align: center; margin-top: 23px" align="center"><?= $form->field($omodel, 'drawpwd', ['inputOptions' => ['class' => 'text_value tender-recharge-5-input', 'style' => 'width: 200px'], 'template' => '交易密码：{input}{error}'])->passwordInput(); ?></span>

			<span class="tendersuccess_form_success_pp">
					<input class="tendersuccess_form_success_a1" type="submit" value="同意并签署合同确认投资" />
			</span>

<?= $form->field($omodel, 'order_money', ['template' => '{input}'])->hiddenInput() ?> 
			<input type="hidden" value="<?= $omodel->order_money ?>" name="order_money"/>
<?= $form->field($omodel, 'order_money', ['template' => '{error}']) ?> 
<?php ActiveForm::end(); ?>
		</div>
	</div>
</div>
<script type="text/javascript">
        function check(){
//            $('#online_order_form').submit();
//            alert($('#online_order_form has-error').length);
//            if($('#online_order_form has-error').length){
//            }else{
//                //$(obj).attr('disabled','disabled')
//            }
//            $(":submit",$('#online_order_form')).attr("disabled","disabled");
//            alert(1);
//            return false;
        }
        
	function showHetong(id) {
		$.get("/product/tender/preview", {id: id}, function (data)
		{
                    var html=data;
			$.layer({
				type: 1,
				title: [
					'认购合同',
					'background:#fff; height:36px; color:#515151; border-bottom:1px solid #f5ebdf; font-weight:bold;' //自定义标题样式
				],
				border: [0],
				area: ['700px', '500px'],
				close: function (index) {
					layer.close(index);
				},
				page: {
					html: "<div style='height:463px;width: 699px;overflow-y:scroll;overflow-x: hidden;'>" + html + "</div>"
				}
			})
		});

                $('.xuboxPageHtml span').css('color','red');
                alert(1);
	}
</script>
