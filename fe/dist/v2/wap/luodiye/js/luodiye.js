function validateForm(){var t=$("#iphone").val();if(""===t)return"手机号不能为空";var e=/^0?1[3|4|5|6|7|8][0-9]\d{8}$/;if(!e.test(t))return"手机号格式错误";var n=$("#captchaform-captchacode").val();if(""===n)return"图形验证码不能为空";if(4!==n.length)return"图形验证码必须为4位字符";var i=$("#yanzhengma").val();if(""===i)return"短信验证码不能为空";if(6!==i.length)return"手机验证码必须为6位字符";var a=$("#pass").val();if(""===a)return"密码不能为空";if(a.length<6)return"密码长度最少6位";var e=/[a-zA-Z]/,r=/[0-9]/;return-1===a.indexOf(" ")&&e.test(a)&&r.test(a)?"checked"!==$("#xieyi").attr("checked")?"请查看网站服务协议":"":"请至少输入字母与数字组合"}function signup(){var t=$("#signup_form"),e=$.post(t.attr("action"),t.serialize());e.done(function(t){t.code&&("undefined"!=typeof t.tourl?toastCenter(t.message,function(){"undefined"!=typeof _paq?(_paq.push(["trackEvent","user","reg",null,null,function(){location.href=t.tourl}]),setTimeout(function(){location.href=t.tourl},1500)):location.href=t.tourl}):"undefined"!=typeof t.message&&(piwik(t.message),toastCenter(t.message,function(){$("#signup-btn").attr("disabled",!1)})))}),e.fail(function(){piwik("网络繁忙, 请稍后重试!"),$("#signup-btn").attr("disabled",!1)})}function piwik(t){t&&"undefined"!=typeof _paq&&_paq.push(["trackEvent","user","reg-error",t])}$(function(){function t(){0===i?(window.clearInterval(n),$("#yzm").removeAttr("disabled"),$("#yzm").removeClass("yzm-disabled"),$("#yzm").val("重新发送")):(i--,$("#yzm").val(i+"s后重发"))}function e(){i=a,$("#yzm").addClass("yzm-disabled"),$("#yzm").attr("disabled","true"),$("#yzm").val(i+"s后重发"),n=window.setInterval(t,1e3)}FastClick.attach(document.body),$(".field-captchaform-captchacode").css("float","left"),$(".content-picture").on("click",function(){$(this).parents(".fixed-box").hide().siblings(".fixed-float").hide()}),$("input.login-info").focus(function(){$(this).css("color","#000")}),$("input.login-info").blur(function(){$(this).css("color","")}),$("#signup_form").submit(function(t){t.preventDefault(),$("#signup-btn").attr("disabled",!0);var e=validateForm();return""!==e?(piwik(e),toastCenter(e,function(){$("#signup-btn").attr("disabled",!1)}),!1):void signup()}),$(".password img").on("click",function(){"password"===$("#pass").attr("type")?($("#pass").attr("type","text"),$(this).removeAttr("src","/images/eye-close.png"),$(this).attr({src:"/images/eye-open.png",alt:"eye-open"})):($("#pass").attr("type","password"),$(this).removeAttr("src","/images/eye-open.png"),$(this).attr({src:"/images/eye-close.png",alt:"eye-close"}))}),$("#xieyi").bind("click",function(){"checked"===$(this).attr("checked")?$(this).attr("checked",!1):$(this).attr("checked",!0)});var n,i,a=60;$("#yzm").on("click",function(){return msg="",""===$("#captchaform-captchacode").val()?msg="图形验证码不能为空":4!==$("#captchaform-captchacode").val().length&&(msg="图形验证码必须为4位字符"),""!==msg?(piwik(msg),toastCenter(msg),!1):void createSms("#iphone",1,"#captchaform-captchacode",function(){e()})})});