<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$flag = Yii::$app->request->get('flag');
?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="<?= Yii::$app->charset ?>">
        <?= Html::csrfMetaTags() ?>
        <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
        <link href="/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style-metro.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style-responsive.css" rel="stylesheet" type="text/css"/>
        <link href="/css/default.css" rel="stylesheet" type="text/css" id="style_color"/>
        <link href="/css/uniform.default.css" rel="stylesheet" type="text/css"/>
        <script src="/js/jquery.js" type="text/javascript"></script>

        <script type="text/javascript" src="/js/layer/layer.min.js"></script>
        <script type="text/javascript" src="/js/layer/extend/layer.ext.js"></script>
        <script type="text/javascript" src="/js/showres.js"></script>

    </head>
    <body class="page-header-fixed page-full-width" style="background-color:white !important">
        <div class="page-container row-fluid" style="margin-top:0px">		
            <div class="page-content">
                <div class="form-horizontal form-view">
                      <?php $form = ActiveForm::begin(['id'=>'admin_form', 'action' =>"/adminuser/admin/editpass" ]); ?>
                        <div class="control-group">
                            <label class="control-label"><span style="color:red">*</span>原始密码</label>
                            <div class="controls">
                                <span class="text">
                                    <?=
                                    $form->field($model, 'password', ['inputOptions' => ['class' => 'text_value', 'style' => 'width: 200px;'],  'template' => '<div>{input}</div><div>{error}</div>'])->passwordInput();
                                    ?> 
                                </span>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label"><span style="color:red">*</span>新密码</label>
                            <div class="controls">
                                <span class="text">
                                    <?=
                                    $form->field($model, 'new_pass', ['inputOptions' => ['class' => 'text_value', 'style' => 'width: 200px;'],  'template' => '<div>{input}</div><div>{error}</div>'])->passwordInput();
                                    ?> 
                                </span>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label"><span style="color:red">*</span>确认密码</label>
                            <div class="controls">
                                <span class="text">
                                    <?=
                                    $form->field($model, 'r_pass', ['inputOptions' => ['class' => 'text_value', 'style' => 'width: 200px;'],  'template' => '<div>{input}</div><div>{error}</div>'])->passwordInput();
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="form-actions">
                                <button type="submit" class="btn green">保 存</button>
                                <button type="button" class="btn" onclick="closewin()">取 消</button>
                        </div>
                 <?php ActiveForm::end(); ?>
		 </div>
            </div>
        </div>

        <script type="text/javascript">  

            var flag = '<?= $flag ?>';
            var index = window.parent.playSum;
            $(function (){

                if (flag === '1')
                {  
                    window.parent.location.href='/login/logout';
                    //parent.layer.close(index);            
                }

            });

        </script>
    </body>
</html>



