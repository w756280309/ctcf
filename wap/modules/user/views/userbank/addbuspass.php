<?php
$this->title="设置交易密码";
$this->registerJsFile('/js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>
<link rel="stylesheet" href="/css/bind.css"/>
<link rel="stylesheet" href="/css/chongzhi.css"/>
<link rel="stylesheet" href="/css/base.css"/>

    <!--交易密码-->
    <div style="height: 10px"></div>
    <form method="post" class="cmxform" id="editpassform" action="/user/userbank/addbuspass" data-to="1">
        <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <div class="row kahao">
            <div class="hidden-xs col-sm-1"></div>
            <div class="col-xs-3 col-sm-3">交易密码</div>
            <div class="col-xs-9 col-sm-7"><input type="password" id="new_pass" name="EditpassForm[new_pass]" placeholder="请输入6位数字的交易密码" maxlength="6"/></div>
            <div class="hidden-xs col-sm-1"></div>
        </div>
        <div class="row kahao">
            <div class="hidden-xs col-sm-1"></div>
            <div class="col-xs-3 col-sm-3">再次确认</div>
            <div class="col-xs-9 col-sm-7"><input type="password" id="r_pass" name="EditpassForm[r_pass]" placeholder="请再次确认交易密码" maxlength="6"/></div>
            <div class="hidden-xs col-sm-1"></div>
        </div>
        <!--提交按钮-->
        <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-6 login-sign-btn">
                <input id="editpassbtn" class="btn-common btn-normal" name="signUp" type="button" value="提交" >
            </div>
            <div class="col-xs-3"></div>
        </div>
    </form>
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
       $('#editpassbtn').bind('click',function(){
           var reg = /^[0-9]{6,6}$/;
           if (!reg.test($('#new_pass').val()) || !reg.test($('#r_pass').val()) ) {
                toast(this, '交易密码必须为6位纯数字');
                $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
                return false;
           }
           if($('#new_pass').val()==''){
               toast(this,'交易密码不能为空');
               $(this).removeClass("btn-press").addClass("btn-normal");
               return false;
           }
           if($('#r_pass').val()==''){
               toast(this,'确认密码不能为空');
               $(this).removeClass("btn-press").addClass("btn-normal");
               return false;
           }
           if($('#new_pass').val() != $('#r_pass').val()){
               toast(this,'两次输入的密码不一致');
               $(this).removeClass("btn-press").addClass("btn-normal");
               return false;
           }
           $(this).addClass("btn-press").removeClass("btn-normal");
           subForm("#editpassform", "#editpassbtn");
           $(this).removeClass("btn-press").addClass("btn-normal");
       });

    })
    </script>

