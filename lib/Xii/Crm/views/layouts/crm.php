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

$menuItems = [
    ['label' => '客户', 'url' => ['/crm/account'], 'active' => $controllerUniqId === 'crm/account'],
];

echo Nav::widget([
    'options' => ['class' => 'navbar-nav'],
    'items' => $menuItems,
]);
?>
<ul class="nav navbar-nav navbar-right">
    <li class="dropdown">
        <a href="#" data-toggle="dropdown" class="dropdown-toggle">
            <span class="glyphicon glyphicon-plus"></span> 添加 <b class="caret"></b>
        </a>
<?php
echo Dropdown::widget([
    'items' => [
        ['label' => '潜客', 'url' => ['/crm/identity/create']],
    ],
]);
?>
    </li>
</ul>
<?php
NavBar::end();
?>
<div class="container">
    <div class="row">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            'homeLink' => ['label' => '首页', 'url' => '/crm/account'],
        ]) ?>
    </div>
    <?= $content ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
