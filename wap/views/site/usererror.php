<?php
$this->title="账户异常提醒";

?>

<link rel="stylesheet" href="/css/setting.css">

<div class="row flase-box">
    <div class="col-xs-12 text-align-ct">
        <img src="<?= ASSETS_BASE_URI ?>images/false.png" class="false-img" alt="失败">
    </div>
    <div class="col-xs-12 text-align-ct false-txt">当前用户已被冻结</div>
    <div class="col-xs-12 text-align-ct bg-height">客服联系电话：400-888-6888</div>
</div>
<div class="row login-sign-btn">
    <div class="col-xs-3"></div>
    <div class="col-xs-6 text-align-ct">
        <form method="post" class="cmxform" action="/site/logout">
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <input id="" class="btn-common btn-normal" name="" type="submit" value="回到首页">
        </form>
    </div>
    <div class="col-xs-3"></div>
</div>
