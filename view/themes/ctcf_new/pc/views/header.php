<?php

use common\utils\StringUtils;

$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/header.min.css', ['depends' => 'frontend\assets\CtcfFrontAsset']);
$action = Yii::$app->controller->action->getUniqueId();
$fromNb = \common\models\affiliation\Affiliator::isFromNb(Yii::$app->request);
$old_site_visible_user_id = explode(',', Yii::$app->params['old_site_visible_user_id']);
?>

<div class="ctcf-header">
    <div class="top-bar clear-fix">
        <div class="lf f14">
            <span class="header-hover mgr20 pointer erweima">
                <img class="mgr7" src="<?= ASSETS_BASE_URI ?>ctcf/images/icon_01.png" alt="">
                <i>官方微信</i>
                <img style="width: 98px" class="hidden pst" src="<?= ASSETS_BASE_URI ?>ctcf/images/weixin_follow.png" alt="">
            </span>
<!--            <a style="color:#ff6707;" href="http://legacy.hbctcf.com/account/allAssets">老站提现入口</a>-->
           <!-- <span class="header-hover pointer erweima">
                <img class="mgr7" src="<?/*= ASSETS_BASE_URI */?>ctcf/images/icon_02.png" alt="">
                <i>官方微博</i>
                <img class="hidden pst" src="<?/*= ASSETS_BASE_URI */?>ctcf/images/weibo@2x.png" alt="">
            </span>-->
        </div>
        <!--未登录-->
        <?php if (Yii::$app->user->isGuest) { ?>
            <div class="rg fz14">
                <ul class="clear-fix">
                    <li class="lf"><a class="pd15 border-r1 header-hover" href="/site/signup">免费注册</a></li>
                    <li class="lf"><a class="pd15 border-r1 header-hover" href="/site/login">登录</a></li>
                    <li class="lf">
                        <a class="pd15 border-r1 header-hover" href="javascript:void(0);">
                        <span class="erweima">
                            <img class="mgr7" src="<?= ASSETS_BASE_URI ?>ctcf/images/icon_03.png" alt="">下载APP<img style="width: 98px;" class="hidden pst-spec" src="<?= ASSETS_BASE_URI ?>ctcf/images/app_download.png" alt="">
                        </span>
                        </a>
                    </li>
                    <li class="lf"><a class="pd15 border-r1 header-hover" href="/helpcenter/operation/">帮助</a></li>
                    <li class="lf header-hover pdl15">客服热线：<?= Yii::$app->params['platform_info.contact_tel'] ?></li>
                </ul>
            </div>
        <!--已登录-->
        <?php } else { ?>
            <div class="rg fz14">
                <ul class="clear-fix">
                    <?php if (in_array(Yii::$app->user->id, $old_site_visible_user_id)) { ?>
                        <li class="lf"><a class="pd15 border-r1 header-hover" style="color:#ff6707;font-weight: bold" href="http://legacy.hbctcf.com/account/allAssets"  target="_blank">老站提现入口</a></li>
                    <?php } ?>
                    <li class="lf"><a class="header-hover" href="/user/user" style="margin-left:15px">您好！<?= StringUtils::obfsMobileNumber(Yii::$app->user->identity->mobile) ?></a></li>
                    <li class="lf">
                        <a class="pd15 border-r1 header-hover" href="javascript:void(0)" onclick="if(!$(this).hasClass('logout')){$(this).addClass('logout');$('#header-logout').submit();}">退出登录</a>
                        <form method="post" id="header-logout" action="/site/logout">
                            <input name="_csrf" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
                        </form>
                    </li>
                   <!-- <li class="lf">
                        <a class="pd15 border-r1 header-hover" href="javascript:void(0);">
                        <span class="erweima">
                            <img class="mgr7" src="<?/*= ASSETS_BASE_URI */?>ctcf/images/icon_03.png" alt="">下载APP<img class="hidden pst-spec" src="<?/*= ASSETS_BASE_URI */?>ctcf/images/weixin@2x.png" alt="">
                        </span>
                        </a>
                    </li>-->
                    <li class="lf"><a class="pd15 border-r1 header-hover" href="/helpcenter/operation/">帮助</a></li>
                    <li class="lf header-hover pdl15">客服热线：<?= Yii::$app->params['platform_info.contact_tel'] ?></li>
                </ul>
            </div>
        <?php } ?>
    </div>

</div>
<div class="ctcf-nav">
    <div class="nav-bar clear-fix">
        <div class="lf"><a href="/"><img style="width: 368px;" src="<?= ASSETS_BASE_URI ?>ctcf/images/logo.png" alt=""></a></div>
        <div class="rg fz16">
            <ul class="clear-fix">
                <li class="lf mgr18"><a class="<?= 'site/index' === $action ? 'nav-active' : '' ?>" href="/">首页</a></li>
                <li class="lf mgr18"><a class="<?= 'licai/index' === $action || 'licai/notes' === $action ? 'nav-active' : '' ?>" href="/licai/">我要理财</a></li>
                <li class="lf mgr18"><a class="<?= 'safeguard/index' === $action ? 'nav-active' : '' ?>" href="/safeguard/">安全保障</a></li>
                <li class="lf mgr18"><a class="<?= 'helpcenter/operation' === $action ? 'nav-active' : '' ?>" href="/helpcenter/operation/">帮助中心</a></li>
                <li class="lf"><a class="<?= 'guide/index' === $action ? 'nav-active' : '' ?>" href="/guide/">新手引导</a></li>
            </ul>
        </div>
    </div>
</div>
