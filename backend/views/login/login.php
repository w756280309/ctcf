<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '后台管理系统登录';

?>
<?php $this->beginBlock('blockhead'); ?>
<link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
<link href="/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="/css/style-metro.css" rel="stylesheet" type="text/css"/>
<link href="/css/style.css" rel="stylesheet" type="text/css"/>
<link href="/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="/css/default.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="/css/login.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
    .has-error{
        color: red;
    }
    .help-block-error{
        color: red;
    }
</style>
<?php $this->endBlock(); ?>

<?php $this->beginBlock('blockmain'); ?>
<body class="login">
	<!-- BEGIN LOGO -->
	<div class="logo">
		<img src="/image/logo-big.png" alt="" /> 
	</div>
	<!-- END LOGO -->
	<!-- BEGIN LOGIN -->
	<div class="content">
		<!-- BEGIN LOGIN FORM -->
<!--		<form class="form-vertical login-form" action="index.html">-->
<?php $form = ActiveForm::begin(['id'=>'admin_form' , 'action' =>"/login",'options' => ['class' => 'form-vertical login-form']]); ?>
			<h3 class="form-title">欢迎使用后台管理系统</h3>
			
			<div class="control-group">
				<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
				<label class="control-label visible-ie8 visible-ie9">登录名</label>
				<div class="controls">
					<div class="input-icon left">
						<i class="icon-user"></i>
						
                                                <?=
                                                $form->field($model, 'username', ['template' => '{input}', 'inputOptions' => ['placeholder' => '登录名','class'=>'m-wrap placeholder-no-fix']]);
                                                ?>
                                                
					</div>
                                                <?=
                                                $form->field($model, 'username', ['template' => '{error}']);
                                                ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label visible-ie8 visible-ie9">密码</label>
				<div class="controls">
					<div class="input-icon left">
						<i class="icon-lock"></i>
                                                <?=
                                                $form->field($model, 'password', [ 'template' => '{input}', 'inputOptions' => ['placeholder' => '密码','class'=>'m-wrap placeholder-no-fix']])->passwordInput();;
                                                ?>
                                                
					</div>
                                    <?=
                                                $form->field($model, 'password', ['template' => '{error}']);
                                                ?>
				</div>
			</div>
			<div class="form-actions">
				
				<button type="submit" class="btn green pull-right">
				Login <i class="m-icon-swapright m-icon-white"></i>
				</button>            
			</div>
			
<!--		</form>-->
<?php ActiveForm::end(); ?>
		<!-- END LOGIN FORM -->        
		
	</div>
	<!-- END LOGIN -->
	
	<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
	<!-- BEGIN CORE PLUGINS -->
	<script src="/js/jquery-1.10.1.min.js" type="text/javascript"></script>
	<script src="/js/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
	<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
	<script src="/js/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>      
	<script src="/js/bootstrap.min.js" type="text/javascript"></script>
	<!--[if lt IE 9]>
	<script src="/js/excanvas.min.js"></script>
	<script src="/js/respond.min.js"></script>  
	<![endif]-->   
	<script src="/js/jquery.slimscroll.min.js" type="text/javascript"></script>
	<script src="/js/jquery.blockui.min.js" type="text/javascript"></script>  
	<script src="/js/jquery.cookie.min.js" type="text/javascript"></script>
	<script src="/js/jquery.uniform.min.js" type="text/javascript" ></script>
	<!-- END CORE PLUGINS -->
	<!-- BEGIN PAGE LEVEL PLUGINS -->
	<script src="/js/jquery.validate.min.js" type="text/javascript"></script>
	<!-- END PAGE LEVEL PLUGINS -->
	<!-- BEGIN PAGE LEVEL SCRIPTS -->
	<script src="/js/app.js" type="text/javascript"></script>
	<script src="/js/login.js" type="text/javascript"></script>      
	<!-- END PAGE LEVEL SCRIPTS --> 
	<script>
		jQuery(document).ready(function() {     
		  App.init();
		  //Login.init();
		});
	</script>
	<!-- END JAVASCRIPTS -->

</body>
</html>

<?php $this->endBlock(); ?>
