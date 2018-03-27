<?php

use yii\helpers\Html;

$this->title="绑定银行卡";
$this->showViewport = false;

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=20170906">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/tie-card/step.css?v=20170907">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>libs/iscroll.js"></script>
<script src="<?= FE_BASE_URI ?>wap/common/js/pop.js?v=2"></script>
<script src="<?= ASSETS_BASE_URI ?>js/qpay.js?v=20170306"></script>

<div class="flex-content">
    <?php if (!defined('IN_APP')) { ?>
        <div class="topTitle f18">
            <img class="goback" src="<?= FE_BASE_URI ?>wap/tie-card/img/back.png?v=1" alt="" onclick="window.location.href='/user/user'">
            <div><?= Html::encode($this->title) ?></div>
            <a class="rg" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><img src="<?= FE_BASE_URI ?>wap/tie-card/img/phone.png" alt=""></a>
        </div>
    <?php } ?>

    <div class="stepShow">
        <ul class="clearfix stepNum">
            <li class="lf f15 noBorder"><img class="stepok" src="<?= ASSETS_BASE_URI ?>ctcf/images/guide-realName/checked.png" alt=""></li>
            <li class="lf line"></li>
            <li class="lf f15 noBorder"><img class="stepok" src="<?= ASSETS_BASE_URI ?>ctcf/images/guide-realName/checked.png" alt=""></li>
            <li class="lf line"></li>
            <li class="lf f15 stepNow">3</li>
        </ul>
        <ul class="clearfix f12 stepIntro">
            <li class="lf">开通资金托管账户</li>
            <li>开通免密服务</li>
            <li class="rg">绑定银行卡</li>
        </ul>
    </div>

    <form method="post" class="cmxform" id="form" action="/user/bank/verify" data-to="1">
        <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <div class="name clearfix" style="position: relative;">
            <label for="bank_id" class="f15 lf">开户银行</label>
            <img id="bank_id_img" class="lf" src="" alt="">
            <input type="text" id="bank_show" class="lf f14" placeholder="请选择开户银行" readonly>
            <input id="bank_id" type="hidden" name="QpayBinding[bank_id]">
            <input id="bank_name" type="hidden" name="QpayBinding[bank_name]">
            <img class="tieCard" src="<?= FE_BASE_URI ?>wap/tie-card/img/jiantou.png" style="position: absolute;top: 0.4rem;right: 0; width: 0.506667rem;" alt="">
        </div>
        <div class="creidtCard clearfix">
            <label for="card_no" class="f15 lf">储蓄卡号</label>
            <input type="tel" id="card_no" name="QpayBinding[card_number]" onkeyup="this.value=this.value.replace(/\D/g,'');" class="lf f14" placeholder="请输入银行储蓄卡卡号" autocomplete="off">
        </div>
    </form>
    <p class="f13 limitMoney">
        <span class="lf" style="color: #ff6707">目前仅支持添加一张银行卡</span>
        <a href="/user/userbank/bankxiane"><img src="<?= FE_BASE_URI ?>wap/tie-card/img/icon_02.png" alt=""> 限额提醒</a>
    </p>
    <p id="bankRechargeRefer" class="f13" style="padding-left: 0.53333333rem;padding-right: 0.53333333rem;color: #ff6058"></p>

    <a class="instant instantSpecial f18" href="javascript:void(0)" id="bind_btn">绑 卡</a>

    <div class="f12">
        <p class="tipsCtn">*绑定的银行卡必须为本人身份证办理</p>
        <p class="tipsCtn">*绑定的银行卡将作为唯一充值\提现卡</p>
        <p class="tipsCtn">*同卡进出防止盗刷风险</p>
    </div>


    <div class="mask closePomp"></div>
    <div class="pomp f14">
        <img class="bgImg" src="<?= FE_BASE_URI ?>wap/tie-card/img/mask.png" alt="">
        <img class="closePomp" src="<?= FE_BASE_URI ?>wap/tie-card/img/close.png" alt="">
        <div id="wrapper">
            <ul>
                <?php foreach($banklist as $bank): ?>
                    <li disableRecharge="<?= $bank->isDisabled ?>" data="<?= $bank->bankId ?>"><img src="<?= ASSETS_BASE_URI ?>images/bankicon/<?= $bank->bankId ?>.png" alt="" style="padding-right:10px"><?= $bank->bank->bankName ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<!-- 绑定提示 start  -->
<div id="qpay-binding-confirm-mask" class="mask"></div>
<div id="qpay-binding-confirm-diag" class="bing-info">
    <div class="bing-tishi">提示</div>
    <p>绑定的银行卡将作为唯一充值，提现银行卡</p>
    <div class="bind-btn">
        <span class="bind-xian x-cancel">取消</span>
        <span class="x-confirm">确定</span>
    </div>
</div>
<!-- 绑定提示 end  -->

<script>
    var csrf;
    $(function() {
        FastClick.attach(document.body);

        $.get('/ctcf/user/get-card-number', function (data) {
            $("#card_no").val(data.cardNumber);
            $("#card_no").trigger('blur');
        })

        var err = '<?= $data['code'] ?>';
        var mess = '<?= $data['message'] ?>';
        var tourl = '<?= $data['tourl'] ?>';
        if ('1' === err) {
            toastCenter(mess, function() {
                if ('' !== tourl) {
                    location.href = tourl;
                }
            });
            return;
        }

        $('.closePomp').on('click',function(e) {
            e.stopPropagation();
            $('.closePomp').hide();
            $('.pomp').hide();
        });

        var myScroll = new iScroll('wrapper', {
            vScrollbar:false,
            hScrollbar:false
        });

        $("#bank_show,.tieCard").on("click",function() {
            $('.closePomp').show();
            $('.pomp').show();
            myScroll.refresh();//点击后初始化iscroll
        });

        $("#wrapper ul li").on("click",function() {
            $("#bank_id_img").attr({"src":$(this).children()[0].src}).show();
            $("#bank_show").val($(this).text()).addClass("changeWidth");
            var selectedBankId = $(this).attr('data');
            var selectedBankName = $(this).text();
            $("#bank_id").val(selectedBankId);
            $("#bank_name").val(selectedBankName);
            $('.closePomp').hide();
            $('.pomp').hide();
            refer(selectedBankId, selectedBankName);
        });

        csrf = $("meta[name=csrf-token]").attr('content');
        $('#bind_btn').on('click', function(e) {
            e.preventDefault();

            if (validateBinding()) {
                qpay_showConfirmModal();
            }
        });

        $('#card_no').blur(function() {
            var card_no = $(this).val();
            if(card_no === '') {
                return false;
            }

            $.post("/user/bank/check", {card: card_no, _csrf:csrf}, function (data) {
                if('' !== data.bank_id && '' !== data.bank_name) {
                    $("#bank_id_img").attr({"src": '<?= ASSETS_BASE_URI ?>images/bankicon/'+data.bank_id+'.png'}).show();
                    $("#bank_show").val(' '+data.bank_name).addClass("changeWidth");
                    $('#bank_id').val(data.bank_id);
                    $('#bank_name').val(data.bank_name);
                }
                refer(data.bank_id, data.bank_name);
                if(data.code) {
                    toastCenter(data.message);
                    return;
                }
            });
        });
    })

    //是否支持绑卡但不支持快捷充值提示
    function refer(bankId, bankName) {
        if ('' === bankId) {
            $('#bankRechargeRefer').html('');
            return false;
        }
        var isDisabledRecharge = $("#wrapper ul").find("li[data='"+bankId+"']").attr('disableRecharge');
        if ('1' !== isDisabledRecharge) {
            $('#bankRechargeRefer').html('');
            return false;
        }
        $('#bankRechargeRefer').html('尊敬的用户，由于'+bankName+'银行系统改造，绑定后仅可用于到账提现，也可以正常的PC端网银充值，但是此卡不提供手机（快捷）充值服务。');
    }
</script>
