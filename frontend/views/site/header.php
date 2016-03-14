<div id="top-box">
    <div class="top-box">
        <a href="/"><img class="logo" src="/images/logo.png"/></a>
        <div class="top-right">
            <?php if (Yii::$app->user->isGuest) { ?>
            <div class="top-resign"><a href="/site/login?flag=reg">注册</a></div>
            <div class="top-login"><a href="/site/login">登录</a><span>大额充值通道</span></div>
            <?php } else { ?>
            <div class="top-resign"><a href="/user/useraccount/accountcenter">我的账户</a></div>
            <div class="top-login"><a href="javascript:void(0)" onclick="$('#logout').submit();">安全退出</a><span>大额充值通道</span></div>
            <form method="post" id="logout" action="/site/logout">
                <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            </form>
            <?php } ?>
        </div>
    </div>
</div>