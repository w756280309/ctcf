<?php
use backend\assets\AppAsset;
use yii\widgets\ActiveForm;
$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">

    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">            
            <h3 class="page-title">
                资讯管理 <small>新闻资讯类模块</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/news/">资讯管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/news/news/index">资讯列表</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">内容编辑</a>
                </li>
            </ul>
        </div>
        <div class="portlet-body form">
            <!-- BEGIN FORM-->
            <?php $form = ActiveForm::begin(['id' => 'news_form', 'action' => "/news/news/edit?id=".$model->id, 'options' => ['enctype' => 'multipart/form-data','class'=>'form-horizontal form-bordered form-label-stripped']]); ?>
                    <div class="control-group">
                        <label class="control-label">标题</label>
                        <div class="controls">
                            <?= $form->field($model, 'title', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'标题']])->textInput() ?>
                            <?= $form->field($model, 'title', ['template' => '{error}']); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">分类</label>
                        <div class="controls">
                            <?= $form->field($model, 'category', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'标题']])->checkboxList(\yii\helpers\ArrayHelper::map($categories,'id','name')) ?>
                            <?= $form->field($model, 'category', ['template' => '{error}']); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">状态</label>
                        <div class="controls">
                            <?= $form->field($model, 'status', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span3 Wdate','placeholder'=>'状态']])->dropDownList($status) ?>
                            <?= $form->field($model, 'status', ['template' => '{error}']); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">发布时间</label>
                        <div class="controls">
                            <?= $form->field($model, 'news_time', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span3 Wdate','placeholder'=>'选择发布时间', 'onclick'=>'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm:ss",maxDate:\''.  date("Y-m-d").'\'});']])->textInput() ?>
                            <?= $form->field($model, 'news_time', ['template' => '{error}']); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">内容</label>
                        <div class="controls">
                            <?= $form->field($model, 'body', ['template' => '{input}', 'inputOptions'=>[ 'style' => "width:688px; height:350px;"]])->textarea(); ?>
                            <?= $form->field($model, 'body', ['template' => '{error}']); ?>
                        </div>
                    </div>
                <div class="control-group">
                    <label class="control-label">显示顺序</label>
                    <div class="controls">
                        <?= $form->field($model, 'sort', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span3','placeholder'=>'资讯排序']])->textInput() ?>
                        <?= $form->field($model, 'sort', ['template' => '{error}']); ?>
                    </div>
                </div>
                    <div class="form-actions">
                            <button type="submit" class="btn blue"><i class="icon-ok"></i> 提交</button>
                            <a href="/news/news/index" class="btn">取消</a>
                    </div>
             <?php $form->end(); ?>
            <!-- END FORM-->
    </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {

    });
</script> 
<?php $this->endBlock(); ?>