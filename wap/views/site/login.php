<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use common\view\BaiduTongjiHelper;

BaiduTongjiHelper::registerTo($this, BaiduTongjiHelper::WAP_KEY);

frontend\assets\WapAsset::register($this);
$this->registerJsFile(ASSETS_BASE_URI . 'js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" >
    <meta name="renderer" content="webkit">
	<title><?= Html::encode($this->title) ?></title>
        <?= Html::csrfMetaTags() ?>
	<?php $this->head() ?>
    <link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/base.css">
    <link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/loginsign.css">
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="container">
        <div class="row nav-height">
            <div class="col-xs-2 back"><img src="<?= ASSETS_BASE_URI ?>images/back.png" alt="" /></div>
            <div class="col-xs-8 title">登录</div>
        </div>
        <div class="row">
            <?php $form = ActiveForm::begin(['id' => 'login', 'action' => "/site/login", 'options' => ['data-to'=>'1']]); ?>
            <input name="from" type="hidden" value="<?=$from ?>">
                <input id="iphone" class="login-info" name="LoginForm[phone]" maxlength="11" type="tel" placeholder="请输入手机号" AUTOCOMPLETE="off" >

                <div class="row sm-height">
                    <div class="col">
                        <input id="pass" class="login-info" name="LoginForm[password]" maxlength="20" type="password" placeholder="请输入密码" AUTOCOMPLETE="off" />
                    </div>
                </div>

                <?php if($is_flag) { ?>
                <div class="row sm-height border-bottom">
                    <div class="col-xs-8 col">
                        <input name="is_flag" type="hidden" value="<?= $is_flag ?>">
                        <input class="login-info" type="text" id="verifycode" placeholder="请输入验证码" name="LoginForm[verifyCode]" maxlength="4" >
                    </div>
                    <div class="col-xs-4 yz-code text-align-rg col" style="height:51px;background: #fff; overflow: hidden;" >
                        <?= $form->field($model, 'verifyCode', ['inputOptions' => ['style' => 'height: 40px']])
                                 ->label(false)->widget(Captcha::className(), [
                                     'template' => '{image}',
                                     'captchaAction' => '/site/captcha'
                        ]) ?>
                    </div>
                </div>
                <?php } ?>

                <div class="form-bottom">&nbsp;</div>
                <div class="col-xs-3"></div>
                <div class="col-xs-6 login-sign-btn">
                    <input id="login-btn" class="btn-common btn-normal" name="start" type="button" value="登录" >
                </div>
                <div class="col-xs-6 login-sign-btn reg_forget_area">
                    <a  href="/site/signup" align="center" >注册账号</a>
                    &emsp;
                    |
                    &emsp;
                    <a href="/site/resetpass" align="center" >忘记密码</a>
                </div>
                <div class="col-xs-3"></div>

<!--            </form>-->
             <?php ActiveForm::end(); ?>
        </div>
        <!-- 登录页 end  -->
        <!-- 输入弹出框 start  -->
        <div class="error-info">您输入的密码不正确</div>
        <!-- 输入弹出框 end  -->
    </div>
    <!-- 引入表单验证插件  -->
<!--    <script src="/js/is.js"></script>-->
    <script>
        var csrf;
        var is_flag = '<?= $is_flag?1:0 ?>';
        $(function(){
            csrf = $("meta[name=csrf-token]").attr('content');
            $('#login-btn').bind('click',function(){
                $(this).addClass("btn-press").removeClass("btn-normal");
                if($('#iphone').val()==''){
                    toast('手机号不能为空');
                    $(this).removeClass("btn-press").addClass("btn-normal");
                    return false;
                }
                if($('#pass').val()==''){
                    toast('密码不能为空');
                    $(this).removeClass("btn-press").addClass("btn-normal");
                    return false;
                }
                var tel = $('#iphone').val();
                var reg = /^0?1[3|4|5|6|8][0-9]\d{8}$/;
                if (!reg.test(tel)) {
                    toast('手机号格式错误');
                    $(this).removeClass("btn-press").addClass("btn-normal");
                    return false;
                }
                if ($('#pass').val().length<6) {
                    toast('密码长度最少6位');
                    $(this).removeClass("btn-press").addClass("btn-normal");
                    return false;
                }
                if (is_flag == 1) {
                    if ($('#verifycode').val() === '') {
                        toast('验证码不能为空');
                        $(this).removeClass("btn-press").addClass("btn-normal");
                        return false;
                    }
                    if ($('#verifycode').val().length !== 4) {
                        toast('验证码长度必须为4位');
                        $(this).removeClass("btn-press").addClass("btn-normal");
                        return false;
                    }
                }
                subForm("#login", "#login-btn");

                $(this).removeClass("btn-press").addClass("btn-normal");
            });
            $('.back img').bind('click',function(){
                history.go(-1);
            });
            $('input.login-info').focus(function () {
                $(this).css("color", "#000");
            });
            $('input.login-info').blur(function () {
                $(this).css("color", "");
            });
        });
    </script>
    <?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
