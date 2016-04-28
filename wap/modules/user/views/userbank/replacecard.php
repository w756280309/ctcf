<?php
$this->title="换卡信息";
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/bind.css?v=20160406"/>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/swiper.min.css"/>
<script src="<?= ASSETS_BASE_URI ?>js/qpay.js?v=20160419001"></script>
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
                    <?php foreach($banklist as $bank): ?>
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

<form method="post" class="cmxform" id="form" action="/user/qpay/binding/verify" data-to="1">
    <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
    <div class="row kahao">
        <div class="col-xs-3 xian">卡号</div>
        <div class="col-xs-9 xian"><input id="card_no" type="tel" name='QpayBinding[card_number]' placeholder="请输入银行卡号" AUTOCOMPLETE="on"/></div>
    </div>

    <div class="row kahao">
        <div class="col-xs-3 xian">开户行</div>
        <div class="col-xs-7 xian">
            <input id="bank_id" type="hidden" name='QpayBinding[bank_id]'/>
            <input id="bank_name" type="hidden" name='QpayBinding[bank_name]' placeholder="请选择开户行"/>
            <div class="selecter kaihu">请选择开户行</div>
            <img class="selecter kaihu1"/>
            <span class="selecter kaihu1 kaihu2"></span>
        </div>
        <div class="col-xs-2 xian"><img class='you' src="<?= ASSETS_BASE_URI ?>images/jiantou.png" alt=""/></div>
    </div>

    <!--限额提醒-->
    <div class="row tixing form-bottom">
        <div class="col-xs-10">
            <div class="notice">?</div>
            <span><a href="/user/userbank/bankxiane">限额提醒</a></span>
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

    <input id="qpay-binding-sn" name="QpayBinding[binding_sn]" type="hidden" />
</form>
<!-- 卡号弹出框 start  -->
<div class="error-info">您输入的卡号有误</div>
<!-- 开好弹出框 end  -->
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
<script type="text/javascript">
    var csrf;
    $(function() {
        var err = '<?= $data['code'] ?>';
        var mess = '<?= $data['message'] ?>';
        var tourl = '<?= $data['tourl'] ?>';
        if (err === '1') {
            toast(mess, function() {
                if (tourl !== '') {
                    location.href = tourl;
                }
            });
            return;
        }

        csrf = $("meta[name=csrf-token]").attr('content');
        $('#form').on('submit', function(e) {
            e.preventDefault();
            if (validateBinding()) {
                qpay_showConfirmModal();
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
</script>
