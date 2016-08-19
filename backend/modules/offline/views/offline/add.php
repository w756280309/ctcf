<?php

$this->title = '导入新数据';
use yii\widgets\ActiveForm;

?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                线下数据
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/offline/offline/list">线下数据</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/offline/offline/list">线下数据</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/offline/offline/add">导入新数据</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="portlet-body">
        <?php $form = ActiveForm::begin(['action'=>'/offline/offline/add', 'options'=>['class'=>'form-horizontal form-bordered form-label-stripped', 'enctype'=>'multipart/form-data']]); ?>
            <table class="table">
                <tr>
                    <td width="10%">
                        <span class="title" style="display: inline-block;line-height:27px;">上传excel表格</span>
                    </td>
                    <td>
                        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken(); ?>">
                        <div>
                            <?= $form->field($model, 'excel', ['template' => '{input}'])->fileInput() ?>
                            <?= $form->field($model, 'excel', ['template' => '{error}']) ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="hidden" name="flag" value="1" style="display: none;">
                    </td>
                    <td>
                        <button id="post-excel" class="btn blue" style="width: 60px;display: inline-block;">提交</button>
                        <input type="reset" value="取消" class="btn white" style="width: 60px;display: inline-block" />
                    </td>
                </tr>
            </table>
        <?php $form->end(); ?>
    </div>
</div>
<script>
    $('#post-excel').bind('click', function() {
        if ($(this).hasClass('clicked-btn')) {
            return false;
        }
        $(this).addClass('clicked-btn');
    });
</script>
<?php $this->endBlock(); ?>
