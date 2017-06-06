<?php
use yii\widgets\ActiveForm;

$this->title = '编辑渠道信息';
?>

<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                运营管理 <small>渠道管理</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/growth/referral/index">渠道管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="#">编辑渠道</a>
                </li>
            </ul>
        </div>

        <?php
        $form = ActiveForm::begin([
            'action' => '/growth/referral/edit?id='. $model->id,
            'id' => 'import_form',
            'method' => 'post',
            'options' => [
                'class' => 'form-horizontal form-bordered form-label-stripped',
            ]
        ]);
        ?>
        <div class="portlet-body form">
            <div class="control-group">
                <label class="control-label">名称</label>
                <div class="controls">
                    <?= $form->field($model, 'name', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '']])->textInput() ?>
                    <?= $form->field($model, 'name', ['template' => '{error}']) ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">渠道码</label>
                <div class="controls">
                    <?= $form->field($model, 'code', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '']])->textInput() ?>
                    <?= $form->field($model, 'code', ['template' => '{error}']) ?>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn blue" id="submit_btn"><i class="icon-ok"></i> 编辑</button>
                <a href="/growth/referral/index" class="btn">取消</a>
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