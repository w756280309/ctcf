<?php
\frontend\assets\FrontAsset::register($this);
$this->registerJsFile(ASSETS_BASE_URI . 'js/useraccount/deposit.js');
$this->registerCssFile(ASSETS_BASE_URI . 'css/useraccount/usercenter.css');
$this->registerCssFile(ASSETS_BASE_URI . 'css/useraccount/chargedeposit.css');
?>
<div class="deposit-box">
    <div class="deposit-header">
        <div class="deposit-header-icon"></div>
        <span class="deposit-header-font">开户</span>
        <div class="clear"></div>
    </div>
    <div class="deposit-content">
        <div class="deposit-content-left">
            <div class="deposit-inflow">
                <span class="inflow-type">真实姓名</span>
                <input class="name-text" type="text">
                <div class="tip_pop">
                    <div class="tip_pop-en">
                        <div class="tip_pop-border">
                            <em></em>
                            <span></span>
                        </div>
                        <span class="tip_pop-content-icon"></span>
                        <div class="tip_pop-content name-content"></div>
                    </div>
                </div>
            </div>
            <div class="deposit-inflow">
                <span class="inflow-type">身份证号</span>
                <input class="identity-text" type="text">
                <span class="tip-error error-identity"></span>
                <div class="tip_pop">
                    <div class="tip_pop-en">
                        <div class="tip_pop-border">
                            <em></em>
                            <span></span>
                        </div>
                        <span class="tip_pop-content-icon"></span>
                        <div class="tip_pop-content identity-content"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="deposit-content-right">
            <span class="deposit-content-link">立即开通</span>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="charge-explain">
    <p class="charge-explain-title">为什么要开通第三方资金托管账户？</p>
    <div class="charge-explain-content">
        <span class="span-left">合法合规的需要：</span>
        <span class="span-right">按照监管部门要求，互联网金融平台客户资金需第三方存管。</span>
        <div class="clear"></div>
    </div>
    <div class="charge-explain-content">
        <span class="span-left">资金安全的需要：</span>
        <span class="span-right">开通第三方资金存管账户后，可避免资金挪用风险，用户完全拥有资金自主使用权，可有效保证投/融资双方资金安全。</span>
        <div class="clear"></div>
    </div>
</div>
<script>
    $(function () {
        /*点击立即开通*/
        $('.deposit-content-link').on('click', function () {
            var name = $('.name-text');
            var idcard = $('.identity-text');
            var nameisok = validate_name();
            var identityisok = validate_idcard();
            if (nameisok != false && identityisok != false) {
                $.post('/user/userbank/idcardrz', {
                    'User[real_name]': name.val(),
                    'User[idcard]': idcard.val(),
                    '_csrf': '<?= Yii::$app->request->csrfToken?>'
                }, function (data) {
                    if (data.code == 0) {
                        //成功
                        window.location.href = data.tourl;
                    } else {
                        //失败
                        var message = data.message;
                        console.log(message);
                        if (data.tourl) {
                            location.herf = data.tourl;
                        }
                    }
                });
            }
        });
    });
</script>
