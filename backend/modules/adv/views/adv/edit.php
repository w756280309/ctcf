<?php

use yii\widgets\ActiveForm;

$this->registerJs("var t=0;", 1); //在头部加载  0:adv 1:product
$this->registerJsFile('/js/swfupload/swfupload.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/js/swfupload/handlers.js', ['depends' => 'yii\web\YiiAsset']);
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
            <?php $form = ActiveForm::begin(['id' => 'adv_form', 'action' => "/adv/adv/edit?id=".$model->id, 'options' => ['class' => 'form-horizontal form-bordered form-label-stripped']]); ?>
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
                    <?= $form->field($model, 'showOnPc', ['template' => '{input}', 'inputOptions' => ['id' => 'shebei']])->dropDownList([0 => '移动端显示', 1 => 'PC端显示']) ?>
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
                    <?= $form->field($model, 'image', ['template' => '{input}'])->hiddenInput() ?>
                    <div style="width: 180px; height: 18px; border: solid 1px #7FAAFF; background-color: #C5D9FF; padding: 2px;">
                        <span id="spanButtonPlaceholder_baoli"></span>
                    </div>
                    <div id="divFileProgressContainer_baoli" style="height: 10px; display:none"></div>
                    <div id='thumbnails_baoli'>
                        <?php if ($model->id) { ?>
                            <img src="/upload/adv/<?= $model->image ?>" style="margin: 5px; vertical-align: middle; width: 100px; height: 100px; opacity: 1;">
                        <?php } ?>
                    </div>
                </div>
                <div class="controls">
                    <span style="color:red">
                        <span id="notice">图片大小不超过2M，只限于jpg格式图片，并且大小限定为：高350px，宽750px</span>
                        <?= $form->field($model, 'image', ['template' => '{error}']) ?>
                    </span>
                </div>
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
                    <?= $form->field($share, 'imgUrl', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '图片地址']])->textInput() ?>
                    <?= $form->field($share, 'imgUrl', ['template' => '{error}']) ?>
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
    var swfu;
    var swfu_baoli;
    var swfu_gongguan;
    window.onload = function()
    {
        swfu_baoli = new SWFUpload({
            // Backend Settings
            upload_url: "/js/swfupload/upload_baoli.php?type=adv&shebei=<?= $model->showOnPc ? 'pc' : 'wap' ?>",
            post_params: {"PHPSESSID": "<?= rand(time(), time() + time()) ?>"},
            file_size_limit: "2 MB",
            file_types: "*.jpg;",
            file_types_description: "JPG Images;",
            file_upload_limit: 1,
            swfupload_preload_handler: preLoad,
            swfupload_load_failed_handler: loadFailed,
            file_queue_error_handler: fileQueueError,
            file_dialog_complete_handler: fileDialogComplete,
            upload_progress_handler: uploadProgress,
            upload_error_handler: uploadError,
            upload_success_handler: uploadSuccess,
            upload_complete_handler: uploadComplete,
            button_image_url: "/js/swfupload/SmallSpyGlassWithTransperancy_17x18.png",
            button_placeholder_id: "spanButtonPlaceholder_baoli",
            button_width: 180,
            button_height: 18,
            button_text: '<span class="button">选择图片 <span class="buttonSmall">(2 MB Max)</span></span>',
            button_text_style: '.button { font-family: Helvetica, Arial, sans-serif; font-size: 12pt; } .buttonSmall { font-size: 10pt; }',
            button_text_top_padding: 0,
            button_text_left_padding: 18,
            button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
            button_cursor: SWFUpload.CURSOR.HAND,
            flash_url: "/js/swfupload/swfupload.swf",
            flash9_url: "/js/swfupload/swfupload_fp9.swf",
            custom_settings:
            {
                upload_target: "divFileProgressContainer_baoli",
            },
            debug: false
        });
    };

    function delimg(id, img, obj)
    {
        var status = confirm("是否确定删除！");
        if (status)
        {
            $.get("/adv/adv/imgdel", {img: img, id: id}, function(data)
            {
                if (data)
                {
                    del();
                    alert("成功删除");
                }
            });
        }
    }

    $(function() {
        $('.ajax_button').click(function() {
            vals = $("#adv_form").serialize();
            $.post($("#adv_form").attr("action"), vals, function(data)
            {
                res(data, "/adv/adv/index");
            });
        });

        notice();

        $('#shebei').on('change', function() {
            var deviceName = notice();
            swfu_baoli.setUploadURL("/js/swfupload/upload_baoli.php?type=adv&shebei="+deviceName);
            del();
            if ($(this).val() == 1) {
                $('#share_select').hide();
                $('#share_block').hide();
            } else {
                $('#share_select').show();
                if ($('#adv-canshare').attr('checked') == 'checked') {
                    $('#share_block').show();
                }
            }
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
        var v = $('#shebei').val();

        if ('1' === v) {
            $('#app').hide();
            $('#isDisabledInApp').val('');
            $('#notice').html('图片大小不超过2M，只限于jpg格式图片，并且大小限定为：高340px，宽1920px');

            return 'pc';
        } else {
            $('#app').show();
            $('#notice').html('图片大小不超过2M，只限于jpg格式图片，并且大小限定为：高350px，宽750px');

            return 'wap';
        }
    }

    function del()
    {
        var stats = swfu_baoli.getStats();
        stats.successful_uploads--;
        swfu_baoli.setStats(stats);
        $('#thumbnails_baoli').find('img').detach();
        $('#adv-image').val('');
    }
</script>
<?php $this->endBlock(); ?>