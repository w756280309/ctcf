<?php
use yii\widgets\ActiveForm;

$btnDesc = empty($ob->id) ? '添加' : '编辑';
$this->title = '底层融资方'.$btnDesc;
?>
    <style>
        .help-block {
            color: red;
        }
    </style>

<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                底层融资方管理 <small>会员管理模块</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/user/user/listt">会员管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/user/user/listob">底层融资方管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);"><?= $btnDesc ?></a>
                </li>
            </ul>
        </div>

        <?php
        $form = ActiveForm::begin([
            'action' => '/user/user/'.(empty($ob->id) ? 'addob' : 'editob?id='.$ob->id),
            'options' => [
                'class' => 'form-horizontal form-bordered form-label-stripped',
                'enctype' => 'multipart/form-data',
            ]
        ]);
        ?>
        <div class="portlet-body form">
            <div class="control-group">
                <label class="control-label">底层融资方名称</label>
                <div class="controls">
                    <?= $form->field($ob, 'name', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '底层融资方名称']])->textInput() ?>
                    <?= $form->field($ob, 'name', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn blue"><i class="icon-ok"></i> <?= $btnDesc ?></button>&nbsp;&nbsp;&nbsp;
                <a href="/user/user/listob" class="btn">取消</a>
            </div>
        </div>
        <?php $form->end(); ?>
    </div>
<?php $this->endBlock(); ?>
