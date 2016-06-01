<?php
    use common\utils\StringUtils;
?>

<div>
    <div>
        手机客户端 | 客服热线: <?= Yii::$app->params['contact_tel'] ?>(9:00~20:00)
    </div>
    <div>
        <?php if (Yii::$app->user->isGuest) { ?>
            <a href="/site/signup">注册</a> |
            <a href="/site/login">登录</a>
        <?php } else { ?>
            <a href=""><?= StringUtils::obfsMobileNumber(Yii::$app->user->identity->mobile) ?></a>
            <a href="javascript:void(0)" onclick="$('#logout').submit();">安全退出</a>
            <form method="post" id="logout" action="/site/logout">
                <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            </form>
        <?php } ?>
    </div>
</div>
<div>
    <div><a href="/"><img class="logo" src="<?= ASSETS_BASE_URI ?>images/logo.png"></a></div>
    <div>
        <ul>
            <li><a href="/">首页</a></li>
            <li><a href="/licai/">理财列表</a></li>
        </ul>
    </div>
</div>
