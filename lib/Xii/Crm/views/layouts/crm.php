<?php
use yii\helpers\Html;
use yii\bootstrap\Dropdown;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?php
NavBar::begin([
    'brandLabel' => '温都金服CRM',
    'brandUrl' => '/crm/account',
    'options' => [
    ],
]);

$controllerUniqId = \Yii::$app->controller->getUniqueId();

$moduleMenu = [
    ['label' => '客户', 'url' => ['/crm/account'], 'active' => $controllerUniqId === 'crm/account'],
];

echo Nav::widget([
    'options' => ['class' => 'navbar-nav'],
    'items' => $moduleMenu,
]);

$shortcutMenu = [
    ['label' => '登记潜客', 'url' => ['/crm/identity/create']],
    ['label' => '登记客服记录', 'url' => ['/crm/activity/call']],
];

echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar-right'],
    'items' => $shortcutMenu,
]);

NavBar::end();
?>
<div class="container">
    <?= Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        'homeLink' => ['label' => '首页', 'url' => '/crm/account'],
    ]) ?>
    <?= $content ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
