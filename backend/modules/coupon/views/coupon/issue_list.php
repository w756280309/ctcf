<?php
$this->title = '发放代金券';

use yii\helpers\Html;

$user_id = Html::encode($uid);
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
        <script src="/js/jquery.js" type="text/javascript"></script>

        <script type="text/javascript" src="/js/layer/layer.min.js"></script>
        <script type="text/javascript" src="/js/layer/extend/layer.ext.js"></script>
        <script type="text/javascript" src="/js/showres.js"></script>
        <script type="text/javascript" src="/js/ajax.js"></script>
        <script type="text/javascript">
            $(function() {
                $('#issue-coupon').on('change', function() {
                    var cid = $(this).val();
                    $.get('/coupon/coupon/allow-issue-list?uid=<?= $user_id ?>&cid='+cid, function(data) {
                        if (!data.code) {
                            $('.coupon-info').html('该代金券面值为'+data.data[0]['amount']+'元，最小投资金额为'+data.data[0]['minInvest']+'元。');
                        } else {
                            alert('获取代金券数据失败');
                        }
                    });
                });

                var allowClick = true;
                $('#issue').on('click', function() {
                    if (!allowClick) {
                        return;
                    }

                    if (confirm("确认发放该券给用户?")) {
                        var form = $('#form');
                        allowClick = false;
                        var xhr = $.get(form.attr('action'), form.serialize(), function (data) {
                            newalert(!data.code, data.message);
                            parent.location.reload();
                            allowClick = true;
                        });

                        xhr.fail(function () {
                            allowClick = true;
                        });
                    }
                });
            })
        </script>
    </head>

    <body class="page-header-fixed page-full-width">
        <div class="page-container row-fluid">
            <div class="page-content">
                <div>&nbsp;</div>
                <div class="form-horizontal form-view">
                    <form action="/coupon/coupon/issue-for-user" method="get" id="form">
                    <div class="control-group">
                        <label class="control-label">可发代金券</label>
                        <div class="controls">
                            <input type="hidden" name="uid" value="<?= $user_id ?>">
                            <select name="cid" id="issue-coupon">
                                <option value="">--请选择--</option>
                                <?php foreach($model as $val) : ?>
                                    <option value="<?= $val->id ?>"><?= $val->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group">
                            <div class="controls">
                                <span class="text notice coupon-info"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn green" id="issue">发放</button>
                        <button type="button" class="btn green" onclick="closewin()">关闭窗口</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>

