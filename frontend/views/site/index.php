<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
    <body>
        <?php $this->beginBody() ?>
        <?php if (Yii::$app->user->isGuest) { ?>
        <a href="/site/login">登陆</a>
        <?php } else { ?>
        <a href="/user/useraccount/accountcenter"><?php echo "ID:".Yii::$app->user->id ?></a>
        <a href="/site/logout">注销</a>
        <?php } ?>
        <?php $this->endBody() ?>

    </body>
</html>
<?php $this->endPage() ?>
