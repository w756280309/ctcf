<?php
use yii\widgets\ActiveForm;

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                代金券管理 <small>运营模块</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/coupon/coupon/list">代金券管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);"><?= empty($model->id) ? "添加" : "编辑" ?></a>
                </li>
            </ul>
        </div>

        <?php $form = ActiveForm::begin([
            'action' => empty($model->id) ? "/coupon/coupon/add" : "/coupon/coupon/edit?id=$model->id",
            'options' => ['class' => 'form-horizontal form-bordered form-label-stripped']
            ]); ?>
        <div class="portlet-body form">
            <div class="control-group">
                <label class="control-label">代金券名称</label>
                <div class="controls">
                    <?= $form->field($model, 'name', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '代金券名称']])->textInput() ?>
                    <?= $form->field($model, 'name', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">代金券面值</label>
                <div class="controls">
                    <?=
                        $form->field($model, 'amount', ['template' => '<div class="input-prepend input-append"><span class="add-on">￥</span>{input}<span class="add-on">元</span> </div>{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '代金券面值']])->textInput(['class' => 'm-wrap span12'])
                    ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">代金券有效期</label>
                <div class="controls">
                    <?=
                        $form->field($model, 'expiresInDays', ['template' => '<div class="input-append">{input}<span class="add-on">(天)</span></div>{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '有效天数']])->textInput(['class' => 'm-wrap span12'])
                    ?>
                    <span style="color: red;">or</span>
                    <?=
                        $form->field($model, 'useEndDate', [
                                'template' => '<div class="input-append date form_datetime">{input}<span class="add-on"><i class="icon-calendar"></i></span></div>{error}',
                                'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '截止日期']
                            ])->textInput([
                                'readonly' => 'readonly',
                                'class' => 'm-wrap span12',
                                'onclick' => 'WdatePicker({dateFmt:"yyyy-MM-dd", minDate:\''.date('Y-m-d').'\'});'
                            ])
                    ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">起投金额</label>
                <div class="controls">
                    <?=
                        $form->field($model, 'minInvest', ['template' => '<div class="input-prepend input-append"><span class="add-on">￥</span>{input}<span class="add-on">元</span> </div>{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '起投金额']])->textInput(['class' => 'm-wrap span12'])
                    ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">项目类型</label>
                <div class="controls">
                    <?=
                        $form->field($model, 'loanCategories', ['template' => '{input}{error}', 'inputOptions' => ['autocomplete' => 'off', 'class' => 'chosen-with-diselect span4']])->dropDownList(['' => '所有项目'] + Yii::$app->params['pc_cat'])
                    ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">发放时间</label>
                <div class="controls">
                    <?=
                        $form->field($model, 'issueStartDate', [
                                'template' => '<div class="input-append date form_datetime">{input}<span class="add-on"><i class="icon-calendar"></i></span></div>{error}',
                                'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '开始日期']
                            ])->textInput([
                                'readonly' => 'readonly',
                                'class' => 'm-wrap span12',
                                'onclick' => 'WdatePicker({dateFmt:"yyyy-MM-dd", minDate:\''.date('Y-m-d').'\'});'
                            ])
                    ?>
                    ---
                    <?=
                        $form->field($model, 'issueEndDate', [
                                'template' => '<div class="input-append date form_datetime">{input}<span class="add-on"><i class="icon-calendar"></i></span></div>{error}',
                                'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '截止日期']
                            ])->textInput([
                                'readonly' => 'readonly',
                                'class' => 'm-wrap span12',
                                'onclick' => 'WdatePicker({dateFmt:"yyyy-MM-dd", minDate:"#F{$dp.$D(\'coupontype-issuestartdate\')}"});'
                            ])
                    ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">发放人群</label>
                <div class="controls">
                    <?=
                        $form->field($model, 'customerType', ['template' => '{input}{error}', 'inputOptions' => ['autocomplete' => 'off', 'class' => 'chosen-with-diselect span4']])->dropDownList(['' => '所有用户', 1 => '未投资用户'])
                    ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">用户是否可以领取</label>
                <div class="controls">
                    <?=
                        $form->field($model, 'allowCollect', ['template' => '<div class="input-append">{input}</div>{error}'])->checkbox()
                    ?>
                </div>
            </div>

            <div class="form-actions">
                <?php if (!$model->isAudited) { ?>
                    <button type="submit" class="btn blue"><i class="icon-ok"></i> 提交</button>
                    <a href="list" class="btn">取消</a>
                <?php } else { ?>
                    <button type="button" class="btn blue" onclick="location.href='/coupon/coupon/list'"><i class="icon-ok"></i> 返回列表</button>
                <?php } ?>
            </div>
        <?php $form->end(); ?>
    </div>
</div>
<?php $this->endBlock(); ?>