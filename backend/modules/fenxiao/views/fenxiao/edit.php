<?php
use yii\widgets\ActiveForm;
?>
<?php $this->beginBlock('blockmain'); ?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                渠道管理 <small>运营模块</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/fenxiao/fenxiao/list">渠道管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);"><?= empty($admin) ? "添加" : "编辑" ?></a>
                </li>
            </ul>
        </div>

        <?php
            $form = ActiveForm::begin([
                'action' => empty($admin) ? "/fenxiao/fenxiao/add" : "/fenxiao/fenxiao/edit?id=$admin->id",
                'options' => [
                    'class' => 'form-horizontal form-bordered form-label-stripped',
                    'enctype' => 'multipart/form-data',
                ]
            ]); ?>
        <div class="portlet-body form">

            <div class="control-group">
                <label class="control-label">分销商名称</label>
                <div class="controls">
                    <?= $form->field($model, 'affName', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '分销商名称']])->textInput() ?>
                    <?= $form->field($model, 'affName', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">登录名称</label>
                <div class="controls">
                    <?= $form->field($model, 'loginName', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '登录名称']])->textInput() ?>
                    <?= $form->field($model, 'loginName', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">登录密码</label>
                <div class="controls">
                    <?= $form->field($model, 'password', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '登录密码']])->textInput() ?>
                    <?= $form->field($model, 'password', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">渠道码</label>
                <div class="controls">
                    <?= $form->field($model, 'affCode', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '渠道码']])->textInput() ?>
                    <?= $form->field($model, 'affCode', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">分销商图片</label>
                <div class="controls">
                    <?= $form->field($model, 'imageFile', ['template' => '{input}'])->fileInput() ?>
                    <?= $form->field($model, 'imageFile', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn blue"><i class="icon-ok"></i> 提交</button>
                <a href="list" class="btn">取消</a>
            </div>
        <?php $form->end(); ?>
    </div>
</div>
<?php $this->endBlock(); ?>