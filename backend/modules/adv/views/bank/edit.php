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
        .title{margin-right: 10px;line-height: 39px;}
    </style>
</head>
<body>
<?php $form = ActiveForm::begin() ?>
<?= $form->field($eBank, 'typePersonal')->checkbox() ?>
<?= $form->field($eBank, 'typeBusiness')->checkbox() ?>
<?= $form->field($qPay, 'allowBind')->checkbox() ?>
<?= $form->field($qPay, 'isDisabled')->checkbox(['uncheck' => 1,'value'=>0]) ?>
<?= $form->field($qDeputePay, 'isDisabled')->checkbox(['uncheck' => 1,'value'=>0]) ?>
<div class="form-group title">
    <label for="">快捷充值限额</label>
</div>
<?= $form->field($qPay, 'singleLimit', ['template' => '{input}{label}'])->textInput(['style' => 'width:30px;']) ?>
<?= $form->field($qPay, 'dailyLimit', ['template' => '{input}{label}'])->textInput(['style' => 'width:30px']) ?>
<div class="form-group title">
    <label for="">商业委托快捷充值限额</label>
</div>
<?= $form->field($qDeputePay, 'singleLimit', ['template' => '{input}{label}'])->textInput(['style' => 'width:30px;']) ?>
<?= $form->field($qDeputePay, 'dailyLimit', ['template' => '{input}{label}'])->textInput(['style' => 'width:30px']) ?>

<div class="button_div">
    <span class="btn blue" id="submit_bank">确认修改</span>
    <span class="btn" id="cancel_bank">取消</span>
</div>
<?php ActiveForm::end() ?>
<script type="text/javascript">
    $(function () {
        var div = $('div.form-group');
        $(div[6]).css({'float':'left'});
        $(div[5]).css({'float':'left'});
        $(div[8]).css({'float':'left'});
        $(div[9]).css({'float':'left'});
        $('.button_div').css({'clear':'both'});
        $('#submit_bank').click(function () {
            $.post('/adv/bank/edit?id=<?= $id ?>',
                $('form').serialize(),
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

