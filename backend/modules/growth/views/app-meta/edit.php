<?php
use yii\widgets\ActiveForm;

$this->title = '编辑应用信息';
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
                    <a href="/growth/app-meta/index">应用信息</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="#">编辑</a>
                </li>
            </ul>
        </div>

        <?php
        $form = ActiveForm::begin([
            'action' => '/growth/app-meta/edit?id='. $meta->id,
            'id' => 'import_form',
            'method' => 'post',
            'options' => [
                'class' => 'form-horizontal form-bordered form-label-stripped',
            ]
        ]);
        ?>
        <div class="portlet-body form">
            <div class="control-group">
                <label class="control-label"><?= $meta->name?></label>
                <div class="controls">
                    <?= $form->field($meta, 'value', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '']])->textInput() ?>
                    <?= $form->field($meta, 'value', ['template' => '{error}']) ?>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn blue" id="submit_btn"><i class="icon-ok"></i> 编辑</button>
                <a href="/growth/app-meta/index" class="btn">取消</a>
            </div>
        </div>
        <?php $form->end(); ?>
    </div>
    <script>
        $(".form-actions").on( "click", "#submit_btn", function(e) {
            e.preventDefault();
            $(this).attr('disabled', true);
            $('#import_form').submit();
            setTimeout(function(){
                $("#submit_btn").attr('disabled', false);
            }, 2000);
        });
    </script>
<?php $this->endBlock(); ?>