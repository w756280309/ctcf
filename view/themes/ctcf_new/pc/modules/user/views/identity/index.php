<?php
$this->registerJsFile(ASSETS_BASE_URI . 'js/useraccount/deposit.js');
$this->registerCssFile(ASSETS_BASE_URI . 'ctcf/css/useraccount/chargedeposit.css?v=1.2');
$this->title = '开户';
?>

<div class="deposit-box">
    <div class="deposit-header">
        <div class="deposit-header-icon"></div>
        <span class="deposit-header-font">开户</span>
        <div class="clear"></div>
    </div>
    <div class="deposit-content">
        <div class="deposit-content-left">
            <div class="deposit-inflow name">
                <span class="inflow-type">真实姓名</span>
                <input class="name-text" type="text" id="real_name">
                <p class="err-info"></p>
            </div>
            <div class="deposit-inflow identity">
                <span class="inflow-type">身份证号</span>
                <input class="identity-text" type="text" maxlength="18" id="idcard">
                <p class="err-info"></p>
            </div>
        </div>
        <div class="deposit-content-right">
            <span class="deposit-content-link" style="cursor: pointer">立即开通</span>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="charge-explain">
    <p class="charge-explain-title">为什么要开通第三方支付账户？</p>
    <div class="charge-explain-content">
        <span class="span-left">合法合规的需要：</span>
        <span class="span-right">楚天财富接入了联动优势电子商务有限公司的第三方账户体系，实现了平台对个人及企业用户的账户进行独立管理，交易仅限用户本人操作，资金安全有保障。</span>
        <div class="clear"></div>
    </div>
    <div class="charge-explain-content">
        <span class="span-left">资金安全的需要：</span>
        <span class="span-right">开通第三方支付账户后，可避免资金挪用风险，用户完全拥有资金自主使用权，可有效保证投/融资双方资金安全。</span>
        <div class="clear"></div>
    </div>
</div>

<script>
    $("input").bind('keypress', function(e) {
        if (e.keyCode === 13) {
            $('.deposit-content-link').click();
        }
    });

    /*点击立即开通*/
    var allowSub = true;
    var t = 0;

    $(function () {
        $.get('/ctcf/user/get-name-and-card', function (data) {
            $('#real_name').val(data.real_name);
            $("#idcard").val(data.idCard)
        })
    })

    <?php
    /**
     * @var \common\models\user\OpenAccount $lastRecord
     */
    if(!is_null($lastRecord)) {
    ?>
    t = setInterval(function () {
        getIdentityRes(<?= $lastRecord->id?>);
    }, 1000);
    allowSub = false;
    $('#real_name').val('<?= $lastRecord->getName()?>');
    $('#idcard').val('<?= $lastRecord->getIdCard()?>');
    $('.deposit-content-link').html('开通中...');

    <?php
    }
    ?>

    $('.deposit-content-link').on('click', function () {
        var name = $('.name-text');
        var idcard = $('.identity-text');
        var nameisok = validate_name();
        var identityisok = validate_idcard();
        if (nameisok != false && identityisok != false) {
            if (!allowSub) {
                return;
            }

            allowSub = false;
            $('.deposit-content-link').html('开通中...');

            $.post('/user/identity/verify', {
                'User[real_name]': name.val(),
                'User[idcard]': idcard.val(),
                '_csrf': '<?= Yii::$app->request->csrfToken?>'
            }, function (data) {
                if (0 === data.code) {
                    if (data.id) {
                        t = setInterval(function () {
                            getIdentityRes(data.id);
                        }, 1000);
                    }
                } else {
                    //失败
                    if (data.tourl) {
                        location.href = data.tourl;
                    }

                    $('.identity .err-info').text(data.message);
                    $('.identity .err-info').show();

                    allowSub = true;
                    $('.deposit-content-link').html('立即开通');
                }
            });
        }
    });

    function getIdentityRes(id) {
        var xhr = $.get('/user/identity/res?id=' + id);
        xhr.done(function (data) {
            if (0 === data.code || 2 === data.code) {
                clearInterval(t);
                if (2 === data.code) {
                    $('.identity .err-info').text(data.message);
                    $('.identity .err-info').show();
                    allowSub = true;
                    $('.deposit-content-link').html('立即开通');
                } else {
                    window.location.href = data.tourl;
                }
            }
        })
    }
</script>