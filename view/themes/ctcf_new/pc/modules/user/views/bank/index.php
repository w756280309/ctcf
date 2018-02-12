<?php
$this->title = '绑定银行卡';

$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/useraccount/bindcard.css?v=1.2', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'ctcf/js/useraccount/bindcard.js?v=20170111', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/useraccount/chargedeposit.css');
?>

<div class="bindCard-box">
    <div class="bindCard-header">
        <div class="bindCard-header-icon"></div>
        <!--绑卡换卡标题后台控制-->
        <span class="bindCard-header-font">绑定银行卡</span>
    </div>
    <div class="bind-middle">
        <div class="bind-limit"><img src="<?= ASSETS_BASE_URI ?>images/useraccount/tip-red.png" alt=""><a href="/user/userbank/xiane" target="_blank">限额提醒</a></div>
        <div class="clearfix">
            <div class="bind-kaihu">开户行</div>
            <!--选择银行卡-->
            <div class="bind-check">
                <a href="javascript:;">请选择</a>
            </div>
            <!--银行卡-->
            <div class="bind-card">
                <div class="bind-icon"><img class="single-icon-img" height="18" src="" alt=""></div>
                <div class="bind-bank"></div>
            </div>
        </div>

        <form id="form" method="post" action="/user/bank/verify">
        <div class="bind-info">
            <div class="bind-kaihu">卡号</div>
            <div class="bind-input">
                <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                <input id="card_no" type="tel" name='QpayBinding[card_number]' placeholder="请输入银行卡号">
                <input id="bank_id" type="hidden" name='QpayBinding[bank_id]'>
                <input id="bank_name" type="hidden" name='QpayBinding[bank_name]'>
                <br>
                <p class="error-ins error" id="error"></p>
                <p class="error-ins">提示：目前仅支持添加一张储蓄卡，将作为唯一充值、提现银行卡</p>
            </div>
        </div>
        <input class="bind-btn" type="submit" value="绑定">
        </form>
    </div>
    <div class="charge-explain">
        <p class="charge-explain-title">温馨提示：</p>
        <p class="charge-explain-content">1、绑定银行卡为快捷卡，绑定后将默认为快捷充值卡和取现卡，如需修改，可申请更换银行卡；</p>
        <p class="charge-explain-content">2、暂不支持设置多张快捷支付卡；</p>
        <p class="charge-explain-content">3、绑定的银行卡必须为本人身份证办理；</p>
        <p class="charge-explain-content">4、绑定快捷卡后，不影响使用本人其他银行卡或他人银行卡代充值。</p>
    </div>
</div>
<!--选择银行卡-->
<div class="bank-mark"></div>
<div class="bankIcon-box">
    <h3 class="bankIcon-top">选择银行 <img class="close" src="<?= ASSETS_BASE_URI ?>images/login/close.png" alt=""></h3>
    <ul class="clearfix bankIcon-inner">
        <?php foreach ($banklist as $val) : ?>
            <li class="bank-li-box" data-img="<?= $val->bankId ?>" data-bank="<?= $val->bank->bankName ?>"><img src="<?= ASSETS_BASE_URI ?>images/banks/<?= $val->bankId ?>.jpg" alt=""></li>
        <?php endforeach; ?>
    </ul>
    <div class="bankIcon-btn">确定</div>
</div>
<script>
    $('.mycard').addClass('selected');
    var m = <?= $data['code'] ?>;
    if (m == 1) {
        mianmi();
    }

    $(function () {
        $.get('/ctcf/user/get-card-number', function (data) {
            $("#card_no").val(data.cardNumber);
            $("#card_no").trigger('blur');
        })
    })
</script>