<?php
use backend\assets\AppAsset;
use yii\helpers\Html;


/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>

<?php $this->beginBody() ?>
    <?php if (isset($this->blocks['blockmain'])): ?>
      <?= $this->blocks['blockmain'] ?>
    <?php else: ?>
       <h1>欢迎使用后台管理系统</h1>
    <?php endif; ?>
<?php $this->endBody() ?>
       
<?php $this->endPage() ?>
