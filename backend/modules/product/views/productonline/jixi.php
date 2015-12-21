<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
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
        <script type="text/javascript" src="/js/My97DatePicker/WdatePicker.js"></script>

    </head>
    <body class="page-header-fixed page-full-width" style="background-color:white !important">
        <div class="page-container row-fluid" style="margin-top:0px">		
            <div class="page-content">
                <div class="form-horizontal form-view">                    
                        <div class="row-fluid">
                            <div class="span6 ">
                                <?php $form = ActiveForm::begin(['id' => 'auto_form', 'action' => "/product/productonline/jixi?product_id=" . $model['id'], 'options' => ['enctype' => 'multipart/form-data']]); ?>
                                <div class="control-group">
                                    <label class="control-label">计息开始时间</label>
                                    <div class="controls">
                                        <span class="text">
                                            <?=                                                       
                                                 $form->field($model, 'jixi_time', ['template' => '<div class="input-append date form_datetime">{input}<span class="add-on"><i class="icon-calendar"></i></span></div>{error}', 'inputOptions'=>['autocomplete'=>"off",'placeholder'=>'计息开始日']])->textInput(['readonly' => 'readonly','class' => "m-wrap span12", 'onclick' => 'WdatePicker({dateFmt:"yyyy-MM-dd",minDate:\''.date("Y-m-d",$model->start_date).'\',maxDate:\''.date("Y-m-d",$model->finish_date).'\'});'])
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn blue">确定</button>
                            <button type="button" class="btn" onclick="closewin();">取消</button>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </body>
</html>

<script type="text/javascript">
    $(function(){
        var c_flag = '<?= $c_flag ?>';
        if(c_flag == 'close') {
            window.parent.location.href = '/product/productonline/list';
        }
    })
</script>

