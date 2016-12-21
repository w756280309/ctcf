<?php
use yii\widgets\ActiveForm;

$btnDesc = empty($meta->id) ? '添加' : '编辑';
$this->title = $btnDesc . 'META';
?>

<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                发行方管理 <small>运营模块</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/growth/page-meta/list">页面META</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="#"><?= $btnDesc ?></a>
                </li>
            </ul>
        </div>

        <?php
        $form = ActiveForm::begin([
            'action' => '/growth/page-meta/'.(empty($meta->id) ? 'add' : 'edit?id='.$meta->id),
            'options' => [
                'class' => 'form-horizontal form-bordered form-label-stripped',
                'enctype' => 'multipart/form-data',
            ]
        ]);
        ?>
        <div class="portlet-body form">
            <div class="control-group">
                <label class="control-label">别名</label>
                <div class="controls">
                    <?= $form->field($meta, 'alias', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '请输入别名']])->textInput() ?>
                    <?= $form->field($meta, 'alias', ['template' => '{error}']) ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">链接地址</label>
                <div class="controls">
                    <?= $form->field($meta, 'url', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '请输入链接地址', 'class' => 'span8']])->textarea() ?>
                    <?= $form->field($meta, 'url', ['template' => '{error}']) ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">页面标题</label>
                <div class="controls">
                    <?= $form->field($meta, 'title', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '请输入页面标题', 'class' => 'span8']])->textInput() ?>
                    <?= $form->field($meta, 'title', ['template' => '{error}']) ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">关键词</label>
                <div class="controls">
                    <?= $form->field($meta, 'keywords', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span8', 'placeholder' => '请输入关键词']])->textarea(['rows' => 5]) ?>
                    <?= $form->field($meta, 'keywords', ['template' => '{error}']) ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">描述</label>
                <div class="controls">
                    <?= $form->field($meta, 'description', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span8', 'placeholder' => '请输入描述']])->textarea(['rows' => 7]) ?>
                    <?= $form->field($meta, 'description', ['template' => '{error}']) ?>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn blue"><i class="icon-ok"></i> <?= $btnDesc ?></button>&nbsp;&nbsp;&nbsp;
                <a href="list" class="btn">取消</a>
            </div>
        </div>
        <?php $form->end(); ?>
    </div>
<?php $this->endBlock(); ?>