<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title="充值";
$this->registerJsFile('/js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>
<link rel="stylesheet" href="/css/bind.css"/>
<link rel="stylesheet" href="/css/chongzhi.css"/>
<link rel="stylesheet" href="/css/base.css"/>

        <!--充值金额-->
        <form method="post" class="cmxform" id="form" action="/user/userbank/rechargecheckpay" data-to="1">
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <input name="s" type="hidden" value="<?= Yii::$app->request->get('s'); ?>">
            <div class="row kahao">
                <div class="hidden-xs col-sm-1"></div>
                <div class="col-xs-3 col-sm-1">验证码</div>
                <div class="col-xs-9 col-sm-8 safe-lf"><input type="text" id="yzm"  name='yzm' placeholder="输入验证码"/></div>
                <div class="hidden-xs col-sm-1"></div>
            </div>
            <input type="text" name="" style="display:none"/>
            
            <!--提交按钮-->
            <div class="row">
                <div class="col-xs-3"></div>
                <div class="col-xs-6 login-sign-btn">
                    <input id="rechargepaybtn" class="btn-common btn-normal" name="signUp" type="button" value="提交验证" >
                </div>
                <div class="col-xs-3"></div>
            </div>
        </form>
        <script type="text/javascript">
        var csrf;
        $(function(){
           csrf = $("meta[name=csrf-token]").attr('content');
           $('#rechargepaybtn').bind('click',function(){
               if($('#yzm').val()==''){
                   toast(this,'验证码不能为空');
                   $(this).removeClass("btn-press").addClass("btn-normal");
                   return false;
               }
               subForm("#form");
               $(this).removeClass("btn-press").addClass("btn-normal");
           });

        })    
        </script>

    