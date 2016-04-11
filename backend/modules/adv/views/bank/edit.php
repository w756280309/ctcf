<?php
use yii\helpers\Html;

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
<?= Html::beginForm('/adv/bank/edit?id=' . $id, 'post') ?>
<div>
    <?= Html::checkbox('isPersonal', $model->isPersonal ? 'checked' : false, ['value' => 1]) ?>个人网银充值
</div>
<div>
    <?= Html::checkbox('isBusiness', $model->isBusiness ? 'checked' : false, ['value' => 1]) ?>企业网银充值
</div>
<div>
    <?= Html::checkbox('isQuick', $model->isQuick ? 'checked' : false, ['value' => 0]) ?>快捷充值（wap端绑卡）
</div>
<div>
    快捷充值限额
    <?= Html::textInput('singleLimit', $model->singleLimit, ['style' => 'width:80px;']) ?> 万/次
    <?= Html::textInput('dailyLimit', $model->dailyLimit, ['style' => 'width:80px;']) ?> 万/日
</div>
<div class="button_div">
    <span class="btn blue" id="submit_bank">确认修改</span>
    <span class="btn" id="cancel_bank">取消</span>
</div>
<?= Html::endForm() ?>
<script type="text/javascript">
    $(function () {
        $('#submit_bank').click(function () {
            $.post('/adv/bank/edit?id=<?= $id ?>', $('form').serialize(), function (data) {
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

