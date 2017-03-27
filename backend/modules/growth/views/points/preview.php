<?php

$this->title = '批量添加积分预览';
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                积分批量发放预览
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/growth/points/init">积分批量导入</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">导入记录预览</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="row-fluid">
        <?php if($failCount > 0) {?>
            <div class="alert alert-danger" role="alert">
                有<?= $failCount?>条异常数据，建议修改Excel后重新上传!
            </div>
        <?php }?>
        <table class="table">
            <tr>
                <td>总记录数: <?= $totalCount?></td>
                <td>异常记录数: <?= $failCount?></td>
                <td>正常记录数: <?= $successCount?></td>
                <?php if ($successCount > 0) {?>
                    <td>
                        <?php $form = \yii\widgets\ActiveForm::begin(['action' => '/growth/points/add?batchSn='.$batchSn, 'options' => ['class' => 'form-group', 'id' => 'add_form']]); ?>
                        <?=  \yii\helpers\Html::submitButton('确认导入['.$successCount.']', ['id' => 'add_record', 'class' => 'btn btn-primary blue'])?>
                        <?php $form->end(); ?>
                    </td>
                <?php }?>
                <?php if ($failCount > 0) { ?>
                    <td>
                        <a href="/growth/points/init" class="btn">返回修改数据</a>
                    </td>
                <?php }?>
            </tr>
        </table>
        <?= $this->renderFile('@backend/modules/growth/views/points/_excel_preview.php', [
            'dataProvider' => $dataProvider,
            'attributes' => $attributes,
        ])?>
        <script>
            $('#add_record').click(function(e){
                e.preventDefault();
                if (confirm('确认导入所有正常记录[<?= $successCount?>]?')) {
                    $(this).attr('disabled', true);
                    $('#add_form').submit();
                }
            });
        </script>
    </div>
</div>

<?php $this->endBlock(); ?>
