<?php
$this->title = '我的银行卡';

$this->registerCssFile('/css/useraccount/bindcardalready.css');
$this->registerCssFile('/css/useraccount/chargedeposit.css');

use common\models\bank\BankCardUpdate;
?>

<div class="bindCard-box">
    <div class="bindCard-header">
        <div class="bindCard-header-icon"></div>
        <span class="bindCard-header-font">我的银行卡</span>
    </div>
    <div class="bindCard-content">
        <?php if ($user_bank) { ?>
            <p class="bindCard-content-header">已绑定银行卡：</p>
            <div class="bindCard-single">
                <span class="single-left">持卡人</span>
                <span class="single-right"><?= $user_bank->account ?></span>
            </div>
            <!------------------已绑卡开始------------------>
            <div class="bindCard-already">
                <span class="single-left">银行卡</span>
                <div class="single-div no-pointer">
                    <span class="single-icon"><img class="single-icon-img" height="18" src="<?= ASSETS_BASE_URI ?>images/useraccount/bankicon/<?= $user_bank->bank_id ?>.png" alt=""></span>
                    <span class="single-name"><?= $user_bank->bank_name ?></span>
                    <span class="single-number">尾号<?= $user_bank->card_number?substr($user_bank->card_number, -4):"" ?></span>
                </div>
                <?php if ($bankcardUpdate && BankCardUpdate::STATUS_ACCEPT === $bankcardUpdate->status) { ?>
                    <a href="javascript:void(0)" class="link-changeCard">换卡申请中</a>
                <?php } else { ?>
                    <a href="/user/userbank/updatecard" class="link-changeCard">申请更换银行卡</a>
                <?php } ?>
                <div class="clear"></div>
                <div class="link-en">
                    <a href="/user/recharge/init" class="link-charge">充值</a>
                    <a href="/user/draw/tixian" class="link-withdraw">提现</a>
                </div>
            </div>
            <!------------------已绑卡结束------------------>
        <?php } elseif ($binding) { ?>
            <p class="bindCard-content-header">绑卡处理中：</p>
            <div class="bindCard-single">
                <span class="single-left">持卡人</span>
                <span class="single-right"><?= $binding->account ?></span>
            </div>
            <div class="bindCard-already">
                <span class="single-left">银行卡</span>
                <div class="single-div no-pointer">
                    <span class="single-icon"><img class="single-icon-img" height="18" src="<?= ASSETS_BASE_URI ?>images/useraccount/bankicon/<?= $binding->bank_id ?>.png" alt=""></span>
                    <span class="single-name"><?= $binding->bank_name ?></span>
                    <span class="single-number">尾号<?= $binding->card_number?substr($binding->card_number, -4):"" ?></span>
                </div>
                <a href="javascript:void(0)" class="link-changeCard">绑卡处理中</a>
                <div class="clear"></div>
                <div class="link-en">
                </div>
            </div>
        <?php } else { ?>
            <p class="bindCard-content-header">未绑定银行卡：</p>
            <!------------------未绑卡开始------------------>
            <div class="bindCard-yet">
                <span class="single-left">银行卡</span>
                <a class="single-div">
                    <span class="add-icon"></span>
                    <span class="link-font" onclick="goBinding()">点击绑定银行卡</span>
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
<script>
    var m = <?= intval($data['code'])?>;
    if (m == 1) {
        mianmi();
    }

    function goBinding()
    {
        var code = <?= $data['code'] ?>;

        if (code) {
            location.reload();
        } else {
            location.href = "/user/userbank/bindbank";
        }
    }
</script>
