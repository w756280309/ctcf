<?php
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerCssFile('/vendor/kindeditor/4.1.11/themes/default/default.css');
$this->registerCssFile('/vendor/kindeditor/4.1.11/plugins/code/prettify.css');
$this->registerJsFile('/vendor/kindeditor/4.1.11/kindeditor-all-min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/vendor/kindeditor/4.1.11/lang/zh-CN.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/vendor/kindeditor/4.1.11/plugins/code/prettify.js', ['depends' => 'yii\web\YiiAsset']);
$this->title = '编辑资讯';
?>
<?php $this->beginBlock('blockmain'); ?>
    <div class="container-fluid">
        <!-- BEGIN PAGE HEADER-->
        <div class="row-fluid">
            <div class="span12">
                <h3 class="page-title">
                    资讯管理
                    <small>运营模块</small>
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home"></i>
                        <a href="/adv/adv/index">运营管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="/news/news/index">资讯管理</a>
                        <i class="icon-angle-right"></i>
                    </li>
                    <li>
                        <a href="javascript:void(0);">编辑资讯</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body form">
                <!-- BEGIN FORM-->
                <?php $form = ActiveForm::begin([ 'action' => "/news/news/edit?id=" . $model->id, 'options' => ['class' => 'form-horizontal form-bordered form-label-stripped', 'enctype' => 'multipart/form-data']]); ?>
                <div class="control-group">
                    <label class="control-label">标题</label>
                    <div class="controls">
                        <?= $form->field($model, 'title', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '标题']])->textInput() ?>
                        <?= $form->field($model, 'title', ['template' => '{error}']); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">概括</label>
                    <div class="controls">
                        <?= $form->field($model, 'summary', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '概括']])->textInput() ?>
                        <?= $form->field($model, 'summary', ['template' => '{error}']); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">分类</label>
                    <div class="controls">
                        <?= $form->field($model, 'category', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span12', 'placeholder' => '分类']])->checkboxList(ArrayHelper::map($categories, 'id', 'name')) ?>
                        <?= $form->field($model, 'category', ['template' => '{error}']); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">状态</label>
                    <div class="controls">
                        <?= $form->field($model, 'status', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span3 Wdate', 'placeholder' => '状态']])->dropDownList($status) ?>
                        <?= $form->field($model, 'status', ['template' => '{error}']); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">发布时间</label>
                    <div class="controls">
                        <?= $form->field($model, 'news_time', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span3 Wdate', 'placeholder' => '选择发布时间', 'onclick' => 'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm:ss",maxDate:\'' . date("Y-m-d") . '\'});']])->textInput() ?>
                        <?= $form->field($model, 'news_time', ['template' => '{error}']); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">内容</label>
                    <div class="controls">
                        <?= $form->field($model, 'body', ['template' => '{input}', 'inputOptions' => ['style' => "width:688px; height:350px;"]])->textarea(); ?>
                        <?= $form->field($model, 'body', ['template' => '{error}']); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">PC缩略图</label>
                    <div class="controls">
                        <?= $form->field($model, 'pc_thumb', ['template' => '{input}<br><span class="notice">*图片上传格式必须为PNG或JPG，且大小不超过50K，尺寸限定为：宽271px，高156px</span>', 'inputOptions' => ['style' => "width:78%"]])->fileInput() ?>
                        <?= $form->field($model, 'pc_thumb', ['template' => '{error}']) ?>
                    </div>
                    <?php if (!empty($model->pc_thumb)) { ?>
                        <div class="controls">
                            <img src="<?= '/'.Html::encode($model->pc_thumb) ?>" alt="PC缩略图" />
                        </div>
                    <?php } ?>
                </div>
                <div class="control-group">
                    <label class="control-label">显示顺序</label>
                    <div class="controls">
                        <?= $form->field($model, 'sort', ['template' => '{input}', 'inputOptions' => ['autocomplete' => "off", 'class' => 'm-wrap span3', 'placeholder' => '显示顺序']])->textInput() ?>
                        <?= $form->field($model, 'sort', ['template' => '{error}']); ?>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn blue"><i class="icon-ok"></i> 提交</button>
                    <a href="/news/news/index" class="btn">取消</a>
                </div>
                <?php $form->end(); ?>
                <!-- END FORM-->
            </div>
        </div>
    </div>
    <style>
        .ke-icon-text_indent {
            background-image: url('/vendor/kindeditor/4.1.11/themes/default/default.png');
            background-position: 0px -672px;
            width: 16px;
            height: 16px;
        }
    </style>
    <script type="text/javascript">
        function parentP(KNode) {
            if (!KNode) {
                return null;
            }
            if (KNode.name == 'body') {
                return null;
            }
            if (KNode.name == 'p') {
                return KNode;
            }
            if (KNode.parent()) {
                return parentP(KNode.parent());
            }
            return null;
        }
        jQuery(document).ready(function () {
            KindEditor.lang({
                text_indent : '首行缩进'
            });
            KindEditor.plugin('text_index', function(K) {
                var self = this, name = 'text_indent';
                self.clickToolbar(name, function() {
                    var cmd = self.cmd;
                    if (cmd.sel.anchorNode) {
                        var p = K(cmd.sel.anchorNode);
                        p = parentP(p);
                        if (p && p.name == 'p') {
                            if (p.hasClass('text_indent')) {
                                p.removeClass('text_indent');
                                p.css('text-indent', '0em');
                            } else {
                                p.addClass('text_indent');
                                p.css('text-indent', '2em');
                            }
                        }
                    }
                });
            });
            KindEditor.ready(function(K) {
                var editor1 = K.create('#news-body', {
                    pasteType:1,
                    items:['source', 'preview','fontname', 'fontsize',  'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', 'removeformat', '|','plainpaste','wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist','insertunorderedlist', '|', 'image', 'table', 'link', 'unlink', 'text_indent'],
                    uploadJson :'/news/news/upload', //指定上传文件的服务器端程序
                    extraFileUploadParams:{_csrf:"<?= Yii::$app->request->csrfToken ?>"}
                });
                prettyPrint();
            });
        });
    </script>
<?php $this->endBlock(); ?>