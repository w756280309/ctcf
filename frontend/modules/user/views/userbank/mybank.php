<?php
    $this->title = '我的银行卡';
    \frontend\assets\FrontAsset::register($this);
    $this->registerCssFile('/css/UserAccount/bindCardAlready.css');
?>
<div class="bindCard-box">
    <div class="bindCard-header">
        <div class="bindCard-header-icon"></div>
        <span class="bindCard-header-font">我的银行卡</span>
    </div>
    <div class="bindCard-content">
        <p class="bindCard-content-header">已绑定银行卡：</p>
        <div class="bindCard-single">
            <span class="single-left">持卡人</span>
            <span class="single-right"><?= $user_bank->account ?></span>
        </div>
        <!------------------已绑卡开始------------------>
        <?php if($user_bank) { ?>
            <div class="bindCard-already">
                <span class="single-left">银行卡</span>
                <div class="single-div no-pointer">
                    <span class="single-icon"><img class="single-icon-img" height="18" src="<?= ASSETS_BASE_URI ?>images/UserAccount/bankIcon/<?= $user_bank->bank_id ?>.png" alt=""></span>
                    <span class="single-name"><?= $user_bank->bank_name ?></span>
                    <span class="single-number">尾号<?= $user_bank->card_number?substr($user_bank->card_number, -4):"" ?></span>
                </div>
                <a href="" class="link-changeCard">申请更换银行卡</a>
                <div class="clear"></div>
                <div class="link-en">
                    <a href="" class="link-charge">充值</a>
                    <a href="" class="link-withdraw">提现</a>
                </div>
            </div>
            <!------------------已绑卡结束------------------>
        <?php } else { ?>
            <!------------------未绑卡开始------------------>
            <div class="bindCard-yet">
                <span class="single-left">银行卡</span>
                <a class="single-div">
                    <span class="add-icon"></span>
                    <span class="link-font">点击绑定银行卡</span>
                </a>
                <a class="clear"></a>
            </div>
            <!------------------未绑卡结束------------------>
        <?php }?>
        <div class="clear"></div>
    </div>
    <div class="charge-explain">
        <p class="charge-explain-title">温馨提示：</p>
        <p class="charge-explain-content">1、绑定银行卡为快捷卡，绑定后将默认为快捷充值卡和取现卡，如需修改，可申请更换银行卡；</p>
        <p class="charge-explain-content">2、暂不支持设置多张快捷支付卡；</p>
        <p class="charge-explain-content">3、绑定的银行卡必须为本人身份证办理；</p>
        <p class="charge-explain-content">4、绑定快捷卡后，不影响使用本人其他银行卡或他人银行卡代充值。</p>
    </div>
</div>
