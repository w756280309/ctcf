<?php

$this->title = '标的'.($model->id ? '编辑' : '添加');

use PayGate\Cfca\CfcaUtils;
use yii\helpers\ArrayHelper;
use yii\web\YiiAsset;
use yii\widgets\ActiveForm;
use common\models\product\OnlineProduct;

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => YiiAsset::class]);
$this->registerCssFile('/vendor/kindeditor/4.1.11/themes/default/default.css');
$this->registerCssFile('/vendor/kindeditor/4.1.11/plugins/code/prettify.css');
$this->registerJsFile('/vendor/kindeditor/4.1.11/kindeditor-all-min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/vendor/kindeditor/4.1.11/lang/zh-CN.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/vendor/kindeditor/4.1.11/plugins/code/prettify.js', ['depends' => 'yii\web\YiiAsset']);

$readonly = $model->online_status ? ['readonly' => 'readonly'] : [];
$disabled = $model->online_status ? ['disabled' => 'disabled'] : [];
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                贷款管理 <small>贷款管理模块【主要包含项目的管理以及项目分类管理】</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/product/productonline/list">贷款管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/product/productonline/list">项目列表</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0)">编辑项目</a>
                </li>
            </ul>
        </div>
    </div>

    <!--项目编辑区域 start-->
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <?php $form = ActiveForm::begin(['id' => 'product_product_form', 'action' => '/product/productonline/senior-edit?id='.$model->id]); ?>
        <h3 class="form-section">项目基本信息</h3>
        <?php if ($model->id) {  ?>
        <div class="row-fluid">
            <div class="span12 ">
                <div class="control-group">
                    <label class="control-label">项目编号</label>
                    <div class="controls">
                        <?= $model->sn ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="row-fluid">
            <div class="span12 ">
                <div class="control-group">
                    <label class="control-label">项目名称</label>
                    <div class="controls">
                        <?=
                            $form->field($model, 'title', ['template' => '{input}{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '项目名称', 'maxLength' => 32, 'class' => 'm-wrap span12']])->textInput()
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12 ">
                <div class="control-group">
                    <label class="control-label">项目副标题</label>
                    <div class="controls">
                        <?=
                        $form->field($model, 'internalTitle', ['template' => '{input}{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '项目副标题（60字以内）', 'class' => 'm-wrap span12']])->textInput()
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid sourceRfmet">
            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">项目宽限期</label>
                    <div class="controls">
                    <?=
                        $form->field($model, 'kuanxianqi', ['template' => '{input}'])->hiddenInput(['value' => 0, 'id' => 'kuanxianqi_hide'])->label(false)
                    ?>
                    <?=
                        $form->field($model, 'kuanxianqi', ['template' => '<div class="input-append">{input}<span class="add-on">(天)</span></div>{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '默认0天']])->textInput(['class' => 'm-wrap span6'])
                    ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span6 ">
                <div class="control-group">
                    <label class="control-label">发行方</label>
                    <div class="controls">
                        <?php
                        $change_input_option = array_merge([
                            'autocomplete' => 'off',
                            'class' => 'chosen-with-diselect span6',
                            'onchange' => 'changeIssueName(this)',
                        ], $disabled);
                        ?>
                        <?=
                            $form->field($model, 'issuer', [
                                'template' => '{input}{error}',
                                'inputOptions' => $change_input_option])->dropDownList(ArrayHelper::merge(['' => '--请选择--'], ArrayHelper::map($issuer, 'id', 'name')))
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <div class="control-group">
                    <label class="control-label">发行方项目编号</label>
                    <div class="controls">
                        <?=
                        $form->field($model, 'issuerSn', ['template' => '{input}{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '发行方项目编号']])->textInput(['class' => 'm-wrap span12'])
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <div class="control-group">
                    <label class="control-label">资产包编号</label>
                    <div class="controls">
                        <?=  $form->field($model, 'pkg_sn')->textInput(['autocomplete' => 'off', 'value' => $model->pkg_sn, 'class' => 'm-wrap span12'])->label(false) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-actions">
            <?= $form->field($model, 'status', ['template' => '{error}'])->textInput(); ?>
            <button id="product-submit" type="submit" class="btn blue"><i class="icon-ok"></i> 提交</button>
            <a href="/product/productonline/list" class="btn">取消</a>
        </div>
        <?php $form->end(); ?>
        <!-- END FORM-->
    </div>
    <!--end-->
</div>

<script type="text/javascript">
    $(function() {
        var $productForm = $('#product_product_form');
        var $submit = $productForm.find('button[type=submit]');
        var originalBorrower = $("#onlineproduct-issuer option:selected").text();
        originalBorrower = originalBorrower.replace(/(^\s*)|(\s*$)/g,"");
        if(parseInt($("#onlineproduct-refund_method").val())===1){
            $('#onlineproduct-kuanxianqi').removeAttr('readonly');
        }
        KindEditor.ready(function(K) {
            K.create('#company', {
                cssPath: '/vendor/kindeditor/4.1.11/plugins/code/prettify.css',
                fileManagerJson: '',
                uploadJson: '/kindeditor/editor.php?baseurl='+'<?= urlencode(UPLOAD_BASE_URI) ?>',
                allowFileManager: true,
                filterMode: false,
                afterUpload: function(url, data) {
                    $('#file_id').val($('#file_id').val() + data.id + ',');
                },
                afterCreate : function() {
                    this.sync();
                },
                afterBlur:function(){
                    this.sync();
                }
            });

            kindEdit();
        });

        $productForm.submit(function() {
            $submit.attr('disabled', true);
        }).on('afterValidate', function() {
            if (!$(this).data('yiiActiveForm').validated) {
                $submit.attr('disabled', false);
            }
        });

        if(originalBorrower == '深圳立合旺通商业保理有限公司') {
            $(".originalBorrower").show()
        } else {
            $(".originalBorrower").hide()
        }
    });



</script>
<?php $this->endBlock(); ?>
