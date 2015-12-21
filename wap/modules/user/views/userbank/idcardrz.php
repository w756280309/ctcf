<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title="实名认证";
$this->registerJsFile('/js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>
<link rel="stylesheet" href="/css/bind.css"/>
<link rel="stylesheet" href="/css/chongzhi.css"/>
<link rel="stylesheet" href="/css/base.css"/>
    
    <div style="height: 10px"></div>
    <!--交易密码-->
    <form method="post" class="cmxform" id="form" action="/user/userbank/idcardrz" data-to="1">
        <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <div class="row kahao">
            <div class="hidden-xs col-sm-1"></div>
            <div class="col-xs-3 col-sm-1">真实姓名</div>
            <div class="col-xs-9 col-sm-8"><input type="text" id="real_name" name='User[real_name]' placeholder="请输入您的真实姓名"/></div>
            <div class="hidden-xs col-sm-1"></div>
        </div>
        <div class="row kahao">
            <div class="hidden-xs col-sm-1"></div>
            <div class="col-xs-3 col-sm-1">身份证号</div>
            <div class="col-xs-9 col-sm-8"><input type="text" id="idcard" name='User[idcard]' placeholder="请输入您的身份证号"/></div>
            <div class="hidden-xs col-sm-1"></div>
        </div>
        <!--提交按钮-->
        <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-6 login-sign-btn">
                <input id="idcardbtn" class="btn-common btn-normal" name="signUp" type="button" value="下一步">
            </div>
            <div class="col-xs-3"></div>
        </div>
    </form>
    <!-- 卡号弹出框 start  -->
    <div class="error-info">您输入身份证少于18位</div>
    <!-- 开好弹出框 end  -->
    <script type="text/javascript">
    var csrf;
    $(function(){
       var err = '<?= $code ?>';
       var mess = '<?= $message ?>';
       var tourl = '<?= $tourl ?>';
       if(err == '1') {
           toasturl(tourl,mess);
       }
       
       csrf = $("meta[name=csrf-token]").attr('content');
       $('#idcardbtn').bind('click',function(){
           $(this).addClass("btn-press").removeClass("btn-normal");
           if($('#real_name').val()==''){
                    toast(this,'姓名不能为空');
                    $(this).removeClass("btn-press").addClass("btn-normal");
                    return false;
                }
                if($('#idcard').val()==''){
                    toast(this,'身份证不能为空');
                    $(this).removeClass("btn-press").addClass("btn-normal");
                    return false;
                }
                
//                if(!validateIdCard($('#idcard').val())){
//                    toast(this,'身份证号码错误');
//                    return false;
//                }
           subForm("#form");
           $(this).removeClass("btn-press").addClass("btn-normal");
       });

    })    
    </script>

    