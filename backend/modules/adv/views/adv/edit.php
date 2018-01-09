<?php

use common\models\adv\Adv;
use yii\widgets\ActiveForm;
use yii\web\YiiAsset;

$this->title = '轮播图添加/编辑';
$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => YiiAsset::class]);
?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                运营管理 <small>运营管理模块【主要包含广告管理】</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/adv/adv/index">广告管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/adv/adv/index">首页轮播</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">添加</a>
                </li>
            </ul>
        </div>

        <div class="portlet-body form">
            <?php
                $form = ActiveForm::begin([
                    'id' => 'adv_form',
                    'action' => "/adv/adv/edit?id=".$model->id,
                    'options' => [
                        'class' => 'form-horizontal form-bordered form-label-stripped',
                        'enctype' => 'multipart/form-data',
                    ]
                ]);
            ?>
            <?= $form->field($model, 'type')->label(false)->hiddenInput(['id' => 'advType', 'value' => Adv::TYPE_LUNBO]) ?>

            <?php if ($model->id) { ?>
                <div class="control-group">
                    <label class="control-label">序号</label>
                    <div class="controls"><?= $model->sn ?></div>
                </div>
            <?php } ?>

            <div class="control-group">
                <label class="control-label">标题</label>
                <div class="controls">
                    <?= $form->field($model, 'title', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '标题']])->textInput(['style' => 'display:block !important']) ?>
                    <?= $form->field($model, 'title', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">描述</label>
                <div class="controls">
                    <?= $form->field($model, 'description', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '描述']])->textarea(['rows' => 3]) ?>
                    <?= $form->field($model, 'description', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">显示设备</label>
                <div class="controls">
                    <?= $form->field($model, 'showOnPc', ['template' => '{input}', 'inputOptions' => ['id' => 'showOnPc']])->dropDownList([0 => '移动端显示', 1 => 'PC端显示']) ?>
                    <?= $form->field($model, 'showOnPc', ['template' => '{error}']) ?>
                </div>
            </div>
            <div class="control-group" id="app">
                <label class="control-label">APP端不显示</label>
                <div class="controls">
                    <?= $form->field($model, 'isDisabledInApp', ['template' => '{input}', 'inputOptions' => ['id' => 'isDisabledInApp']])->checkBox(['autocomplete' => "on"]) ?>
                    <?= $form->field($model, 'isDisabledInApp', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">上传图片</label>
                <div class="controls">
                    <?=
                        $form->field($model, 'imageUri', [
                            'template' => '{input}<span class="notice" id="notice">*图片大小不超过200k，格式可以为jpg或png，并且大小限定为：高350px，宽750px</span>',
                        ])
                        ->fileInput()
                    ?>
                    <?= $form->field($model, 'imageUri', ['template' => '{error}']) ?>
                </div>
                <?php if (!$model->hasErrors() && !empty($model->media)) { ?>
                    <div class="controls" id="notePic">
                        <img src="/<?= $model->media->uri ?>" alt="首页开屏图">
                    </div>
                <?php } ?>
            </div>

            <div class="control-group">
                <label class="control-label">链接</label>
                <div class="controls">
                    <?= $form->field($model, 'link', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '链接']])->textarea(['rows' => 3]) ?>
                    <?= $form->field($model, 'link', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">显示顺序</label>
                <div class="controls">
                    <?= $form->field($model, 'show_order', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '显示顺序']])->textInput() ?>
                    <?= $form->field($model, 'show_order', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="control-group" id="share_select" style="display: <?= $model->showOnPc === 1 ? 'none' : 'block'?>">
                <label class="control-label">页面可分享</label>
                <div class="controls">
                    <?= $form->field($model, 'canShare', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12']])->checkbox() ?>
                    <?= $form->field($model, 'canShare', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="control-group" style="display: <?= ($model->canShare && $model->showOnPc === 0) ? 'block' : 'none'?>" id="share_block">
                <label class="control-label">分享详情</label>
                <div class="controls">
                    <?= $form->field($share, 'title', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '分享标题']])->textInput() ?>
                    <?= $form->field($share, 'title', ['template' => '{error}']) ?>
                </div>
                <div class="controls">
                    <?= $form->field($share, 'description', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '分享描述']])->textarea(['rows' => 3]) ?>
                    <?= $form->field($share, 'description', ['template' => '{error}']) ?>
                </div>
                <div class="controls">
                    <?= $form->field($share, 'imgUrl', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '图片地址，图片大小为300*300']])->textInput() ?>
                    <?= $form->field($share, 'imgUrl', ['template' => '{error}']) ?>
                </div>
                <div class="controls">
                    <?= $form->field($share, 'url', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '分享链接地址，必须为绝对地址']])->textarea(['rows' => 3]) ?>
                    <?= $form->field($share, 'url', ['template' => '{error}']) ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">上线时间</label>

                <div class="controls picker-start-date">
                    <?= $form->field($model, 'timing', [
                        //'template' => '{input}',
                        'inputOptions' => [
                            'autocomplete' => 'off',

                        ]])->checkbox(['class' => 'm-wrap span3 Wdate timing']) ?>
                    <?= $form->field($model, 'start_date', [
                        'template' => '{input}',
                        'inputOptions' => [
                            'autocomplete' => 'off',
                            'class' => 'm-wrap span4 Wdate',
                            'placeholder' => '选择开始时间',
                            'onclick' => 'WdatePicker({dateFmt: \'yyyy-MM-dd HH:mm:ss\'})',
                        ]])->textInput() ?>
                </div>
                <div class="control-group">
                    <label class="control-label">最少投资可见（元）</label>
                    <div class="controls">
                        <?= $form->field($model, 'investLeast', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '']])->textInput() ?>
                        <?= $form->field($model, 'investLeast', ['template' => '{error}']) ?>
                    </div>
                </div>
            </div>
            <!--普通提交-->
            <div class="form-actions">
                <button type="submit" class="btn blue"><i class="icon-ok"></i> 提交</button>
                <a href="/adv/adv/index" class="btn">取消</a>
            </div>
            <?php $form->end(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $('.timing').on('change', function(e) {
            var $this = $(this);
            $(this).closest('.picker-start-date')
                .find('.field-adv-start_date')
                .toggle($this.prop('checked'));
        }).trigger('change');

        $('.ajax_button').click(function() {
            vals = $("#adv_form").serialize();
            $.post($("#adv_form").attr("action"), vals, function(data)
            {
                res(data, "/adv/adv/index");
            });
        });

        $('#adv-image').on('click', function () {
            $('#notePic').html('');
        });

        notice();

        $('#showOnPc').on('change', function() {
            notice();
            $('#notePic').html('');
        });

        $('#adv-canshare').change(function(){
            if ($(this).attr('checked') == 'checked') {
                $('#share_block').show();
            } else {
                $('#share_block').hide();
            }
        });
    });

    function notice()
    {
        var v = $('#showOnPc').val();

        if ('1' === v) {
            $('#app').hide();
            $('#isDisabledInApp').val('');
            $('#notice').html('图片大小不超过2M，格式可以为jpg或png，并且大小限定为：高340px，宽1920px');
        } else {
            $('#app').show();
            $('#notice').html('图片大小不超过200k，格式可以为jpg或png，并且大小限定为：高350px，宽750px');
        }
    }
</script>
<?php $this->endBlock(); ?>