<?php
use common\utils\StringUtils;

$this->registerCssFile(ASSETS_BASE_URI.'css/header.css', ['depends' => 'frontend\assets\FrontAsset']);
?>

<div id="top-box">
    <div class="top-box">
        <div class="top-left">
            <span><a href="http://a.app.qq.com/o/simple.jsp?pkgname=com.wz.wenjf">手机客户端</a></span><i>|</i><span>客服热线：<?= Yii::$app->params['contact_tel'] ?>(9:00~20:00)</span>
        </div>
        <?php if (Yii::$app->user->isGuest) { ?>
            <div class="top-right" id="before-login">
                <a href="/site/signup">注册</a><i>|</i><a href="/site/login">登录</a>
            </div>
        <?php } else { ?>
            <div class="top-login" id="after-login">
                <div class="top-name">
                    <a href="/user/user" id="user-name"><?= StringUtils::obfsMobileNumber(Yii::$app->user->identity->mobile) ?></a>
                </div>
                <i>|</i>
                <span>
                    <a class="top-back" href="javascript:void(0)" onclick="$('#logout').submit();">安全退出</a>
                    <form method="post" id="logout" action="/site/logout">
                        <input name="_csrf" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
                    </form>
                </span>
            </div>
        <?php } ?>
    </div>
</div>
<!--nav start-->
<div id="nav-box">
    <div class="nav-box">
        <a href="/">
            <div class="logo">
                <img src="<?= ASSETS_BASE_URI ?>images/logo.png" alt="">
            </div>
        </a>
        <div class="nav">
            <ul>
                <li><a class="<?= 'site/index' === Yii::$app->controller->action->getUniqueId() ? 'nav-click' : '' ?>" href="/">首页</a></li>
                <li><a class="<?= 'licai/index' === Yii::$app->controller->action->getUniqueId() ? 'nav-click' : '' ?>" href="/licai/">我要理财</a></li>
                <li><a>安全保障</a></li>
                <li><a>帮助中心</a></li>
                <li class="nav-last"><a href="">新手引导</a></li>
            </ul>
        </div>
    </div>
</div>