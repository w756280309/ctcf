<?php

use common\models\adv\Adv;
use yii\widgets\ActiveForm;

$this->title = '首页开屏图添加/编辑';

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
                        <a href="/adv/adv/kaiping-list">首页开屏</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/adv/adv/kaiping-edit"><?= $this->title ?></a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body form">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'adv_form',
                        'action' => "/adv/adv/kaiping-edit?id=".$adv->id,
                        'options' => [
                            'class' => 'form-horizontal form-bordered form-label-stripped',
                            'enctype' => 'multipart/form-data',
                        ]
                    ]);
                ?>
                <?= $form->field($adv, 'type')->label(false)->hiddenInput(['id' => 'advType', 'value' => Adv::TYPE_KAIPING]) ?>
                <?= $form->field($adv, 'showOnPc')->label(false)->hiddenInput(['id' => 'showOnPc', 'value' => 0]) ?>

                <?php if ($adv->id) { ?>
                    <div class="control-group">
                        <label class="control-label">序号</label>
                        <div class="controls"><?= $adv->sn ?></div>
                    </div>
                <?php } ?>
                <div class="control-group">
                    <label class="control-label">标题</label>
                    <div class="controls">
                        <?= $form->field($adv, 'title', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '标题']])->textInput(['style' => 'display:block !important']) ?>
                        <?= $form->field($adv, 'title', ['template' => '{error}']) ?>
                    </div>
                </div>
                <div class="control-group" id="app">
                    <label class="control-label">APP端不显示</label>
                    <div class="controls">
                        <?= $form->field($adv, 'isDisabledInApp', ['template' => '{input}<span class="notice">*目前上传图片均为移动端(包括WAP和APP)</span>', 'inputOptions' => ['id' => 'isDisabledInApp']])->checkBox(['autocomplete' => "on"]) ?>
                        <?= $form->field($adv, 'isDisabledInApp', ['template' => '{error}']) ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">首页开屏图</label>
                    <div class="controls">
                        <?= $form->field($adv, 'imageUri', ['template' => '{input}<span class="notice">*图片上传格式必须为PNG或JPG，尺寸限定为：宽600px，高800px，图片大小不超过1M</span>'])->fileInput() ?>
                        <?= $form->field($adv, 'imageUri', ['template' => '{error}']) ?>
                    </div>
                    <?php if (!empty($adv->media)) { ?>
                        <div class="controls" id="notePic">
                            <img src="<?= '/'.$adv->media->uri ?>" alt="首页开屏图">
                        </div>
                    <?php } ?>
                </div>
                <div class="control-group">
                    <label class="control-label">链接地址</label>
                    <div class="controls">
                        <?= $form->field($adv, 'link', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '链接']])->textarea(['rows' => 3]) ?>
                        <?= $form->field($adv, 'link', ['template' => '{error}']) ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">显示顺序</label>
                    <div class="controls">
                        <?= $form->field($adv, 'show_order', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '显示顺序']])->textInput() ?>
                        <?= $form->field($adv, 'show_order', ['template' => '{error}']) ?>
                    </div>
                </div>
                <!--普通提交-->
                <div class="form-actions">
                    <button type="submit" class="btn blue"><i class="icon-ok"></i> 提交</button>
                    <a href="/adv/adv/kaiping-list" class="btn">取消</a>
                </div>
                <?php $form->end(); ?>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(function () {
            $('#adv-image').on('click', function () {
                $('#notePic').html('');
            });
        });
    </script>
<?php $this->endBlock(); ?>