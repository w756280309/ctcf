<?php
use yii\widgets\ActiveForm;

$btnDesc = empty($issuer->id) ? '添加' : '编辑';
$this->title = '发行方'.$btnDesc;
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
                发行方管理 <small>运营模块</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/product/issuer/list">发行方管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="#"><?= $btnDesc ?></a>
                </li>
            </ul>
        </div>

        <?php
        $form = ActiveForm::begin([
            'action' => '/product/issuer/'.(empty($issuer->id) ? 'add' : 'edit?id='.$issuer->id),
            'options' => [
                'class' => 'form-horizontal form-bordered form-label-stripped',
                'enctype' => 'multipart/form-data',
            ]
        ]);
        ?>
        <div class="portlet-body form">
            <div class="control-group">
                <label class="control-label">发行方名称</label>
                <div class="controls">
                    <?= $form->field($issuer, 'name', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '发行方名称']])->textInput() ?>
                    <?= $form->field($issuer, 'name', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">视频名称</label>
                <div class="controls">
                    <?= $form->field($issuer, 'mediaTitle', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '视频名称']])->textInput() ?>
                    <?= $form->field($issuer, 'mediaTitle', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">视频地址</label>
                <div class="controls">
                    <?= $form->field($issuer, 'videoUrl', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '不应包含中文字符', 'class' => 'span12']])->textarea() ?>
                    <?= $form->field($issuer, 'videoUrl', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">视频示例图</label>
                <div class="controls">
                    <?= $form->field($issuer, 'imgUrl', ['template' => '{input}<span class="notice">*图片上传格式必须为PNG或JPG，尺寸限定为：宽750px，高340px</span>'])->fileInput() ?>
                    <?= $form->field($issuer, 'imgUrl', ['template' => '{error}']) ?>
                </div>
                <?php if (!empty($issuer->imgUrl)) { ?>
                    <div class="controls">
                        <img src="<?= '/'.$issuer->imgUrl ?>" alt="发行方视频示例图"/>
                    </div>
                <?php } ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn blue"><i class="icon-ok"></i> <?= $btnDesc ?></button>&nbsp;&nbsp;&nbsp;
                <a href="list" class="btn">取消</a>
            </div>
        </div>
        <?php $form->end(); ?>
    </div>
<?php $this->endBlock(); ?>