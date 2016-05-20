<?php

use yii\helpers\Html;
use frontend\assets\AppAsset;
use common\view\BaiduTongjiHelper;

AppAsset::register($this);
BaiduTongjiHelper::registerTo($this, BaiduTongjiHelper::PC_KEY);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <link rel="stylesheet" href="<?=ASSETS_BASE_URI ?>css/index.css?v=20160405">
    </head>

    <body>
        <?php $this->beginBody() ?>

        <div class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="/">
                        大河金服
                    </a>
                </div>

                <p class="brand-sub">大额充值通道</p>

                <div class="navbar-text navbar-right">
                    <?php if (Yii::$app->user->isGuest) { ?>
                        <a class="navbar-link" href="/site/login">登录</a>
                        <a class="navbar-link" href="/site/login?flag=reg">注册</a>
                    <?php } else { ?>
                        <a class="navbar-link" href="/user/useraccount/accountcenter">我的账户</a>
                        <a class="navbar-link" href="javascript:void(0)" onclick="$('#logout').submit();">安全退出</a>
                        <form method="post" id="logout" action="/site/logout">
                            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                        </form>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div style="line-height: 60px;">&nbsp;</div>
        <?= $content ?>
        <?= $this->render('@frontend/views/site/footer.php'); ?>

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
