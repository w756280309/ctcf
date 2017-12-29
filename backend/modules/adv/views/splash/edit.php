<?php

use yii\widgets\ActiveForm;
use common\models\adv\Splash;
use yii\web\YiiAsset;

$splash = new Splash();
$this->title = '闪屏图添加/编辑';
$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => YiiAsset::class]);
?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                运营管理 <small>闪屏图</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/adv/splash/index">闪屏图管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);"><?= $this->title ?></a>
                </li>
            </ul>
        </div>

        <div class="portlet-body form">
            <?php
                $form = ActiveForm::begin([
                    'id' => 'adv_form',
                    'action' => "/adv/splash/edit?id=".$model->id,
                    'options' => [
                        'class' => 'form-horizontal form-bordered form-label-stripped',
                        'enctype' => 'multipart/form-data',
                    ]
                ]);
            ?>

            <?php if ($model->id) { ?>
                <div class="control-group">
                    <label class="control-label">序号</label>
                    <div class="controls"><?= $model->sn ?></div>
                </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label">标题</label>
                <div class="controls">
                    <?= $form->field($model, 'title', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '标题,字数在60以内']])->textInput(['style' => 'display:block !important']) ?>
                    <?= $form->field($model, 'title', ['template' => '{error}']) ?>
                </div>
            </div>
            <?php
                foreach ($images as $image) {
            ?>
                <div class="control-group">
                    <label class="control-label">上传<?= $image['width'] ?>*<?= $image['height'] ?>图片</label>
                    <div class="controls">
                        <?=
                        $form->field($model, $image['name'], [
                            'template' => '{input}
                        <span class="notice" >*图片大小不超过1M，格式可以为jpg或png，并且大小限定为：高'. $image['height'] . 'px，宽' . $image['width'] . 'px</span>',
                        ])
                            ->fileInput()
                        ?>
                        <?= $form->field($model, $image['name'], ['template' => '{error}']) ?>
                    </div>
                    <?php if (!$model->hasErrors() && $model->getAttribute($image['name'])) { ?>
                        <div class="controls">
                            <img src="/<?= $splash->getMediaUri($model->getAttribute($image['name']))  ?>" alt="<?= $image['width'] ?>*<?= $image['height'] ?>图片">
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <div class="control-group">
                <label class="control-label">发布时间</label>
                <div class="controls">
                    <?= $form->field($model, 'publishTime', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span2', 'placeholder' => '点击获取发布时间']])->textInput([
                        'style' => 'display:block !important',
                        'onclick' => 'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm",minDate:\''.date('Y-m-d').'\'});'
                    ]) ?>
                    <?= $form->field($model, 'publishTime', ['template' => '{error}']) ?>
                </div>
            </div>
            <div class="control-group" id="share_select">
                <label class="control-label">是否发布</label>
                <div class="controls">
                    <?= $form->field($model, 'isPublished', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12']])->checkbox() ?>
                    <?= $form->field($model, 'isPublished', ['template' => '{error}']) ?>
                </div>
            </div>
            <!--普通提交-->
            <div class="form-actions">
                <button type="submit" class="btn blue"><i class="icon-ok"></i>保存</button>
                <a href="/adv/splash/index" class="btn">取消</a>
            </div>
            <?php $form->end(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $("#splash-ispublished").click(function () {
            if ($(this).attr('checked') != 'checked') {
                $("#splash-publishtime").val("")
            }
        })
    })
</script>
<?php $this->endBlock(); ?>