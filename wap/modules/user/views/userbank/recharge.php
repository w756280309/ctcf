<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title="充值";
$this->registerJsFile('/js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>
<link rel="stylesheet" href="/css/bind.css"/>
<link rel="stylesheet" href="/css/chongzhi.css"/>
<link rel="stylesheet" href="/css/base.css"/>

    <!--银行卡-->
        <div class="row bank-card">
            <div class="col-xs-2 bank-img"><img src="/images/bankicon/<?= $user_bank->bank_id ?>.png" alt=""/></div>
            <div class="col-xs-8 bank-content">
                <div class="bank-content1"><?= $user_bank->bank_name ?></div>
                <div class="bank-content2">尾号<?= $user_bank->card_number?substr($user_bank->card_number, -4):"" ?> 储蓄卡</div>
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
        <form method="post" class="cmxform" id="form" action="/user/userbank/recharge" data-to="1">
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <div class="row kahao">
                <div class="hidden-xs col-sm-1"></div>
                <div class="col-xs-3 col-sm-1">充值金额</div>
                <div class="col-xs-9 col-sm-8 safe-lf"><input type="text" id="fund"  name='RechargeRecord[fund]' placeholder="输入充值金额"/></div>
                <div class="hidden-xs col-sm-1"></div>
            </div>
            <input type="text" name="" style="display:none"/>
            <!--限额提醒-->
            <div class="row dan">
                <div class="col-xs-10">
                    <span>单笔5万元，单日5万</span>
                </div>
            </div>
            <!--提交按钮-->
            <div class="row">
                <div class="col-xs-3"></div>
                <div class="col-xs-6 login-sign-btn">
                    <input id="rechargebtn" class="btn-common btn-normal" name="signUp" type="button" value="立即充值" >
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
           if(err == '1') {
               toasturl(tourl,mess);
           }
            
           csrf = $("meta[name=csrf-token]").attr('content');
           $('#rechargebtn').bind('click',function(){
               $(this).addClass("btn-press").removeClass("btn-normal");
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
               subForm("#form");
               $(this).removeClass("btn-press").addClass("btn-normal");
           });

        })    
        </script>

    