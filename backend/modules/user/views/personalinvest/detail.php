<?php
use yii\helpers\Html;
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
    <title><?= Html::encode('详情') ?></title>
    <?php $this->head() ?>
    <link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
    <link href="/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="/css/style-metro.css" rel="stylesheet" type="text/css"/>
    <link href="/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="/css/style-responsive.css" rel="stylesheet" type="text/css"/>
    <link href="/css/default.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <script src="/js/jquery.js" type="text/javascript"></script>

    <script type="text/javascript" src="/js/layer/layer.min.js"></script>
    <script type="text/javascript" src="/js/layer/extend/layer.ext.js"></script>
    <script type="text/javascript" src="/js/showres.js"></script>
    <script type="text/javascript" src="/js/ajax.js"></script>
    <style type="text/css">
        .portlet-body .title{
            height: 34px;
            line-height: 34px;
            vertical-align: middle;
            font-size: 14px;
        }
        .has-error .help-block{
            color: red;
        }
        .form-group div.radio label{
            width:400px;
        }
    </style>
    <script type="text/javascript">
        var urls = [
            '/order/onlinefangkuan/checkfk',//审核通过
            '/order/order/fkdeny',//审核不通过
            '/order/order/fkop',//放款
        ];

        jQuery(document).ready(function () {
            $('.fkbutton').bind('click', function() {
                var csrftoken = '<?= Yii::$app->request->getCsrfToken() ?>';
                key = $(this).attr('data-index');
                var pid = <?=  Yii::$app->request->get('pid') ?>;
                bool = true;
                status = 1;

                if (key == 0) {
                    if (confirm('确认审核通过放款吗？')) {
                        $.post(urls[key], {pid: pid, status: status, _csrf: csrftoken}, function(data) {
                            alert(data.msg);
                            if (data.res == 1) {
                                window.parent.location.reload();
                            }
                        });
                    }
                } else if (key == 1) {
                    if(confirm('确认审核不通过吗？')) {
                        closewin();
                    }
                }
            })
        })
    </script>
</head>
<body class="page-header-fixed page-full-width" style="background-color:white !important">
<div class="page-container row-fluid" style="margin-top:0px">
    <div class="page-content">
        <div class="form-horizontal form-view">
            <div class="control-group">
                <span style="font-size:20px">付息详情</span>
            </div>
            <?php foreach ($time as $k => $v) : ?>
            <div class="row-fluid" style="margin-top:3px;">
                <div style="width:48%;float:left;">
                    <span style="margin-left:20%;">付息时间：<?= $v ?></span>
                </div>
                <div style="width:48%;float:left;">
                    <span>付息金额：<?= $money[$k] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</body>
</html>