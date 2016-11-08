<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = '发行方'.($issuer->id ? '编辑' : '添加');
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
                    <?php $form = ActiveForm::begin(['id' => 'issuer_form', 'action' => '/product/issuer/'.($issuer->id ? 'edit?id='.$issuer->id : 'add')]); ?>
                        <div class="control-group">
                            <label class="control-label">发行方名称</label>
                            <div class="controls data-aff">
                                <?= $form->field($issuer, 'name', ['template' => '{input}', 'inputOptions' => ['autocomplete' => 'off', 'placeholder' => '发行方名称']])->textInput() ?>
                                <?= $form->field($issuer, 'name', ['template' => '{error}']) ?>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn green">修改</button>
                            <button type="button" class="btn green" onclick="closewin()">关闭窗口</button>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </body>
</html>