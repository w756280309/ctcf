<?php
$this->title="提现";
$this->registerJsFile('/js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>
<link rel="stylesheet" href="/css/bind.css"/>
<link rel="stylesheet" href="/css/chongzhi.css"/>
<link rel="stylesheet" href="/css/base.css"/>
<link rel="stylesheet" href="/css/tixian.css"/>

    <!--银行卡-->
    <div class="row bank-card" style="margin-bottom: 0!important;">
        <div class="col-xs-2 bank-img"><img src="/images/bankicon/<?= $user_bank->bank_id ?>.png" alt=""/></div>
        <div class="col-xs-7 bank-content">
            <div class="bank-content1"><?= $user_bank->bank_name ?></div>
            <div class="bank-content2">尾号<?= $user_bank->card_number?substr($user_bank->card_number, -4):"" ?> 储蓄卡</div>
        </div>
        <div class="col-xs-3 bank-content">
            <div class="bank-content1" style="text-align: right;padding-right: 15px">户名</div>
            <div class="bank-content2" style="text-align: right;padding-right: 15px"><?= $user_bank->account ?></div>
        </div>
    </div>
    <!--可提现金额-->
    <div class="row tixian">
        <div class="hidden-xs col-sm-1"></div>
        <div class="col-xs-4 col-sm-2">可提现金额：</div>
        <div class="col-xs-8 col-sm-6"><?= $user_acount->available_balance?$user_acount->available_balance:0 ?>元</div>
        <div class="hidden-xs col-sm-3"></div>
    </div>

    <!--提现金额-->
    <form method="post" class="cmxform" id="form" action="/user/userbank/tixian" data-to="1">
        <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <input name="flag" type="hidden" value="checktrade">
        <div class="row kahao">
            <div class="hidden-xs col-sm-1"></div>
            <div class="col-xs-3 col-sm-1">提现金额</div>
            <div class="col-xs-7 col-sm-8"><input id="money" type="text" name="DrawRecord[money]" placeholder="输入提现金额"/></div>
            <div class="col-xs-1 col-sm-1">元</div>
            <div class="hidden-xs col-sm-1"></div>
        </div>
        <input type="text" name="" style="display:none"/>
        <!--限额提醒-->
        <div class="row dan" style="padding-top: 1px;">
                    <div class="hidden-xs col-sm-1"></div>
                    <div class="col-xs-12 col-sm-10" style="padding: 0">
                        <span>(每笔提现扣除2元手续费,最低提现金额10元)</span>
                    </div>
                    <div class="hidden-xs col-sm-1"></div>
                </div>
        <!--提交按钮-->
        <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-6 login-sign-btn">
                <input id="tixianbtn" class="btn-common btn-normal" type="button" value="提现">
            </div>
            <div class="col-xs-3"></div>
        </div>
    </form>
    <script type="text/javascript">
        var csrf;
        $(function(){
           var err = '<?= $data['code'] ?>';
           var mess = '<?= $data['message'] ?>';
           var tourl = '<?= $data['tourl'] ?>';
           if(err === '1') {
               toasturl(tourl,mess);
           }

           csrf = $("meta[name=csrf-token]").attr('content');
           $('#tixianbtn').bind('click', function() {
               $(this).addClass("btn-press").removeClass("btn-normal");
               if ($.trim($('#money').val()) == '') {
                   toast(this,'提现金额不能为空');
                   $(this).removeClass("btn-press").addClass("btn-normal");
                   return false;
               }
               var reg = /^[0-9]+([.]{1}[0-9]{1,2})?$/;
               if (!reg.test($('#money').val())) {
                   toast(this,'提现金额格式不正确');
                   $(this).removeClass("btn-press").addClass("btn-normal");
                   return false;
               }
               if ($('#money').val() ==0 ) {
                   toast(this,'提现金额不能为零');
                   $(this).removeClass("btn-press").addClass("btn-normal");
                   return false;
               }
               subForm("#form", "#tixianbtn", function(data) {
                   if (data.money != undefined) {
                      $('#money').val(data.money)
                   }
               });
               $(this).removeClass("btn-press").addClass("btn-normal");
           });

        })
     </script>
