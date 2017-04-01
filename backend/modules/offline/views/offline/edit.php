<?php

$this->title = '编辑线下数据客户信息';
use yii\widgets\ActiveForm;

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
                        <a href="/offline/offline/list">线下数据</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/offline/offline/list">线下数据</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="#">编辑线下数据客户信息</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="portlet-body form">

            <!-- BEGIN FORM-->
            <?php $form = ActiveForm::begin([
                'id' => 'import_form',
                'action' => "/offline/offline/update",
                'method' => 'post',
                'options' => ['class' => 'form-horizontal form-bordered form-label-stripped']
            ]); ?>
            <?= $form->field($model, 'id', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4', 'placeholder' => '']])->hiddenInput() ?>
            <div class="control-group">
                <label class="control-label">客户姓名</label>
                <div class="controls">
                    <?= $form->field($model, 'realName', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4', 'placeholder' => '']])->textInput() ?>
                    <?= $form->field($model, 'realName', ['template' => '{error}']); ?>
                </div>
            </div>
             <div class="control-group">
                <label class="control-label">联系电话</label>
                <div class="controls">
                    <?= $form->field($model, 'mobile', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4', 'placeholder' => '']])->textInput() ?>
                    <?= $form->field($model, 'mobile', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">开户行名称</label>
                <div class="controls">
                    <?= $form->field($model, 'accBankName', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4 label-info', 'placeholder' => '']])->textInput() ?>
                    <?= $form->field($model, 'accBankName', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">银行卡账号</label>
                <div class="controls">
                    <?= $form->field($model, 'bankCardNo', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4', 'placeholder' => '']])->textInput() ?>
                    <?= $form->field($model, 'bankCardNo', ['template' => '{error}']); ?>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn blue" id="submit_btn"><i class="icon-ok"></i> 提交</button>
                <a href="/offline/offline/list" class="btn">取消</a>
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