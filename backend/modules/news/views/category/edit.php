<?php
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\models\Category;

$this->title = '添加/编辑分类';
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                分类管理
                <small>运营模块</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/news/category/index">分类管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">分类列表</a>
                </li>
            </ul>
        </div>
        <div class="portlet-body form">
            <!-- BEGIN FORM-->
            <?php $form = ActiveForm::begin(['action' => "/news/category/edit?id=" . $model->id, 'options' => ['class' => 'form-horizontal form-bordered form-label-stripped']]); ?>
            <div class="control-group">
                <label class="control-label">分类类型</label>
                <div class="controls">
                    <?= $form->field($model, 'type', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span3', 'placeholder' => '分类类型']])->dropDownList(Category::getTypeArray()) ?>
                    <?= $form->field($model, 'type', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">分类名称</label>
                <div class="controls">
                    <?= $form->field($model, 'name', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span6', 'placeholder' => '分类名称']])->textInput() ?>
                    <?= $form->field($model, 'name', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">分类简介</label>
                <div class="controls">
                    <?= $form->field($model, 'description', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '分类简介']])->textInput() ?>
                    <?= $form->field($model, 'description', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">上级分类</label>
                <div class="controls">
                    <?= $form->field($model, 'parent_id', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span3', 'placeholder' => '上级分类']])->dropDownList($categoryTree) ?>
                    <?= $form->field($model, 'parent_id', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">状态</label>
                <div class="controls">
                    <?= $form->field($model, 'status', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span3 Wdate', 'placeholder' => '状态']])->dropDownList(\common\models\Category::getStatusArray()) ?>
                    <?= $form->field($model, 'status', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">显示顺序</label>
                <div class="controls">
                    <?= $form->field($model, 'sort', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span3', 'placeholder' => '资讯排序']])->textInput() ?>
                    <?= $form->field($model, 'sort', ['template' => '{error}']); ?>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn blue"><i class="icon-ok"></i> 提交</button>
                <a href="/news/category/index" class="btn">取消</a>
            </div>
            <?php $form->end(); ?>
            <!-- END FORM-->
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {

    });
</script>
<?php $this->endBlock(); ?>


   


