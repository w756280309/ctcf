<?php

use yii\widgets\ActiveForm;

$this->title = '导出类型选择';
$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);
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
                    <a href="#">导出参数</a>
                </li>
            </ul>
        </div>
        <div class="span12 alert alert-info">
            <?php
                echo $exportModel['content'];

                if ($exportModel['itemLabels']) {
                    echo '<br/><b>数据项： </b>'.implode('， ', $exportModel['itemLabels']);
                }
            ?>
        </div>

        <div class="span12">
            <?php
            $form = ActiveForm::begin([
                'id' => 'set_export_params',
                'action' => '/growth/export/confirm?key='.$exportModel['key'],
                'method' => 'post',
            ]);
            ?>

            <?php foreach($exportModel['params'] as $key => $config) { ?>
            <div class="span3">
                <label class="control-label">
                    <?= $config['title']?>
                </label>
                <div class="controls">
                    <?php if ($config['type'] === 'date') { ?>
                        <input type="text" name="<?= $key?>" onclick = "WdatePicker({dateFmt:'yyyy-MM-dd'})"  value="<?= $config['value']?>">
                    <?php } else { ?>
                        <input type="text" name="<?= $key?>"  value="<?= $config['value']?>">
                    <?php }?>
                </div>
            </div>
            <?php }?>

            <div class="span12">
                <button type="submit" class="btn blue sub-btn" id="submit_btn">下载</button>&nbsp;&nbsp;&nbsp;
            </div>
            <?php $form->end(); ?>
        </div>

    </div>
</div>

<script>
    $('#set_export_params').on('beforeSubmit', function(e){
        $('#submit_btn').attr('disabled', true);
    });
</script>
<?php $this->endBlock(); ?>

