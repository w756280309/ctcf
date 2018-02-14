<?php

use yii\web\YiiAsset;
use yii\widgets\ActiveForm;

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => YiiAsset::class]);

?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                优惠券管理 <small>运营模块</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/coupon/coupon/list">优惠管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);"><?= empty($model->id) ? "添加" : "编辑" ?></a>
                </li>
            </ul>
        </div>

        <?php
            $form = ActiveForm::begin([
                'action' => empty($model->id) ? "/coupon/coupon/add" : "/coupon/coupon/edit?id=$model->id",
                'options' => ['class' => 'form-horizontal form-bordered form-label-stripped']
            ]);
        ?>
        <div class="portlet-body form">
            <div class="control-group">
                <label class="control-label">优惠券类型</label>
                <div class="controls">
                    <?= $form->field($model, 'type', ['template' => '{input}{error}', 'inputOptions' => ['autocomplete' => 'off', 'class' => 'chosen-with-diselect span4']])->dropDownList(['0' => '代金券','1'=>'加息券'] )
                    ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">优惠券名称</label>
                <div class="controls">
                    <?= $form->field($model, 'name', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '优惠券名称']])->textInput() ?>
                    <?= $form->field($model, 'name', ['template' => '{error}']) ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">优惠券sn</label>
                <div class="controls">
                    <?= $form->field($model, 'sn', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '优惠券sn']])->textInput() ?>
                    <?= $form->field($model, 'sn', ['template' => '{error}']) ?>
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
                <label class="control-label">加息券利率</label>
                <div class="controls">
                    <?= $form->field($model, 'bonusRate', ['template' => '<div class="input-prepend input-append">{input}<span class="add-on">%</span> </div>{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '加息券利率']])->textInput(['class' => 'm-wrap span12'])
                    ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">加息天数</label>
                <div class="controls">
                    <?= $form->field($model, 'bonusDays', ['template' => '<div class="input-prepend input-append">{input}<span class="add-on">天</span> </div>{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '加息天数']])->textInput(['class' => 'm-wrap span12'])
                    ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">优惠券有效期</label>
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
                <label class="control-label">项目期限<br>（项目满X天及以上可用）</label>
                <div class="controls">
                    <?=
                        $form->field($model, 'loanExpires', ['template' => '<div class="input-append">{input}<span class="add-on">天</span></div>{error}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '项目期限']])->textInput(['class' => 'm-wrap span12'])
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
                <label class="control-label">只能在APP中使用</label>
                <div class="controls">
                    <?=
                        $form->field($model, 'isAppOnly', ['template' => '<div class="input-append">{input}</div>{error}'])->checkbox()
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