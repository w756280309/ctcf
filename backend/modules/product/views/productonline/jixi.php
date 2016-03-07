<?php
use yii\bootstrap\ActiveForm;
?>
<link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
<link href="/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="/css/style-metro.css" rel="stylesheet" type="text/css"/>
<link href="/css/style.css" rel="stylesheet" type="text/css"/>
<link href="/css/style-responsive.css" rel="stylesheet" type="text/css"/>

<script src="/js/jquery.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/layer/layer.min.js"></script>
<script type="text/javascript" src="/js/layer/extend/layer.ext.js"></script>
<script type="text/javascript" src="/js/showres.js"></script>
<script type="text/javascript" src="/js/My97DatePicker/WdatePicker.js"></script>

<style>
    p {
        color: red !important;
    }
</style>

<div class="page-container row-fluid" style="margin-top:0px">
    <div class="page-content">
        <div class="form-horizontal form-view">
                <div class="row-fluid">
                    <div class="span6 ">
                        <?php $form = ActiveForm::begin(['id' => 'auto_form', 'action' => "/product/productonline/jixi?product_id=" . $model['id'], 'options' => ['enctype' => 'multipart/form-data']]); ?>
                        <div class="control-group">
                            <label class="control-label">计息开始时间</label>
                            <div class="controls">
                                <?php if (empty($model->finish_date)) { ?>
                                <span class="text">
                                    <?=
                                         $form->field($model, 'jixi_time', ['template' => '<div class="input-append date form_datetime">{input}<span class="add-on"><i class="icon-calendar"></i></span></div><div style="color: red;">{error}</div>', 'inputOptions'=>['autocomplete'=>"off",'placeholder'=>'计息开始日']])->textInput(['readonly' => 'readonly','class' => "m-wrap span12", 'onclick' => 'WdatePicker({dateFmt:"yyyy-MM-dd",minDate:\''.date('Y-m-d', strtotime('+1 day', $model->start_date)).'\'});'])
                                    ?>
                                </span>
                                <?php } else { ?>
                                <span class="text">
                                    <?=
                                         $form->field($model, 'jixi_time', ['template' => '<div class="input-append date form_datetime">{input}<span class="add-on"><i class="icon-calendar"></i></span></div><div style="color: red;">{error}</div>', 'inputOptions'=>['autocomplete'=>"off",'placeholder'=>'计息开始日']])->textInput(['readonly' => 'readonly','class' => "m-wrap span12", 'onclick' => 'WdatePicker({dateFmt:"yyyy-MM-dd",minDate:\''.date('Y-m-d', strtotime('+1 day', $model->start_date)).'\',maxDate:\''.date("Y-m-d", strtotime('-1 day', $model->finish_date)).'\'});'])
                                    ?>
                                </span>
                                <?php } ?>
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

<script type="text/javascript">
    $(function(){
        var c_flag = '<?= $c_flag ?>';
        if(c_flag === 'close') {
            window.parent.location.href = '/product/productonline/list';
        }
    })
</script>

