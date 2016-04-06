<?php
$this->title="安全中心";
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css">

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
    <div class="col-xs-5 safe-lf"><?= $user->idcard?substr_replace($user->idcard,'***',5,-2):"" ?></div>
    <div class="col-xs-3 arrow">
        <?php if(!$user){ ?>
        <a href="/user/userbank/idcardrz">去认证</a>
        <?php } ?>
    </div>
</div>
<?php if($user_bank){ ?>
    <div class="row sm-height border-bottom">
        <div class="col-xs-4 safe-txt  text-align-lf">银行卡</div>
        <div class="col-xs-2 safe-lf"><?= $user_bank->card_number ? substr_replace($user_bank->card_number,'*****',3,-2) : "" ?></div>
        <?php if((int)$user_bank->status === 3) { ?>
        <div class="col-xs-5 arrow" style="text-align: right;">
                <a href="javascript:void(0);">正在审核中</a>
        </div>
        <?php } ?>
    </div>
<?php }else{ ?>
    <div class="row sm-height border-bottom">
        <div class="col-xs-4 safe-txt  text-align-lf">银行卡</div>
        <div class="col-xs-2 safe-lf col"></div>
        <div class="col-xs-3 safe-lf"></div>
        <div class="col-xs-3 arrow">
                <a href="/user/userbank/bindbank">去绑定</a>
        </div>
    </div>
<?php } ?>


<div class="form-bottom">&nbsp;</div>
<div class="row login-sign-btn">
    <div class="col-xs-3"></div>
    <div class="col-xs-6">
        <form method="post" class="cmxform" action="/site/logout">
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <input class="btn-common btn-normal" style="background: #F2F2F2;" name="signUp" type="submit" value="安全退出">
        </form>
    </div>
    <div class="col-xs-3"></div>
</div>