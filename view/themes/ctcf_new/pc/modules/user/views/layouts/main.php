<?php
$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/useraccount/usercenter.css?v=1.1', ['depends' => 'frontend\assets\FrontAsset']);
$action = Yii::$app->controller->action->getUniqueId();
?>
<?php $this->beginContent('@frontend/views/layouts/main.php'); ?>
<div class="wdjf-body">
    <div class="wdjf-ucenter clearfix">
        <div class="leftmenu">
            <?= $this->renderFile('@frontend/views/left.php') ?>
        </div>
        <div class="rightcontent">
            <?= $content ?>
        </div>
        <div class="clear"></div>
    </div>
</div>
<?php $this->endContent(); ?>

