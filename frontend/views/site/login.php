<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

frontend\assets\WapAsset::register($this);
$this->registerJsFile('/js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?><?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,inital-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" >
    <meta name="renderer" content="webkit">
	<title><?= Html::encode($this->title) ?></title>
        <?= Html::csrfMetaTags() ?>
	<?php $this->head() ?>
        </script><script src="/js/jquery.js"></script>
    <link rel="stylesheet" href="/css/base.css">
    <link rel="stylesheet" href="/css/loginsign.css">
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="container">
        <div class="row">
            <?php $form = ActiveForm::begin(['id' => 'login', 'action' => "/site/login",]); ?>
            <?=
            $form->field($model, 'phone', ['template' => '{input}{error}'])->textInput();
            ?>
               <div class="row sm-height">
                    <div class="col-xs-9 col">
                        <?=
                        $form->field($model, 'password', ['template' => '{input}{error}'])->textInput();
                        ?>
                    </div>
                </div>

                <?php if($is_flag) { ?>
                <div class="row sm-height border-bottom">
                    <div class="col-xs-9 col">
                        <input name="is_flag" type="hidden" value="<?= $is_flag ?>">
                        <input class="login-info" type="text" id="verifycode" placeholder="请输入验证码" name="LoginForm[verifyCode]" maxlength="6" >

                    </div>
                    <div class="col-xs-3 yz-code text-align-rg col" style="height:52px;background: #fff;" >
                        <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                                                        'template' => '{image}','captchaAction'=>'/site/captcha'
                                                        ]) ?>
                    </div>
                </div>
                <?php } ?>

                <div class="col-xs-3"></div>
                <div class="col-xs-6 login-sign-btn">
                    <input id="login-btn" class="btn-common btn-normal" name="start" type="submit" value="登录" >
                </div>
                <div class="col-xs-3"></div>

<!--            </form>-->
             <?php ActiveForm::end(); ?>
        </div>
    </div>
    <!-- 引入表单验证插件  -->
<!--    <script src="/js/is.js"></script>-->
    <script>
        var csrf;
        var is_flag = '<?= $is_flag?1:0 ?>';
        $(function(){
            $('.back img').bind('click',function(){
                history.go(-1);
            });
            $('input.login-info').focus(function () {
                $(this).css("color", "#000");
            });
            $('input.login-info').blur(function () {
                $(this).css("color", "");
                var loginInfo = $(this).val();
                if (loginInfo == '') {
                }
            });
        });
        $(".login-eye img").on("click",function (){
            if( $("#pass").attr("type") == "password"){
                $("#pass").attr("type","text");
                $(this).removeAttr("src","/images/eye-close.png");
                $(this).attr({ src: "/images/eye-open.png", alt: "eye-open" });
            } else {
                $("#pass").attr("type","password");
                $(this).removeAttr("src","/images/eye-open.png");
                $(this).attr({ src: "/images/eye-close.png", alt: "eye-close" });
            }
         });

    </script>
    <?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
