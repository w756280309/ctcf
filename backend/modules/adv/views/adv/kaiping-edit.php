<?php
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
                <?php $form = ActiveForm::begin([
                    'id' => 'adv_form',
                    'action' => "/adv/adv/kaiping-edit?id=".$adv->id,
                    'options' => [
                        'class' => 'form-horizontal form-bordered form-label-stripped',
                        'enctype' => 'multipart/form-data'
                    ]
                ]);
                ?>
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
                <div class="control-group">
                    <label class="control-label">显示设备</label>
                    <div class="controls">
                        <?= $form->field($adv, 'showOnPc', ['template' => '{input}', 'inputOptions' => ['id' => 'shebei']])->dropDownList([0 => '移动端显示', 1 => 'PC端显示',2 => 'WAP端显示']) ?>
                        <?= $form->field($adv, 'showOnPc', ['template' => '{error}']) ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">首页开屏图</label>
                    <div class="controls">
                        <?= $form->field($adv, 'image', ['template' => '{input}<span class="notice">*图片上传格式必须为PNG或JPG，尺寸限定为：宽600px，高800px，图片大小不超过1M</span>'])->fileInput() ?>
                        <?= $form->field($adv, 'image', ['template' => '{error}']) ?>
                    </div>
                    <?php if (!empty($adv->image)) { ?>
                        <div class="controls">
                            <img src="<?= '/'.$adv->image ?>" alt="首页开屏图"/>
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
<?php $this->endBlock(); ?>