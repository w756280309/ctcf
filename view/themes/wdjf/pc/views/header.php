<?php

use common\utils\StringUtils;

$this->registerCssFile(ASSETS_BASE_URI.'css/header.css', ['depends' => 'frontend\assets\FrontAsset']);
$action = Yii::$app->controller->action->getUniqueId();
$fromNb = \common\models\affiliation\Affiliator::isFromNb(Yii::$app->request);
?>

<div id="header-top-box">
    <div class="header-top-box">
        <div class="header-top-left">
            <span><a href="/site/appdownload">手机客户端</a></span><i>|</i><span>客服热线：<?= Yii::$app->params['platform_info.contact_tel'] ?> (8:30-20:00)</span>
        </div>
        <?php if (Yii::$app->user->isGuest) { ?>
            <div class="header-top-right" id="header-before-login">
                <a href="/site/signup">注册</a><i>|</i><a href="/site/login">登录</a>
            </div>
        <?php } else { ?>
            <div class="header-top-login" id="header-after-login">
                <div class="header-top-name">
                    <a href="/user/user" id="header-user-name">我的账户：<?= StringUtils::obfsMobileNumber(Yii::$app->user->identity->mobile) ?></a>
                </div>
                <i>|</i>
                <span>
                    <a class="header-top-back" href="javascript:void(0)" onclick="if(!$(this).hasClass('logout')){$(this).addClass('logout');$('#header-logout').submit();}">安全退出</a>
                    <form method="post" id="header-logout" action="/site/logout">
                        <input name="_csrf" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
                    </form>
                </span>
            </div>
        <?php } ?>
    </div>
</div>
<!--nav start-->
<div id="header-nav-box">
    <div class="header-nav-box">
        <a href="/">
            <div class="header-logo">
                <?php if ($fromNb) { ?>
                    <img src="<?= ASSETS_BASE_URI ?>images/logo_wdjf_nb.png?v=<?= random_int(1, 1000)?>" alt="">
                <?php } else {?>
                    <img src="<?= ASSETS_BASE_URI ?>images/logo.png?v=<?= random_int(1, 1000)?>" alt="">
                <?php }?>
            </div>
        </a>
        <div class="header-nav">
            <ul>
                <li><a class="<?= 'site/index' === $action ? 'header-nav-click' : '' ?>" href="/">首页</a></li>
                <li>
                    <a class="<?= 'licai/index' === $action || 'licai/notes' === $action || 'licai/loan' === $action ? 'header-nav-click' : '' ?>"
                       href="/licai/loan">我要理财</a>
                </li>
                <li><a class="<?= 'safeguard/index' === $action ? 'header-nav-click' : '' ?>" href="/safeguard/">安全保障</a></li>
                <li><a class="<?= 'helpcenter/operation' === $action ? 'header-nav-click' : '' ?>" href="/helpcenter/operation/">帮助中心</a></li>
                <li class="header-nav-last"><a class="<?= 'guide/index' === $action ? 'header-nav-click' : '' ?>" href="/guide/">新手引导</a></li>
            </ul>
        </div>
    </div>
</div>
