<?php

$this->title = "安全中心";
$this->backUrl = '/system/system/setting';
use common\utils\SecurityUtils;

?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css?v=20160613">

<a class="row sm-height border-bottom margin-top block" href="/site/editpass">
    <div class="col-xs-5 safe-txt text-align-lf">修改登录密码</div>
    <div class="col-xs-5"></div>
    <div class="col-xs-1 arrow">
         <img src="<?= ASSETS_BASE_URI ?>images/arrow.png" alt="右箭头">
    </div>
    <div class="col-xs-1"></div>
</a>
<div class="clear"></div>
<a class="row sm-height border-bottom block" href="/user/userbank/editbuspass">
    <div class="col-xs-5 safe-txt text-align-lf">修改交易密码</div>
    <div class="col-xs-5"></div>
    <div class="col-xs-1 arrow">
         <img src="<?= ASSETS_BASE_URI ?>images/arrow.png" alt="右箭头">
    </div>
    <div class="col-xs-1"></div>
</a>
<div class="row sm-height border-bottom margin-top">
    <div class="col-xs-4 safe-txt  text-align-lf">实名验证</div>
    <div class="col-xs-5 safe-lf"><?= null !== $user->safeIdCard ? substr_replace(SecurityUtils::decrypt($user->safeIdCard), '***', 5, -2) : '' ?></div>
    <div class="col-xs-3 arrow">
        <?php if(!$user->idcard_status) { ?>
            <a href="/user/identity">去认证</a>
        <?php } ?>
    </div>
</div>
<?php if($user_bank) { ?>
    <a class="row sm-height border-bottom visit" style="display: block;" href="<?= null !== $user->qpay ? '/user/bank/card' : 'javascript:void(0)' ?>">
        <div class="col-xs-4 safe-txt  text-align-lf">银行卡</div>
        <div class="col-xs-2 safe-lf"><?= $user_bank->card_number ? substr_replace($user_bank->card_number, '*****', 3, -2) : '' ?></div>
        <?php if (null === $user->qpay && $user_bank) { ?>
            <div class="col-xs-5 bing-doing">正在审核中</div>
        <?php } else { ?>
            <div class="col-xs-4"></div>
            <div class="col-xs-1 arrow">
                <img src="<?= ASSETS_BASE_URI ?>images/arrow.png" alt="右箭头">
            </div>
        <?php } ?>
    </a>
<?php } else { ?>
    <div class="row sm-height border-bottom">
        <div class="col-xs-4 safe-txt  text-align-lf">银行卡</div>
        <div class="col-xs-2 safe-lf col"></div>
        <div class="col-xs-3 safe-lf"></div>
        <div class="col-xs-3 arrow">
            <a id="bind_card">去绑定</a>
        </div>
    </div>
<?php } ?>

<div class="form-bottom"></div>
<div class="row login-sign-btn">
    <div class="col-xs-3"></div>
    <div class="col-xs-6">
        <?php if ($closeWin) : ?>
            <input class="btn-common btn-normal safe-btn" type="button" value="安全退出" onclick="WeixinJSBridge.invoke('closeWindow');">
        <?php else : ?>
            <form method="post" class="cmxform" action="/site/logout">
                <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                <input class="btn-common btn-normal safe-btn" type="submit" value="安全退出">
            </form>
        <?php endif; ?>
    </div>
    <div class="col-xs-3"></div>
</div>
<script>
    $('#bind_card').click(function(){
        var xhr = $.get('/user/user/check-kuaijie', function (data) {
            if (data.code && data.tourl) {
                if (data.tourl !== '/user/bank') {
                    toastCenter(data.message, function() {
                        location.href = data.tourl;
                    });
                } else {
                    location.href = data.tourl;
                }

            }
        });

        xhr.fail(function () {
            toastCenter('系统繁忙,请稍后重试!');
        });
    })
</script>