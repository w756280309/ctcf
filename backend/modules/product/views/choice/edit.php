<?php
use common\models\media\Media;
use common\models\product\Issuer;
use yii\widgets\ActiveForm;

$this->title = '首页精选项目管理';
$tupian = Issuer::findOne($issuer->id);
$bigPic = Media::findOne($tupian->big_pic);
$midPic = Media::findOne($tupian->mid_pic);
$smallPic = Media::findOne($tupian->small_pic);
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
                    <a href="#">首页精选项目管理</a>
                </li>
            </ul>
        </div>

        <?php
        $form = ActiveForm::begin([
            'action' => '/product/choice/edit?id='.$issuer->id,
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
                    <?= $form->field($issuer, 'name', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off', 'disabled' => 'true', 'placeholder' => '发行方名称']])->textInput() ?>
                    <?= $form->field($issuer, 'name', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">精选项目排序</label>
                <div class="controls">
                    <?= $form->field($issuer, 'sort', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '请填排序值']])->textInput() ?>
                    <?= $form->field($issuer, 'sort', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">首页显示</label>
                <div class="controls">
                    <?= $form->field($issuer, 'isShow', ['template' => '{input}'])->checkBox(['autocomplete'=>"on"]) ?>

                </div>
            </div>

            <div class="control-group">
                <label class="control-label">图片跳转地址</label>
                <div class="controls">
                    <?= $form->field($issuer, 'path', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off', 'class' => 'span12', 'placeholder' => '图片跳转地址']])->textInput() ?>
                    <?= $form->field($issuer, 'path', ['template' => '{error}']) ?>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">首页精选项目大图</label>
                <div class="controls">
                    <?= $form->field($issuer, 'big_pic', ['template' => '{input}<span class="notice">*图片上传格式必须为PNG或JPG，尺寸限定为：宽670px，高310px</span>'])->fileInput() ?>
                    <?= $form->field($issuer, 'big_pic', ['template' => '{error}']) ?>
                </div>
                <?php if (!empty($bigPic)) { ?>
                    <div class="controls">
                        <img src="<?= '/'.$bigPic->uri ?>" alt="首页精选项目大图"/>
                    </div>
                <?php } ?>
            </div>

            <div class="control-group">
                <label class="control-label">首页精选项目中图</label>
                <div class="controls">
                    <?= $form->field($issuer, 'mid_pic', ['template' => '{input}<span class="notice">*图片上传格式必须为PNG或JPG，尺寸限定为：宽370px，高310px</span>'])->fileInput() ?>
                    <?= $form->field($issuer, 'mid_pic', ['template' => '{error}']) ?>
                </div>
                <?php if (!empty($midPic)) { ?>
                    <div class="controls">
                        <img src="<?= '/'.$midPic->uri ?>" alt="首页精选项目中图"/>
                    </div>
                <?php } ?>
            </div>

            <div class="control-group">
                <label class="control-label">首页精选项目小图</label>
                <div class="controls">
                    <?= $form->field($issuer, 'small_pic', ['template' => '{input}<span class="notice">*图片上传格式必须为PNG或JPG，尺寸限定为：宽286px，高150px</span>'])->fileInput() ?>
                    <?= $form->field($issuer, 'small_pic', ['template' => '{error}']) ?>
                </div>
                <?php if (!empty($smallPic)) { ?>
                    <div class="controls">
                        <img src="<?= '/'.$smallPic->uri ?>" alt="首页精选项目小图"/>
                    </div>
                <?php } ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn blue"><i class="icon-ok"></i>编辑</button>&nbsp;&nbsp;&nbsp;
                <a href="/product/issuer/list" class="btn">取消</a>
            </div>
        </div>
        <?php $form->end(); ?>
    </div>
<?php $this->endBlock(); ?>