<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
$this->title = '编辑文件';
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                资讯管理
                <small>运营模块</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/adminupload/upload/index">上传管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">文件上传</a>
                </li>
            </ul>
        </div>
        <div class="portlet-body form">
            <!-- BEGIN FORM-->
            <?php $form = ActiveForm::begin([ 'action' => "/adminupload/upload/edit?id=" . $model->id, 'options' => ['class' => 'form-horizontal form-bordered form-label-stripped', 'enctype' => 'multipart/form-data']]); ?>
            <div class="control-group">
                <label class="control-label">上传文件</label>
                <div class="controls">
                    <?= $form->field($model, 'link', ['template' => '{input}<br><span class="notice">*图片上传格式必须为PNG或JPG，且大小不超过1M</span>', 'inputOptions' => ['style' => "width:100%"]])->fileInput() ?>
                    <?= $form->field($model, 'link', ['template' => '{error}']) ?>
                </div>
                <?php if (!empty($model->link)) { ?>
                    <div class="controls">
                        <img src="<?= '/'.Html::encode($model->link) ?>" alt="图片" />
                    </div>
                <?php } ?>
            </div>
            <div class="control-group">
                <label class="control-label">显示文件名</label>
                <div class="controls">
                    <?= $form->field($model, 'title', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '文件名']])->textInput() ?>
                    <?= $form->field($model, 'title', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">套页面显示</label>
                <div class="controls">
                    <?= $form->field($model, 'allowHtml', ['template' => '{input}'])->checkBox(['autocomplete'=>"on"]) ?>
                    <?= $form->field($model, 'allowHtml', ['template' => '{error}']) ?>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn blue"><i class="icon-ok"></i> 提交</button>
                <a href="/adminupload/upload/index" class="btn">取消</a>
            </div>
            <?php $form->end(); ?>
            <!-- END FORM-->
        </div>
    </div>
</div>
<?php $this->endBlock(); ?>




