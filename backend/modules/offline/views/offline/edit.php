<?php

$this->title = '编辑线下数据客户信息';
use yii\widgets\ActiveForm;
use yii\web\YiiAsset;

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => YiiAsset::class]);
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
            <?php if (\Yii::$app->session->hasFlash('info')) {?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="background-image: none!important;"><span aria-hidden="true">&times;</span></button>
                    <strong><?= \Yii::$app->session->getFlash('info') ?></strong>
                </div>
            <?php }?>
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
                    <?= $form->field($model, 'realName', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4']])->textInput() ?>
                    <?= $form->field($model, 'realName', ['template' => '{error}']); ?>
                </div>
            </div>
             <div class="control-group">
                <label class="control-label">联系电话</label>
                <div class="controls">
                    <?= $form->field($model, 'mobile', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4', 'placeholder' => '']])->textInput() ?>
                    <?= $form->field($model, 'mobile', ['template' => '{error}']); ?>
                    <div class="checkbox">
                        <label style="color: green">
                            <input type="checkbox" name="checkM" value="1">是否更新到会员账户详情
                        </label>
                    </div>
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
                    <?= $form->field($model, 'bankCardNo', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4', 'placeholder' => '', 'maxlength' => 19,]])->textInput() ?>
                    <?= $form->field($model, 'bankCardNo', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">认购金额（万元）</label>
                <div class="controls">
                    <?= $form->field($model, 'money', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4', 'placeholder' => '']])->textInput() ?>
                    <?= $form->field($model, 'money', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">利率</label>
                <div class="controls">
                    <?= $form->field($model, 'apr', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4', 'placeholder' => '']])->textInput() ?>
                    <?= $form->field($model, 'apr', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">认购日期</label>
                <div class="controls">
                    <?= $form->field($model, 'orderDate', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4', 'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd'})", 'placeholder' => '']])->textInput() ?>
                    <?= $form->field($model, 'orderDate', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">起息日</label>
                <div class="controls">
                    <?= $form->field($model, 'valueDate', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4', 'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd'})", 'placeholder' => '']])->textInput() ?>
                    <?= $form->field($model, 'valueDate', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">渠道</label>
                <div class="controls">
                    <?= $form->field($model, 'affiliator_id', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span4', 'placeholder' => '']])->dropDownList($affiliators,['prompt'=>'请选择']) ?>
                    <?= $form->field($model, 'affiliator_id', ['template' => '{error}']); ?>
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