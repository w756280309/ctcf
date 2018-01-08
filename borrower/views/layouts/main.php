<?php

use yii\helpers\Html;
use borrower\assets\AppAsset;

AppAsset::register($this);

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>

    <body>
        <?php $this->beginBody() ?>

        <div class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="/">
                        充值专用通道
                    </a>
                </div>

                <p class="brand-sub">企业入口</p>

                <div class="navbar-text navbar-right">
                    <?php if (Yii::$app->user->isGuest) { ?>
                        <a class="navbar-link" href="/site/login">登录</a>
                    <?php } else { ?>
                        <a class="navbar-link" href="/user/useraccount/accountcenter"><?= Yii::$app->user->getIdentity()->org_name ?></a>
                        <a class="navbar-link" href="javascript:void(0)" onclick="$('#logout').submit();">退出登录</a>
                        <form method="post" id="logout" action="/site/logout">
                            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                        </form>
                    <?php } ?>
                </div>
            </div>
        </div>

        <?= $content ?>

        <div class="footer">
            <?= Yii::$app->params['page_info']['beian'] ?>
        </div>

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
