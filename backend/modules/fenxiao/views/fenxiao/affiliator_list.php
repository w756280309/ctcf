<?php
$this->title = '修改投资用户分销商';

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
                var allowClick = true;
                $('#edit_aff').on('click', function() {
                    if (!allowClick) {
                        return;
                    }

                    if (confirm("确认修改用户的分销商吗?")) {
                        var affId = $('.data-aff').attr('data-aff');
                        var selId = $('.data-aff select').val();
                        if (affId === selId) {
                            newalert(1);
                            parent.location.reload();
                        }

                        var form = $('#form');
                        allowClick = false;
                        var xhr = $.post(form.attr('action'), form.serialize(), function (data) {
                            newalert(!data.code, data.message);
                            parent.location.reload();
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
                    <form action="/fenxiao/fenxiao/edit-for-user" method="post" id="form">
                        <div class="control-group">
                            <label class="control-label">分销商</label>
                            <?php $userAffId = $userAff ? $userAff->affiliator_id : ''; ?>
                            <div class="controls data-aff" data-aff="<?= $userAffId ?>">
                                <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                                <input type="hidden" name="uid" value="<?= $user_id ?>">
                                <select name="aff_id">
                                    <option value="">官方</option>
                                    <?php foreach($affiliators as $affiliator) : ?>
                                        <option value="<?= $affiliator['id'] ?>" <?= $userAffId === (int) $affiliator['id'] ? 'selected' : '' ?>><?= $affiliator['name'] ?>（<?= $affiliator['trackCode'] ?>）</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn green" id="edit_aff">修改</button>
                            <button type="button" class="btn green" onclick="closewin()">关闭窗口</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
