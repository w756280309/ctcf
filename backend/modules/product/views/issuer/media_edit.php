<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$btnDesc = $issuer->mediaUri ? '编辑' : '添加';
$this->title = '发行方视频'.$btnDesc;
?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="<?= Yii::$app->charset ?>">
        <?= Html::csrfMetaTags() ?>
        <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
        <link href="/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style-metro.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style-responsive.css" rel="stylesheet" type="text/css"/>
        <link href="/css/uniform.default.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="/js/jquery.js"></script>
        <script type="text/javascript" src="/js/layer/layer.min.js"></script>
        <script type="text/javascript" src="/js/layer/extend/layer.ext.js"></script>
        <script type="text/javascript" src="/js/showres.js"></script>
        <script type="text/javascript" src="/js/ajax.js"></script>
        <?php if ($res) : ?>
            <script type="text/javascript">
                $(function() {
                    newalert(1, '操作成功');
                    parent.location.reload();
                })
            </script>
        <?php endif; ?>
        <style>
            .help-block {
                color: red;
            }
        </style>
    </head>

    <body class="page-header-fixed page-full-width">
        <div class="page-container row-fluid">
            <div class="page-content">
                <div>&nbsp;</div>
                <div class="form-horizontal form-view">
                    <?php $form = ActiveForm::begin(['id' => 'issuer_form', 'action' => '/product/issuer/media-edit?id='.$issuer->id]); ?>
                    <div class="control-group">
                        <label class="control-label">发行方名称</label>
                        <div class="controls data-aff"><?= $issuer->name ?></div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">视频名称</label>
                        <div class="controls data-aff">
                            <?= $form->field($issuer, 'mediaTitle', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '视频名称']])->textInput() ?>
                            <?= $form->field($issuer, 'mediaTitle', ['template' => '{error}']) ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">视频地址</label>
                        <div class="controls data-aff">
                            <?= $form->field($issuer, 'mediaUri', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '不应包含中文字符']])->textarea() ?>
                            <?= $form->field($issuer, 'mediaUri', ['template' => '{error}']) ?>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn green"><?= $btnDesc ?></button>
                        <button type="button" class="btn green" onclick="closewin()">关闭窗口</button>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </body>
</html>