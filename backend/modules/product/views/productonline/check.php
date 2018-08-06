<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link href="/css/bootstrap-3.3.4.min.css" rel="stylesheet" type="text/css"/>
    <link href="/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="/css/style-responsive.css" rel="stylesheet" type="text/css"/>
    <link href="/css/default.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="/css/uniform.default.css" rel="stylesheet" type="text/css"/>

    <script src="/js/jquery.js" type="text/javascript"></script>
    <script src="/js/bootstrap3.3.4.min.js"></script>
    <script type="text/javascript" src="/js/layer/layer.min.js"></script>
    <script type="text/javascript" src="/js/layer/extend/layer.ext.js"></script>
    <script type="text/javascript" src="/js/showres.js"></script>
    <script type="text/javascript" src="/js/ajax.js"></script>
    <style type="text/css">
        .form-group {
            margin-bottom: 0px;
        }
        .control-label {
            padding-right: 0px;
        }
        .form-control-static {
            margin-top: 2px;
        }
    </style>
</head>

<body style="background-color:white !important">
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <span>备注：</span>
            <textarea id="check_remark" name="check_remark" style="width: 370px;height: 150px;resize: none;display: block;outline: none;padding:5px 10px;" <?= $deal->check_status === 1 ? '' : 'disabled' ?>><?= $deal->check_remark ?></textarea>
            <?php if ($deal->check_status === 1) { ?>
                <p style="line-height: 30px;">
                    不通过<input style="vertical-align: sub;" type="radio" value="2" name="check_status">
                    通过<input style="vertical-align: sub;" type="radio" value="3" checked name="check_status">
                </p>
                <button onclick="docheck();">提交</button>
            <?php } ?>
        </div>

    </div>
</div>

<script type="text/javascript">
    function docheck() {
        var id = <?= $deal->id ?>;
        var check_status = $('input[name="check_status"]:checked').val();
        var check_remark = $('#check_remark').val();
        if (check_status == '2' && check_remark == '') {
            layer.alert('审核不通过需要备注', {closeBtn: 0, icon: 5});
            return false;
        }
        var csrfToken = $("meta[name='csrf-token']").attr('content');
        $.ajax({
            url: '/product/productonline/docheck',
            data: {id: id, check_status: check_status, check_remark: check_remark, _csrf: csrfToken},
            type: 'POST',
            dataType: 'JSON',
            success: function (res) {
                if (res.code === 0) {
                    layer.msg(res.message, {icon: 1});
                    setTimeout(function () {
                        window.parent.location.reload();
                    }, 1000)
                } else {
                    layer.alert(res.message, {closeBtn: 0, icon: 5});
                }
            },
            error: function () {
                layer.alert('系统繁忙,请稍后重试!', {closeBtn: 0, icon: 5});
                location.reload();
            }
        });
    }
</script>

</body>
</html>
