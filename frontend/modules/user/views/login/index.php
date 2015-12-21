<?php
use yii\widgets\ActiveForm;
?>

<div class="body">
    <div class="login-left">
        <?php if(empty($index_adv['login_adv_arr'])||empty($index_adv['login_adv_arr']['adv'])){ ?>
        <img src="/images/lw_landing_1.jpg" width="647" height="320" alt="做到极致，把关每次风险" />
        <?php }else{        foreach($index_adv['login_adv_arr']['adv'] as $val){ ?>
        <a href="<?= $val['link'] ?>">
            <img src="<?= $val['image'] ?>" title="<?= $val['description'] ?>" alt="<?= $val['description'] ?>" width="<?=$index_adv['index_header']['width'] ?>" height="<?=$index_adv['index_header']['height'] ?>" />
        </a>
        <?php } } ?>
    </div>
    <div class="login-right">
        <?php $form = ActiveForm::begin(['id'=>'user_form', 'action' =>"/user/login" ]); ?>
        <div class="login-right-main">
            <h2>用户登录</h2>

            <?= $form->field($model, 'username',
                    [
                        'inputOptions'=>['class'=>'username','placeholder'=>'用户名','tabindex'=>'1'],
                        'template' => '<div class="username-line">{input}
                        <span class="tip  "></span></div>
                        <div class="tip-text  ">{error}</div>
                        '])->textInput();?>
            <?= $form->field($model, 'password',
                [
                    'inputOptions'=>['class'=>'username','placeholder'=>'密码','tabindex'=>'2'],
                    'template' => '<div class="password-line">{input}
                        <span class="tip  "></span></div>
                        <div class="tip-text  ">{error}</div>
                        '])->passwordInput();?>

        </div>
        <ul class="login-tab">
            <li class="li1"><input type="checkbox" name="rmbuser"  id="rmbuser"/> 记住用户</li>
            <li class="li2"></li>
        </ul>
        <input class="clear login-btn" type="submit" class="clear" value=" 登 录 "onclick="saveUserName()"/>
<!--        <input class="clear login-btn"  type="button" onclick="saveUserName()"  value="进入"/>-->
        <?php ActiveForm::end(); ?>
    </div>
    <div class="clear"></div>
</div>
<script type="text/javascript">
    //初始化页面时验证是否记住了用户
    $(document).ready(function() {
        if (GetCookie("rmbUser") == "true") {
            $("#rmbuser").attr("checked", true);
            $("#loginform-username").val(GetCookie("loginform-username"));
        }
    });

    //保存用户信息
    function saveUserName() {

        if ($("#rmbuser").is(':checked') ) {
            var userName = $("#loginform-username").val();
            var expdate = new Date();
            expdate.setTime(expdate.getTime() + 15 * (24 * 60 * 60 * 1000));
            SetCookie("rmbUser", "true", expdate);
            SetCookie("loginform-username", userName, expdate);
        }
        else {
            var expdate = new Date();
            SetCookie("rmbUser", null, expdate);
            SetCookie("loginform-username", null, expdate);

        }
        return true;
    }

    //取Cookie的值

    function GetCookie(name) {
        var arg = name + "=";
        var alen = arg.length;
        var clen = document.cookie.length;
        var i = 0;
        while (i < clen) {
            var j = i + alen;
            //alert(j);
            if (document.cookie.substring(i, j) == arg) return getCookieVal(j);
            i = document.cookie.indexOf(" ", i) + 1;
            if (i == 0) break;
        }
        return null;
    }

    function getCookieVal(offset) {
        var endstr = document.cookie.indexOf(";", offset);
        if (endstr == -1) endstr = document.cookie.length;
        return unescape(document.cookie.substring(offset, endstr));
    }
    //写入到Cookie

    function SetCookie(name, value, expires) {
        var argv = SetCookie.arguments;
        //本例中length = 3
        var argc = SetCookie.arguments.length;
        var expires = (argc > 2) ? argv[2] : null;
        var path = (argc > 3) ? argv[3] : null;
        var domain = (argc > 4) ? argv[4] : null;
        var secure = (argc > 5) ? argv[5] : false;
        document.cookie = name + "=" + escape(value) + ((expires == null) ? "" : ("; expires=" + expires.toGMTString())) + ((path == null) ? "" : ("; path=" + path)) + ((domain == null) ? "" : ("; domain=" + domain)) + ((secure == true) ? "; secure" : "");
    }

</script>