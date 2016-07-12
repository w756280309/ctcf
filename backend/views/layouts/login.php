<?php

use yii\helpers\Html;
?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="shortcut icon" href="/images/favicon.ico" type="imagend.microsoft.icon">
    <title><?= Html::encode($this->title) ?></title>
    <?= Html::csrfMetaTags() ?>

    <?php $this->head() ?>
    <?php if (isset($this->blocks['blockhead'])): ?>
        <?= $this->blocks['blockhead'] ?>
    <?php endif; ?>

</head>

<body>
<?php $this->beginBody() ?>
    <?php if (isset($this->blocks['blockmain'])): ?>
        <?= $this->blocks['blockmain'] ?>
    <?php else: ?>
       <h1>Welcome!!!</h1>
    <?php endif; ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
