<?php
use backend\assets\AppAsset;
use yii\widgets\ActiveForm;
$menus = array();
$this->registerCssFile('/kindeditor/themes/default/default.css', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/kindeditor/kindeditor-min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/kindeditor/lang/zh_CN.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);
?>

<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">

    <!-- BEGIN PAGE HEADER-->

    <div class="row-fluid">
        <div class="span12">            
                <h3 class="page-title">
                        内容管理 <small>新闻资讯类模块</small>
                </h3>
                <ul class="breadcrumb">
                        <li>
                                <i class="icon-home"></i>
                                <a href="/news/">内容管理</a> 
                                <i class="icon-angle-right"></i>
                        </li>
                        <li>
                                <a href="/news/news/index">内容列表</a>
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
                            <label class="control-label">副标题</label>
                            <div class="controls">
                                    <?= $form->field($model, 'child_title', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'资讯副标题']])->textInput() ?>
                                    <?= $form->field($model, 'child_title', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">
                            <label class="control-label">来源</label>
                            <div class="controls">
                                    <?= $form->field($model, 'source', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'资讯来源于']])->textInput() ?>
                                    <?= $form->field($model, 'source', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">
                            <label class="control-label">所属分类</label>
                            <div class="controls">
                                <?=$form->field($model, 'category_id', ['template' => '{input}', 'inputOptions'=>['class'=>'m-wrap span12']])->dropDownList([0=>'=====顶级分类====='] + $categories); ?>
                                <?= $form->field($model, 'category_id', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">
                            <label class="control-label">所属分类</label>
                            <div class="controls">
                                <?= $form->field($model, 'status', ['template' => '{input}'])->dropDownList($status); ?>
                                <?= $form->field($model, 'status', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">
                            <label class="control-label">所属分类</label>
                            <div class="controls">
                                <?= $form->field($model, 'home_status', ['template' => '{input}'])->dropDownList($homeStatus); ?>
                                <?= $form->field($model, 'home_status', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">
                            <label class="control-label">显示顺序</label>
                            <div class="controls">
                                    <?= $form->field($model, 'sort', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span12','placeholder'=>'资讯排序']])->textInput() ?>
                                    <?= $form->field($model, 'sort', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">
                            <label class="control-label">发布时间</label>
                            <div class="controls">
                                    <?= $form->field($model, 'body', ['template' => '{input}', 'inputOptions'=>['id' => 'news_body', 'class'=>'text_value', 'style' => "width:688px; height:350px;"]])->textInput(); ?>
                                    <?= $form->field($model, 'body', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="control-group">
                            <label class="control-label">内容</label>
                            <div class="controls">
                                    <?= $form->field($model, 'news_time', ['template' => '{input}', 'inputOptions'=>['autocomplete'=>"off",'class'=>'m-wrap span3 Wdate','placeholder'=>'选择发布时间', 'onclick'=>'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm:ss",maxDate:\''.  date("Y-m-d").'\'});']])->textInput() ?>
                                    <?= $form->field($model, 'news_time', ['template' => '{error}']); ?>
                            </div>
                    </div>
                    <div class="form-actions">

                            <button type="submit" class="btn blue"><i class="icon-ok"></i> 提交</button>

                            <button type="button" class="btn">取消</button>

                    </div>

             <?php $form->end(); ?>

            <!-- END FORM-->  

    </div>

        
        
    </div>
                                    
</div>


<script type="text/javascript">
    jQuery(document).ready(function () {

    });
    $(function() {    
        KindEditor.ready(function() {
            editor_company = KindEditor.create(
                    '#news_body', {
                        cssPath: '/kindeditor/plugins/code/prettify.css',
                        fileManagerJson: '',
                        uploadJson: '/kindeditor/editor.php',
                        allowFileManager: true,
                        filterMode: false,
                        afterUpload: function(url, data) {
                            $('#file_id').val($('#file_id').val() + data.id + ',');
                        }
                    });
        });
    });
</script> 
<?php $this->endBlock(); ?>