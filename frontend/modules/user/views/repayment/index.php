<?php

use yii\helpers\Html;
use frontend\assets\AppAsset;
use yii\widgets\ActiveForm;

AppAsset::register($this);
$this->registerJsFile('js/jquery-1.11.1.min.js', ['depends' => 'yii\web\YiiAsset', 'position' => 1]);

$errorcode = array(
	100 => "缺少get参数",
	101 => "访客无法还款",
	102 => "尚未开通融资账户",
	103 => "此项目还没有放款",
	104 => "此项目包含还没有放款的批次",
	105 => "此项目不存在",
	106 => "此项目状态异常",
	107 => "没有需要还款的项目",
	108 => "还款失败，记录失败",
	109 => "还款失败，状态修改失败",
	110 => "还款失败，资金记录失败",
	111 => "还款失败，投资人账户调整失败",
	112 => "账户余额不足",
	113 => "账户余额扣款异常",
	114 => "标的状态异常",
	115 => "没到还款日",
);
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
			.settradepwd-div p {margin: 11px auto;}
			.paddindleft{padding-left: 136px;}
			.popup_paw_ul li{line-height: 27px;}
        </style>
		<script type="text/javascript">
			function lout() {
				var index = window.parent.getlay();
				//parent.location.href="/user/login/logout";
				parent.layer.close(index);
			}
        </script>
	</head>
	<body>
		<?php $this->beginBody() ?>
		<?php if (!empty($error)) { ?>
			<div class="main tender-login-deng settradepwd-div">
				<?= $errorcode[$error] ?>
				<?php
				if (Yii::$app->request->get('response')) {
					echo Yii::$app->request->get('response');
				}
				?>
				<p class="popup_email_enter_in"><input type="button" value="确定" onclick="lout()" /></p>
			</div>
		<?php } else { ?>    
			<?php $form = ActiveForm::begin(['id' => 'popup_form', 'action' => "/user/repayment/index?pid=" . $pid]); ?>
			<?= $form->field($model, 'online_pid', ['template' => '{input}'])->hiddenInput(); ?>
			<ul class="popup_paw_ul popup_paw_ul_wkl settradepwd-ul paddindleft">
				<li class="popup_paw_form_li_1 popup_paw_form_li_1_en"><span class="popup_paw_ul_span"></span>还款本金</li>
				<li class="popup_paw_form_li_2">
					<?= $total_benjin ?>
				</li>
			</ul>
			<div class="clear"></div>
			<ul class="popup_paw_ul popup_paw_ul_wkl settradepwd-ul paddindleft settradepwd-ul-en">
				<li class="popup_paw_form_li_1 popup_paw_form_li_1_en"><span class="popup_paw_ul_span"></span>还款利息</li>
				<li class="popup_paw_form_li_2">
					<?= $total_lixi ?>
				</li>
			</ul>
			<div class="clear"></div>
			<ul class="popup_paw_ul popup_paw_ul_wkl settradepwd-ul paddindleft settradepwd-ul-en">
				<li class="popup_paw_form_li_1 popup_paw_form_li_1_en"><span class="popup_paw_ul_span"></span>逾期罚息</li>
				<li class="popup_paw_form_li_2">
					<?= $total_faxi ?>
				</li>
			</ul>
			<div class="clear"></div>
			<ul class="popup_paw_ul popup_paw_ul_wkl settradepwd-ul paddindleft settradepwd-ul-en">
				<li class="popup_paw_form_li_1 popup_paw_form_li_1_en"><span class="popup_paw_ul_span"></span>总&emsp;&emsp;计</li>
				<li class="popup_paw_form_li_2">
					<?= $total ?>
				</li>
			</ul>
			<div class="clear"></div>
			<ul class="popup_paw_enter settradepwd-sub">
				<li><input type="submit" value="确 定" onclick="" /><li>
				<li><a onclick="lout()" style="cursor:pointer;">取消</a></li>
			</ul>
			<?php ActiveForm::end(); ?>


			<?php $this->endBody() ?>
		<?php } ?>           
	</body>
</html>
<?php $this->endPage() ?>