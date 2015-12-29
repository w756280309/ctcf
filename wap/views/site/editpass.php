<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
$this->title="修改登录密码";
$this->registerJsFile('/js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>
<link rel="stylesheet" href="/css/base.css">
<link rel="stylesheet" href="/css/setting.css">

<?php $form = ActiveForm::begin(['id'=>'editpassform', 'action' =>"/site/editpass" , 'options' => ['class' => 'cmxform']]); ?>
    <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
    <div class="row sm-height border-bottom">
            <div class="col-xs-3 safe-txt text-align-ct">原密码</div>
            <div class="col-xs-9 safe-lf text-align-lf">
                <input type="password" id="password" name="EditpassForm[password]" maxlength="20" placeholder="请输入原密码">
            </div>
        </div>
        <div class="row sm-height border-bottom">
            <div class="col-xs-3 safe-txt text-align-ct">新密码</div>
            <div class="col-xs-7 safe-lf text-align-lf">
                <input type="password" id="new_pass" placeholder="请输入6-20个字母与数字" name="EditpassForm[new_pass]" maxlength="20">
            </div>
            <div class="col-xs-2 eye text-align-ct col">
                <img src="/images/eye-close.png"  align="absmiddle" alt=" 闭眼" >
            </div>
        </div>
        <div class="row sm-height border-bottom">
            <div class="col-xs-3 safe-txt text-align-ct">验证码</div>
            <div class="col-xs-5 safe-lf" style="padding-right: 0;">
                <input type="text" id="sms" placeholder="请输入验证码" name="EditpassForm[verifyCode]" maxlength="6" >
            </div>
            <div class="col-xs-4 yz-code text-align-rg col">
                <?= $form->field($model, 'verifyCode',['inputOptions' => [ 'style' => 'height: 40px']])->widget(Captcha::className(), [
                                                    'template' => '{image}','captchaAction'=>'/site/captcha'
                                                    ]) ?>
            </div>
        </div>
        <div class="row login-sign-btn">
            <div class="col-xs-3"></div>
            <div class="col-xs-6 text-align-ct">
                <input id="editpassbtn" class="btn-common btn-normal" style="margin-top:40px;" type="button" value="确认重置">
            </div>
            <div class="col-xs-3"></div>
        </div>
        </form>

    </div>
    <!-- 遮罩层 start  -->
    <div class="mask"></div>
    <!-- 遮罩层 end  -->
    <!-- 绑定提示 start  -->
    <div class="bing-info hidden">
        <div class="bing-tishi">提示</div>
        <p class="tishi-p"> 密码修改成功，请重新登录</p>
        <div class="bind-btn">
            <span class="true">确定</span>
        </div>
    </div>
    <!-- 绑定提示 end  -->
    <!-- 修改登录密码页 end  -->
    <script type="text/javascript">
    var csrf;
    $(function(){       
       csrf = $("meta[name=csrf-token]").attr('content');
       $('#editpassbtn').bind('click',function(){
            var reg = /(?!^\d+$)(?!^[a-zA-Z]+$)^[0-9a-zA-Z]{6,20}$/;
            if (!reg.test($('#password').val()) || !reg.test($('#new_pass').val()) ) {
                toast(this, '新密码必须为数字和字母的组合');
                $("#signup-btn").removeClass("btn-press").addClass("btn-normal");
                return false;
           }
           if($('#password').val()=='' || $('#new_pass').val()==''){
               toast(this,'密码不能为空');
               return false;
           }
           if($('#password').val().length<6 || $('#password').val().length>20 || $('#new_pass').val().length<6 || $('#new_pass').val().length>20){
               toast(this,'密码必须是6-20个字母与数字');
               return false;
           }
           if($('#sms').val()==''){
               toast(this,'验证码不能为空');
               return false;
           }
           subForm("#editpassform");
       });

    });
    $(".eye img").on("click",function (){
        if( $("#new_pass").attr("type") == "password"){
            $("#new_pass").attr("type","text");
            $(this).removeAttr("src","/images/eye-close.png");
            $(this).attr({ src: "/images/eye-open.png", alt: "eye-open" });
        } else {
            $("#new_pass").attr("type","password");
            $(this).removeAttr("src","/images/eye-open.png");
            $(this).attr({ src: "/images/eye-close.png", alt: "eye-close" });
        }
    });

    $("#editpassform-verifycode-image").attr("height","40px");

</script>

    