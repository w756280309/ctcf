<?php

$this->title = '编辑产品';
use yii\widgets\ActiveForm;
use yii\web\YiiAsset;

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => YiiAsset::class]);
$this->registerCssFile('/vendor/kindeditor/4.1.11/themes/default/default.css');
$this->registerCssFile('/vendor/kindeditor/4.1.11/plugins/code/prettify.css');
$this->registerJsFile('/vendor/kindeditor/4.1.11/kindeditor-all-min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/vendor/kindeditor/4.1.11/lang/zh-CN.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/vendor/kindeditor/4.1.11/plugins/code/prettify.js', ['depends' => 'yii\web\YiiAsset']);
?>

<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
        <!-- BEGIN PAGE HEADER-->
        <div class="row-fluid">
            <div class="span12">
                <h3 class="page-title">
                    线下数据
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/offline/offline/addloan">标的列表</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="#">编辑产品</a>
                        <i class="icon-angle-right"></i>
                    </li>

                </ul>
            </div>
        </div>

        <div class="portlet-body form">
            <?php if (\Yii::$app->session->hasFlash('info')) {?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="background-image: none!important;"><span aria-hidden="true">&times;</span></button>
                    <strong><?= \Yii::$app->session->getFlash('info') ?></strong>
                </div>
            <?php }?>
            <!-- BEGIN FORM-->
            <?php $form = ActiveForm::begin([
                'id' => 'import_form',
                'action' => "/offline/offline/addloan",
                'method' => 'post',
                'options' => ['class' => 'form-horizontal form-bordered form-label-stripped']
            ]); ?>
            <div class="control-group">
                <label class="control-label">序号</label>
                <div class="controls">
                    <?= $form->field($model, 'sn', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4']])->textInput() ?>
                    <?= $form->field($model, 'sn', ['template' => '{error}']); ?>
                    <?= $form->field($model, 'id', ['template' => '{input}'])->hiddenInput() ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">产品名称</label>
                <div class="controls">
                    <?= $form->field($model, 'title', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4']])->textInput() ?>
                    <?= $form->field($model, 'title', ['template' => '{error}']); ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">利率</label>
                <div class="controls">
                    <?= $form->field($model, 'yield_rate', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4 label-info', 'placeholder' => '']])->textInput() ?>
                    <?= $form->field($model, 'yield_rate', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">期限单位</label>
                <div class="controls">
                    <?= $form->field($model, 'unit',['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4', 'placeholder' => '']])->dropDownList(['天'=>'天','个月'=>'个月','年'=>'年'],['prompt'=>'请选择','style'=>'width:120px']) ?>
                    <?= $form->field($model, 'unit', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">期限</label>
                <div class="controls">
                    <?= $form->field($model, 'expires', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4', 'placeholder' => '']])->textInput() ?>
                    <?= $form->field($model, 'expires', ['template' => '{error}']); ?>
                </div>
            </div>


            <div class="form-actions">
                <button type="button" class="btn blue" id="submit_btn"><i class="icon-ok"></i> 提交</button>
                <a href="/offline/offline/loanlist" class="btn">取消</a>
            </div>
            <?php $form->end(); ?>
            <!-- END FORM-->
        </div>
    </div>
    <script>
        $(".form-actions").on( "click", "#submit_btn", function(e) {
            e.preventDefault();
            $(this).attr('disabled', true);
            $('#import_form').submit();
            setTimeout(function(){
                $("#submit_btn").attr('disabled', false);
            }, 2000);
        });
    </script>
<?php $this->endBlock(); ?>