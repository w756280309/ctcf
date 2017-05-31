<?php

use yii\widgets\ActiveForm;

$this->title = '导出类型选择';
?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                数据导出 <small>运营模块</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/growth/export/index">导出类型选择</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="#">导出类型选择</a>
                </li>
            </ul>
        </div>

        <div class="span12">
            <?php
            $form = ActiveForm::begin([
                'id' => 'chose_export_type',
                'action' => '/growth/export/confirm',
                'method' => 'get',
            ]);
            ?>

            <?php foreach($exportConfig as $config) { ?>
            <div class="span3">
                <label class="control-label">
                    <input type="radio" name="key"  value="<?= $config['key']?>">
                    <?= $config['title']?>
                </label>
            </div>
            <?php }?>

            <div class="span12">
                <button type="submit" class="btn blue sub-btn">提交</button>&nbsp;&nbsp;&nbsp;
            </div>
            <?php $form->end(); ?>
        </div>

    </div>
</div>
<?php $this->endBlock(); ?>

