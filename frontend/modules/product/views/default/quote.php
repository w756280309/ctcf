<?php
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
        
</head>
<body>
 <?php $this->beginBody() ?>
    <div style="width:400px; margin: 0 auto;">
        <?php if(empty($next)){ ?>
	<?php $form = ActiveForm::begin(['id'=>'order_form', 'action' =>"/product/default/quote?pid=".$model->id ]); ?>
	<ul class="popup_email_ul" style="padding-bottom: 54px;">
		<li class="popup_email_form_li_1"><span class="popup_email_ul_span">*</span> 输入报价</li>
		<li class="popup_email_form_li_2">
			<?= $form->field($order, 'order_money',
				[
				'inputOptions'=>['class'=>'popup_email_input','placeholder'=>'输入报价','tabindex'=>'1'],
				'template' => '<div class="username-line popup_email-input">{input}
				<span class="tip"></span></div>
				<div class="email-text">{error}</div>
				'])->textInput();?>
		</li>
                
	</ul>
        <div style="text-align:center">
                    <p class="popup_email_enter_in"><input type="submit" value="提交报价"onclick=""/></p>
        </div>	
        <?php ActiveForm::end(); ?>
        <?php }else{ ?>
        <div style=" width: 80%; margin: 0 auto; padding-top: 40px;">请在报价俩个工作日内将保证金按照要求转入指定账户，俩个工作日内未转入保证金，此报价视为无效.</div>
        <div style="text-align:center">
                    <p class="popup_email_enter_in"><input type="submit" value="确定" onclick="lout()"/></p>
        </div>	
        <script type="text/javascript">
    function lout(){
            var index = window.parent.getlay();
            //parent.location.href="/user/login/logout";
            parent.layer.close(index);
        }
    </script>
        <?php } ?>
    </div>
    
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>