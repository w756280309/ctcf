<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en"> <!--<![endif]-->
<head>
    <link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/css/style-metro.css" rel="stylesheet" type="text/css"/>
    <script src="/js/jquery-1.10.1.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="/js/layer/layer.min.js"></script>
    <style type="text/css">
        form {
            margin: 20px;
        }

        form div {
            margin-bottom: 10px;
        }

        .button_div {
            text-align: center;
        }
    </style>
</head>
<body>
<?php $form = ActiveForm::begin() ?>
<?= $form->field($eBank, 'typePersonal')->checkbox() ?>
<?= $form->field($eBank, 'typeBusiness')->checkbox() ?>
<?= $form->field($qPay, 'isDisabled')->checkbox() ?>
<?= $form->field($qPay, 'singleLimit', ['template' => '{input}{label}'])->textInput(['style' => 'width:80px;']) ?>
<?= $form->field($qPay, 'dailyLimit', ['template' => '{input}{label}'])->textInput(['style' => 'width:80px']) ?>
<div class="button_div">
    <span class="btn blue" id="submit_bank">确认修改</span>
    <span class="btn" id="cancel_bank">取消</span>
</div>
<?php ActiveForm::end() ?>
<script type="text/javascript">
    $(function () {
        var dis = $('#qpayconfig-isdisabled');
        dis.trigger('click');
        $('#submit_bank').click(function () {
            if (dis.is(':checked')) {
                isDisabled = 0;
            } else {
                isDisabled = 1;
            }
            $.post('/adv/bank/edit?id=<?= $id ?>',
                $('form').serialize() + '&QpayConfig[isDisabled]=' + isDisabled,
                function (data) {
                    if (true === data.code) {
                        layer.msg(data.msg);
                        parent.location.reload();
                    } else {
                        layer.msg(data.msg);
                    }
                });
        });
        $('#cancel_bank').click(function () {
            parent.layer.closeAll();
        });

    });
</script>
</body>
</html>

