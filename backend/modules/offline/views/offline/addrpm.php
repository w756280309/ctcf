<?php

$this->title = '新增/编辑分期';
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
                        <a href="#">新增/编辑分期</a>
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
                'action' => "/offline/offline/addrpm",
                'method' => 'post',
                'options' => ['class' => 'form-horizontal form-bordered form-label-stripped']
            ]); ?>
            <div class="control-group"></div>
            <div class="control-group">
                <label class="control-label">分期期数</label>
                <div class="controls">
                    <?= $form->field($model, 'term', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4']])->textInput() ?>
                    <?= $form->field($model, 'term', ['template' => '{error}']); ?>
                    <?= $form->field($model, 'loan_id', ['template' => '{input}'])->hiddenInput(['value' => $loan_id ? $loan_id : '']) ?>
                    <?= $form->field($model, 'id', ['template' => '{input}'])->hiddenInput() ?>
                </div>
            </div>


            <div class="control-group">
                <label class="control-label">预期还款时间</label>
                <div class="controls">
                    <?= $form->field($model, 'dueDate', [
                        'template' => '{input}{error}',
                    ])->textarea([
                        'class' => 'm-wrap span4',
                        'value' => $dueDate ? $dueDate : '',
                    ])
                    ?>
                    <span class="red">输入的日期以英文逗号隔开 "," 例如  2017-05-01,2017-06-01,2017-07-01</span>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn blue" id="submit_btn"><i class="icon-ok"></i> 提交</button>
                <>
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