<?php
$this->title="充值";
$this->registerJsFile('/js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>
<link rel="stylesheet" href="/css/bind.css"/>
<link rel="stylesheet" href="/css/chongzhi.css"/>
<link rel="stylesheet" href="/css/base.css"/>
<style type="text/css">
    .yzm-show {
        width: 100px !important;
        margin-top:-4px !important;
        font-size:12px !important;
        height:28px !important;
        line-height:27px !important;
    }
    .ryzm-disabled {
        background:#fff !important;
        border: 1px solid #f44336 !important;
    }
</style>
    <!--银行卡-->
        <div class="row bank-card">
            <div class="col-xs-2 bank-img"><img src="/images/bankicon/<?= $user_bank->bank_id ?>.png" alt=""/></div>
            <div class="col-xs-8 bank-content">
                <div class="bank-content1"><?= $user_bank->bank_name ?></div>
                <div class="bank-content2">
                    尾号<?= $user_bank->card_number?substr($user_bank->card_number, -4):"" ?> 储蓄卡
                    &emsp;银行预留手机号 <?= substr_replace(Yii::$app->user->identity->mobile,'****',3,-4); ?>
                </div>
            </div>
            <div class="col-xs-2"></div>
        </div>
        <!--可用金额-->
        <div class="row kahao">
            <div class="hidden-xs col-sm-1"></div>
            <div class="col-xs-3 col-sm-1">可用金额</div>
            <div class="col-xs-9 col-sm-8"><?= $user_acount->available_balance?$user_acount->available_balance:0 ?>元</div>
            <div class="hidden-xs col-sm-1"></div>
        </div>
        <!--充值金额-->
        <form method="post" class="cmxform" id="form" action="/user/qpay/qrecharge/verify" data-to="1">
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <input name="from" type="hidden" value="<?= Yii::$app->request->get('from'); ?>">
            <input id="qpay-recharge-sn" name="RechargeRecord[sn]" id="" type="hidden" />
            <div class="row kahao">
                <div class="hidden-xs col-sm-1"></div>
                <div class="col-xs-3 col-sm-1">充值金额</div>
                <div class="col-xs-9 col-sm-8 safe-lf"><input type="text" id="fund"  name='RechargeRecord[fund]' placeholder="输入充值金额"/></div>
                <div class="hidden-xs col-sm-1"></div>
            </div>
            <div class="row kahao">
                <div class="hidden-xs col-sm-1"></div>
                <div class="col-xs-3 col-sm-1">短信验证码</div>
                <div class="col-xs-9 col-sm-8 safe-lf" style="position: relative;"><input type="text" id="yzm"  name='yzm' placeholder="输入验证码" maxlength="6"/>
                <input class="yzm yzm-normal yzm-show" name="createsms" id="createsms" value="获取验证码" type="button" style="color: white !important"></div>
            </div>

            <input type="text" name="" style="display:none"/>
            <!--限额提醒-->
            <div class="row dan" hidden="hidden">
                <div class="col-xs-10">
                    <span>单笔5万元，单日5万</span>
                </div>
            </div>
            <!--提交按钮-->
            <div class="row">
                <div class="col-xs-3"></div>
                <div class="col-xs-6 login-sign-btn">
                    <input id="rechargebtn" class="btn-common btn-normal" type="button" value="立即充值" >
                </div>
                <div class="col-xs-3"></div>
            </div>
        </form>
        <script type="text/javascript">
        var csrf;
        function validateform(){
            if($('#fund').val()==''){
                toast(this,'充值金额不能为空');
                $(this).removeClass("btn-press").addClass("btn-normal");
                return false;
            }
            if($('#fund').val()==0){
                toast(this,'充值金额不能为零');
                $(this).removeClass("btn-press").addClass("btn-normal");
                return false;
            }
            return true;
        }
        $(function(){
           var err = '<?= $data['code'] ?>';
           var mess = '<?= $data['message'] ?>';
           var tourl = '<?= $data['tourl'] ?>';
           if(err === '1') {
               toasturl(tourl,mess);
           }

           csrf = $("meta[name=csrf-token]").attr('content');
           $('#rechargebtn').bind('click',function(){
               $(this).addClass("btn-press").removeClass("btn-normal");
               if(!validateform()){
                   return false;
               }
               subRecharge();
               $(this).removeClass("btn-press").addClass("btn-normal");
           });

           $('#createsms').on('click', function(e) {
                e.preventDefault();
                if(!validateform()){
                   return false;
               }
                var $form = $('#form');
                var xhr = $.post(
                    '/user/qpay/qrecharge/init',
                    $form.serialize()
                );

                xhr.done(function(data) {
                    $('#qpay-recharge-sn').val(data['rechargeSn']);
                    qpay_timedown();
                });

                xhr.fail(function(jqXHR) {
                    var errMsg = jqXHR.responseJSON && jqXHR.responseJSON.message
                        ? jqXHR.responseJSON.message
                        : '未知错误，请刷新重试或联系客服';

                    toast(null, errMsg);
                });

            });
        })

        function subRecharge(){
                if(!validateform()){
                   return false;
                }
                var $form = $('#form');
                $('#rechargebtn').attr('disabled', true);
                var xhr = $.post(
                    $form.attr('action'),
                    $form.serialize()
                );

                xhr.done(function(data) {
                    location.href=data['next']
                });

                xhr.fail(function(jqXHR) {
                    var errMsg = jqXHR.responseJSON && jqXHR.responseJSON.message
                        ? jqXHR.responseJSON.message
                        : '未知错误，请刷新重试或联系客服';

                    toast(null, errMsg);
                    $('#rechargebtn').attr('disabled', false);
                });
        }

        //60秒倒计时
        var InterValObj; //timer变量，控制时间
        var curCount;//当前剩余秒数
        var count = 60; //间隔函数，1秒执行
        function qpay_timedown() {
            curCount = count;
            $('#createsms').css({"cssText": "color: #f44336 !important"});
            $('#createsms').addClass("ryzm-disabled");
            $('#createsms').attr("disabled", "true");
            $('#createsms').val(curCount + "s后重发");
            InterValObj = window.setInterval(qpay_SetRemainTime, 1000); //启动计时器，1秒执行一次
        }

        function qpay_SetRemainTime() {
            if (curCount == 0) {
                window.clearInterval(InterValObj);//停止计时器
                $('#createsms').removeAttr("disabled");//启用按钮
                $('#createsms').removeClass("ryzm-disabled");
                $('#createsms').val("重新发送");
            } else {
                curCount--;
                $("#createsms").val(curCount + "s后重发");
            }
        };
        </script>


