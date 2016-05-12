<?php
$this->title = '换卡申请';
$this->backUrl = '/user/userbank/mycard';
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/bind.css?v=20160406"/>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/replacecard.css?v=20160428"/>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/swiper.min.css"/>
<script src="<?= ASSETS_BASE_URI ?>js/swiper.min.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/bind.js"></script>

<div class="row tishi">
    <div class="col-xs-12">*绑定的银行卡必须为本人身份证办理</div>
</div>

<div class="mask"></div>
<div class="row">
    <div class="col-xs-2"></div>
    <div class="col-xs-8">
        <div class="banks"></div>
        <div class="banks1">
            <img src="<?= ASSETS_BASE_URI ?>images/mask.png" alt="" class="mask-bank"/>
            <div class="close"><img src="<?= ASSETS_BASE_URI ?>images/close.png" alt=""/></div>
            <div class="swiper-container">
                <div class="swiper-wrapper" id='bank'>
                    <?php foreach ($banklist as $bank): ?>
                    <div class="swiper-slide" data-id='<?= $bank->bankId ?>'>
                        <div>
                            <img src="<?= ASSETS_BASE_URI ?>images/bankicon/<?= $bank->bankId ?>.png" alt=""/>
                            <span><?= $bank->bank->bankName ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-2"></div>
</div>

<form method="post" class="cmxform" id="form" action="/user/qpay/bankcardupdate/init" data-to="1">
    <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
    <div class="row kahao">
        <div class="col-xs-3 xian">开户行</div>
        <div class="col-xs-7 xian">
            <input id="bank_id" type="hidden" name='BankCardUpdate[bankId]'/>
            <input id="bank_name" type="hidden" name='BankCardUpdate[bankName]' placeholder="请选择开户行"/>
            <div class="selecter kaihu">请选择开户行</div>
            <img class="selecter kaihu1"/>
            <span class="selecter kaihu1 kaihu2"></span>
        </div>
        <div class="col-xs-2 xian"><img class='you' src="<?= ASSETS_BASE_URI ?>images/jiantou.png" alt=""/></div>
    </div>
    <div class="row kahao">
        <div class="col-xs-3 xian">卡号</div>
        <div class="col-xs-9 xian"><input id="card_no" type="tel" name='BankCardUpdate[cardNo]' placeholder="请输入银行卡号" AUTOCOMPLETE="on"/></div>
    </div>
    <!--限额提醒-->
    <div class="row tixing form-bottom">
        <div class="col-xs-10">
            <div class="notice">?</div>
            <span><a href="/user/userbank/bankxiane">限额提醒</a></span>
        </div>
    </div>
    <div class="row replacecard-tixin">
        <div class="col-xs-12">
            <div>*换卡提醒：</div>
            <div>若您的账户余额为0，系统将在半小时以内自动审核换卡；</div>
            <div>若您的账户余额不为0且有回款金额，则需拨打客服热线<?= \Yii::$app->params['contact_tel'] ?>，提交相关资料，提交资料后，进行2-5天的人工审核换卡；</div>
            <div>换卡申请期间不影响充值和提现。</div>
        </div>
    </div>
    <!--提交按钮-->
    <div class="row">
        <div class="col-xs-3"></div>
        <div class="col-xs-6 login-sign-btn">
            <input id="bindbankbtn" class="btn-common btn-normal" name="signUp" type="submit" value="下一步" >
        </div>
        <div class="col-xs-3"></div>
    </div>
</form>
<script type="text/javascript">
    var csrf;
    $(function() {
        csrf = $("meta[name=csrf-token]").attr('content');
        $('#form').on('submit', function(e) {
            e.preventDefault();
            if (validate()) {
                if ($(this).data('submitting')) {
                    return;
                }
                $(this).data('submitting', true);
                var xhr = $.post(
                    $(this).attr("action"),
                    $(this).serialize()
                );

                xhr.done(function (data) {
                    if ('undefined' !== typeof data.message) {
                        toast(data.message);
                        return;
                    }

                    if ('undefined' !== typeof data.next) {
                        toast('转入联动优势进行换卡操作');
                        setTimeout(function () {
                            window.location.replace(data.next);
                        }, 1500);
                    }
                });

                xhr.fail(function (jqXHR) {
                    var errMsg = jqXHR.responseJSON && jqXHR.responseJSON.message
                            ? jqXHR.responseJSON.message
                            : '未知错误，请刷新重试或联系客服';

                    toast(errMsg);
                });

                xhr.always(function () {
                    $("#form").data('submitting', false);
                });
            }
        });

       $('#card_no').blur(function(){
            var card_no = $(this).val();
            if(card_no=='') {
                return false;
            }

            $.post("/user/userbank/checkbank", {card: card_no, _csrf:csrf}, function (result) {
                if(result.code!=0) {
                    alert(result.message);
                    return;
                }

                if(result['bank_id']!=''){
                    document.getElementById("bank_id").value = result['bank_id'];
                    document.getElementById("bank_name").value = result['bank_name'];
                }

                if(result['bank_name']==''){
                    return;
                }else{
                    $('.kaihu2').show();
                    $('.kaihu1').show();
                    $('.kaihu').hide();
                    $('.xian span')[0].innerHTML=result['bank_name'];
                    $('.xian img')[0].src='<?= ASSETS_BASE_URI ?>images/bankicon/'+result['bank_id']+'.png';
                }

            });
       });
    })

    function validate() {
        if ($.trim($('#card_no').val()) == '') {
            toast('银行卡号不能为空');
            return false;
        }
        var reg = /^[0-9]{16,19}$/;
        if (!reg.test($.trim($('#card_no').val()))) {
            toast('你输入的银行卡号有误');
            return false;
        }
        if ($('#bank_name').val() == '') {
            toast('开户行不能为空');
            return false;
        }
        return true;
    }
</script>